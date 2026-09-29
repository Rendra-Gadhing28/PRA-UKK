<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BookingsExport;
use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Beauticians;
use App\Models\Bookings;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class AdminBookingController extends Controller
{
    private const VERSION_KEY = 'admin.bookings:version';

    /**
     * Tampilkan daftar booking dengan filter tanggal, status, beautician, keyword pencarian, dan tab arsip.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'active');
        $query = Bookings::with(['user', 'beautician', 'treatments']);

        if ($tab === 'archived') {
            $query->archived();
        } else {
            $query->active();
        }

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

        $activeCount = Bookings::active()->count();
        $archivedCount = Bookings::archived()->count();
        $beauticians = Beauticians::orderBy('name')->get();

        return view('admin.bookings.index', compact('bookings', 'beauticians', 'tab', 'activeCount', 'archivedCount'));
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

        if (! empty($validated['beautician_id'])) {
            $booking->beautician_id = $validated['beautician_id'];
        }

        if ($newStatus === 'canceled') {
            $booking->canceled_at = now();
            $booking->cancel_reason = $validated['cancel_reason'] ?? 'Dibatalkan oleh admin';
        }

        if ($newStatus === 'completed') {
            $booking->is_archived = true;
            $booking->archived_at = now();
            if ($booking->payment_status !== 'paid') {
                $booking->payment_status = 'paid';
                $booking->payment_verified_at = now();
                $booking->payment_verified_by = auth()->id();
            }
        }
        // Simpan perubahan status booking terlebih dahulu
        $booking->save();

        // Jika status menjadi completed dan poin belum ditambahkan, akumulasi poin
        if ($newStatus === 'completed' && ! $booking->points_added) {
            $totalPoints = $booking->calculateEarnedPoints();
            if ($totalPoints > 0 && $booking->user) {
                $booking->user->addPoints($totalPoints);
            }
            $booking->points_added = true;
            $booking->save();
        }

        $this->bumpBookingCache();

        ActivityLogger::log('update', "Mengubah status reservasi #{$booking->booking_code} dari {$oldStatus} ke {$newStatus} (Otomatis Diarsipkan).", $booking, [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'cancel_reason' => $booking->cancel_reason,
            'is_archived' => $booking->is_archived,
        ]);

        $msg = $newStatus === 'completed'
            ? "Status reservasi #{$booking->booking_code} selesai dan otomatis masuk ke arsip."
            : "Status reservasi #{$booking->booking_code} berhasil diubah dari {$oldStatus} ke {$newStatus}.";

        ToastHelper::success($msg);

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

        if (! $booking->points_added) {
            $totalPoints = $booking->calculateEarnedPoints();
            if ($totalPoints > 0 && $booking->user) {
                $booking->user->addPoints($totalPoints);
            }
            $booking->update(['points_added' => true]);
        }

        $this->bumpBookingCache();

        ActivityLogger::log('verify_payment', "Memverifikasi pembayaran reservasi #{$booking->booking_code} (Status: Lunas).", $booking, [
            'total_amount' => $booking->total_amount,
            'payment_method' => $booking->payment_method,
        ]);

        $successMsg = $isDpPaid
            ? 'Pelunasan tunai sisa Rp '.number_format((float) $booking->remaining_amount, 0, ',', '.')." untuk reservasi #{$booking->booking_code} berhasil dicatat (Lunas)!"
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

        ActivityLogger::log('export', 'Mengekspor laporan booking ke PDF.');

        $pdf = Pdf::loadView('admin.reports.bookings_pdf', compact('bookings', 'request'));

        return $pdf->download('Laporan_Booking_Yalia_Beauty_'.now()->format('Ymd_His').'.pdf');
    }

    /**
     * Export laporan daftar booking ke Excel (XLSX).
     */
    public function exportExcel(Request $request)
    {
        $query = Bookings::with(['user', 'beautician', 'treatments']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('booking_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('booking_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('booking_date', '<=', $request->end_date);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('beautician_id') && $request->beautician_id !== 'all') {
            $query->where('beautician_id', $request->beautician_id);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->get();
        $fileName = 'Laporan_Booking_Yalia_Beauty_'.now()->format('Ymd_His').'.xlsx';

        ActivityLogger::log('export', 'Mengekspor laporan booking ke Excel.');

        return Excel::download(new BookingsExport($bookings, $request->all()), $fileName);
    }

    /**
     * Kirim balasan admin terhadap ulasan reservasi customer dan setujui ulasan.
     */
    public function replyReview(Request $request, Bookings $booking)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:1000',
        ]);

        $review = $booking->review;
        if (! $review) {
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

        ActivityLogger::log('update', "Admin membalas ulasan reservasi #{$booking->booking_code}.", $review);

        return back()->with('success', 'Balasan ulasan berhasil dikirim.');
    }

    /**
     * Arsipkan data booking yang sudah selesai atau dibatalkan.
     */
    public function archive(Bookings $booking)
    {
        $booking->archive();

        $this->bumpBookingCache();

        ActivityLogger::log('archive', "Mengarsipkan riwayat reservasi #{$booking->booking_code}.", $booking);

        ToastHelper::success("Reservasi #{$booking->booking_code} berhasil diarsipkan.");

        return back();
    }

    /**
     * Pulihkan data booking dari arsip ke daftar aktif.
     */
    public function unarchive(Bookings $booking)
    {
        $booking->unarchive();

        $this->bumpBookingCache();

        ActivityLogger::log('unarchive', "Membatalkan arsip reservasi #{$booking->booking_code} ke daftar aktif.", $booking);

        ToastHelper::success("Reservasi #{$booking->booking_code} berhasil dipindahkan ke reservasi aktif.");

        return back();
    }

    /**
     * Hapus reservasi dari sistem (Soft Delete ke Tong Sampah).
     */
    public function destroy(Bookings $booking)
    {
        $code = $booking->booking_code;
        $booking->delete();

        $this->bumpBookingCache();

        ActivityLogger::log('delete', "Memindahkan reservasi #{$code} ke tong sampah (soft delete).", $booking);

        ToastHelper::success("Reservasi #{$code} berhasil dipindahkan ke tong sampah.");

        return redirect()->route('admin.bookings.index');
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
