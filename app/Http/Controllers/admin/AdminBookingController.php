<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Beauticians;
use App\Models\Bookings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    /**
     * Tampilkan daftar booking dengan filter tanggal, status, beautician, dan keyword pencarian.
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

        $oldStatus = $booking->status;
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

        $booking->save();

        ToastHelper::success("Status reservasi #{$booking->booking_code} berhasil diubah dari {$oldStatus} ke {$newStatus}.");

        return redirect()->back();
    }

    /**
     * Verifikasi pembayaran manual atau transfer/QRIS.
     */
    public function verifyPayment(Request $request, Bookings $booking)
    {
        $booking->update([
            'payment_status' => 'paid',
            'payment_verified_at' => now(),
            'payment_verified_by' => auth()->id(),
            'status' => $booking->status === 'pending' ? 'confirmed' : $booking->status,
        ]);

        ToastHelper::success("Pembayaran untuk reservasi #{$booking->booking_code} berhasil diverifikasi!");

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

        $fileName = 'Laporan_Booking_Yalia_Beauty_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Booking', 'Pelanggan', 'No. HP', 'Beautician', 'Treatment', 'Tanggal Booking', 'Waktu', 'Tipe', 'Total Harga', 'Status Pembayaran', 'Status Booking']);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_code,
                    $b->user?->name ?? 'Guest',
                    $b->user?->phone ?? '-',
                    $b->beautician?->name ?? 'Auto Assign',
                    $b->treatments->pluck('name')->join(', ') ?: 'N/A',
                    $b->booking_date ? $b->booking_date->format('Y-m-d') : '-',
                    ($b->time_start ?? '') . ' - ' . ($b->time_end ?? ''),
                    $b->booking_type,
                    $b->total_amount,
                    $b->payment_status,
                    $b->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
