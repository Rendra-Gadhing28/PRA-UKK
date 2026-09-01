<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Beauticians;
use App\Models\Bookings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminBookingController extends Controller
{
    private const VERSION_KEY = 'admin.bookings:version';

    /**
     * Tampilkan daftar booking dengan filter tanggal, status, beautician, dan keyword pencarian (Cached).
     */
    public function index(Request $request)
    {
        $query = Bookings::with(['user', 'beautician', 'treatments']);

        // Filter: Tanggal Mulai & Tanggal Akhir
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }

        // Filter: Status Booking
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter: Status Pembayaran
        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter: Beautician
        if ($request->filled('beautician_id') && $request->beautician_id !== 'all') {
            $query->where('beautician_id', $request->beautician_id);
        }

        // Filter: Search Keyword (Kode Booking atau Nama Pelanggan)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->orderBy('booking_date', 'desc')
            ->orderBy('time_start', 'desc')
            ->paginate(10)
            ->withQueryString();

        $beauticians = Beauticians::orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings', 'beauticians'));
    }

    /**
     * Detail lengkap reservasi & beautician bertugas.
     */
    public function show(Bookings $booking)
    {
        $booking->load(['user', 'beautician', 'treatments', 'bookingTreatments.Treatments', 'review']);
        $beauticians = Beauticians::where('is_active', true)->orderBy('name')->get();

        return view('admin.bookings.show', compact('booking', 'beauticians'));
    }

    /**
     * Update status reservasi (confirm, in_progress, complete, cancel).
     */
    public function updateStatus(Request $request, Bookings $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,in_progress,completed,canceled'],
            'beautician_id' => ['nullable', 'exists:beauticians,id'],
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = is_object($booking->status) ? $booking->status->value : (string) $booking->status;

        if (in_array($oldStatus, ['completed', 'canceled', 'cancelled'], true)) {
            ToastHelper::error('Reservasi yang sudah Selesai atau Dibatalkan tidak dapat diubah lagi statusnya.');
            return redirect()->back();
        }

        $newStatus = $validated['status'];

        $booking->status = $newStatus;

        if (!empty($validated['beautician_id'])) {
            $booking->beautician_id = $validated['beautician_id'];
        }

        if ($newStatus === 'canceled') {
            $booking->canceled_at = now();
            $booking->cancel_reason = $validated['cancel_reason'] ?? 'Dibatalkan oleh admin';
        }

        if ($newStatus === 'completed' && $booking->payment_status !== 'paid') {
            $booking->payment_status = 'paid';
            $booking->payment_verified_at = now();
            $booking->payment_verified_by = auth()->id();
        }
        // Simpan perubahan status booking terlebih dahulu
        $booking->save();

        // Jika status menjadi completed dan poin belum ditambahkan, akumulasi poin
        if ($newStatus === 'completed' && !$booking->points_added) {
            $totalPoints = $booking->calculateEarnedPoints();
            if ($totalPoints > 0 && $booking->user) {
                $booking->user->addPoints($totalPoints);
            }
            $booking->points_added = true;
            $booking->save();
        }

        $this->bumpBookingCache();

        ToastHelper::success("Status reservasi #{$booking->booking_code} berhasil diubah dari {$oldStatus} ke {$newStatus}.");

        return redirect()->back();
    }

    /**
     * Verifikasi pembayaran manual atau transfer/QRIS.
     */
    public function verifyPayment(Request $request, Bookings $booking)
    {
        $currStatus = is_object($booking->status) ? $booking->status->value : (string) $booking->status;
        $isDpPaid = $booking->payment_status === 'dp_paid';

        $booking->update([
            'payment_status' => 'paid',
            'payment_verified_at' => now(),
            'payment_verified_by' => auth()->id(),
            'status' => $currStatus === 'pending' ? 'confirmed' : $booking->status,
        ]);

        if (!$booking->points_added) {
            $totalPoints = $booking->calculateEarnedPoints();
            if ($totalPoints > 0 && $booking->user) {
                $booking->user->addPoints($totalPoints);
            }
            $booking->update(['points_added' => true]);
        }

        $this->bumpBookingCache();

        $successMsg = $isDpPaid
            ? "Pelunasan tunai sisa Rp " . number_format((float)$booking->remaining_amount, 0, ',', '.') . " untuk reservasi #{$booking->booking_code} berhasil dicatat (Lunas)!"
            : "Pembayaran untuk reservasi #{$booking->booking_code} berhasil diverifikasi!";

        ToastHelper::success($successMsg);

        return redirect()->back();
    }

    /**
     * Tampilkan Struk Pembayaran Resmi Salon (Printable Receipt).
     */
    public function receipt(Bookings $booking)
    {
        $booking->load(['user', 'beautician', 'treatments', 'bookingTreatments.Treatments']);

        return view('admin.bookings.receipt', compact('booking'));
    }

    /**
     * Export laporan daftar booking ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = Bookings::with(['user', 'beautician', 'treatments']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('beautician_id') && $request->beautician_id !== 'all') {
            $query->where('beautician_id', $request->beautician_id);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();

        $pdf = Pdf::loadView('admin.reports.bookings_pdf', compact('bookings', 'request'));

        return $pdf->download('Laporan_Booking_Yalia_Beauty_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Export laporan daftar booking ke Excel (CSV).
     */
    public function exportExcel(Request $request)
    {
        $query = Bookings::with(['user', 'beautician', 'treatments']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        }
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('beautician_id') && $request->beautician_id !== 'all') {
            $query->where('beautician_id', $request->beautician_id);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();
        $totalAmount = $bookings->sum('total_amount');
        $fileName = 'Laporan_Booking_Yalia_Beauty_' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($bookings, $totalAmount) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk MS Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // === HEADER ATAS LAPORAN ===
            fputcsv($file, ['LAPORAN DAFTAR RESERVASI BOOKING - YALIA BEAUTY SALON']);
            fputcsv($file, ['Alamat Salon: GHV9+F2 Candi, Kabupaten Boyolali, Jawa Tengah | WA: 0822-2702-3362']);
            fputcsv($file, ['Tanggal Diunduh:', now()->translatedFormat('l, d F Y H:i') . ' WIB']);
            fputcsv($file, ['Ringkasan:', 'Total Data: ' . $bookings->count() . ' Reservasi', 'Nilai Total: Rp ' . number_format($totalAmount, 0, ',', '.')]);
            fputcsv($file, []); // Baris Kosong Pemisah

            // === TABEL DATA & FIELD AKURAT API/DATABASE ===
            fputcsv($file, [
                'No',
                'Kode Booking',
                'Nama Pelanggan',
                'No. Handphone',
                'Terapis / Beautician',
                'Layanan Treatment',
                'Tanggal Booking',
                'Waktu Layanan',
                'Tipe Kunjungan',
                'Total Harga (Rp)',
                'Status Pembayaran',
                'Status Booking'
            ]);

            $no = 1;
            foreach ($bookings as $b) {
                $statusText = is_object($b->status) 
                    ? (method_exists($b->status, 'badgeLabel') ? $b->status->badgeLabel() : $b->status->value) 
                    : (string) $b->status;

                fputcsv($file, [
                    $no++,
                    $b->booking_code,
                    $b->user?->name ?? 'Guest',
                    $b->user?->phone ?? '-',
                    $b->beautician?->name ?? 'Auto Assign',
                    $b->treatments->pluck('name')->join(', ') ?: 'N/A',
                    $b->booking_date ? $b->booking_date->format('Y-m-d') : '-',
                    ($b->time_start ?? '') . ' - ' . ($b->time_end ?? ''),
                    $b->booking_type === 'home' ? 'Home Service' : 'Ke Salon',
                    $b->total_amount,
                    $b->payment_status ? ucfirst($b->payment_status) : 'Lunas',
                    $statusText,
                ]);
            }

            // === BARIS TOTAL SUMMARY ===
            fputcsv($file, []); // Baris Kosong
            fputcsv($file, ['', '', '', '', '', '', '', '', 'TOTAL NILAI RESERVASI', $totalAmount, '', '']);

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function replyReview(Request $request, Bookings $booking)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000'
        ]);

        $review = $booking->review;
        if (!$review) {
            return back()->with('error', 'Ulasan tidak ditemukan.');
        }

        \DB::table('reviews')
            ->where('id', $review->id)
            ->update([
                'admin_reply' => $request->admin_reply,
                'is_approved' => true,
                'updated_at' => now(),
            ]);

        $this->bumpBookingCache();

        return back()->with('success', 'Balasan ulasan berhasil dikirim.');
    }

    /**
     * Bump versi cache booking agar query & detail lama otomatis ter-invalidasi.
     */
    private function bumpBookingCache(): void
    {
        Cache::forever(self::VERSION_KEY, $this->currentVersion() + 1);
        AdminDashboardController::bumpDashboardCache();
    }

    /**
     * Ambil versi cache booking saat ini.
     */
    private function currentVersion(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }
}
