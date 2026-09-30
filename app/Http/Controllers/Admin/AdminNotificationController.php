<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Notifications;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    /**
     * Tampilkan daftar notifikasi dengan filter tab (Aktif vs Arsip), pencarian, dan rentang tanggal.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'active');
        $query = Notifications::with(['booking.user', 'booking.treatments', 'booking.beautician'])->latest();

        if ($tab === 'archived') {
            $query->archived();
        } elseif ($tab === 'active') {
            $query->active();
        }

        // Filter pencarian (kode booking, customer, judul, atau isi pesan)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('data->booking_code', 'like', "%{$search}%")
                    ->orWhere('data->customer_name', 'like', "%{$search}%")
                    ->orWhere('data->title', 'like', "%{$search}%")
                    ->orWhere('data->message', 'like', "%{$search}%")
                    ->orWhereHas('booking', function ($bq) use ($search) {
                        $bq->where('booking_code', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($uq) use ($search) {
                                $uq->where('name', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%");
                            });
                    });
            });
        }

        // Filter rentang tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $notifications = $query->paginate(12)->withQueryString();

        $activeCount = Notifications::active()->count();
        $archivedCount = Notifications::archived()->count();
        $unreadCount = Notifications::unread()->count();
        $readActiveCount = Notifications::active()->read()->count();

        return view('admin.notifications.index', compact(
            'notifications',
            'tab',
            'activeCount',
            'archivedCount',
            'unreadCount',
            'readActiveCount'
        ));
    }

    /**
     * Konfirmasi reservasi booking langsung dari notifikasi (Auto-Confirmed & Auto-Arsip Notifikasi).
     */
    public function confirm(Notifications $notification)
    {
        $booking = $notification->booking;

        if ($booking) {
            $currStatus = is_object($booking->status) ? $booking->status->value : (string) $booking->status;

            if (in_array($currStatus, ['completed', 'canceled', 'cancelled'], true)) {
                ToastHelper::error("Reservasi #{$booking->booking_code} sudah berada pada status {$currStatus} dan tidak dapat dikonfirmasi.");
                $notification->archive();

                return back();
            }

            $booking->status = 'confirmed';
            $booking->save();

            // Auto-arsip seluruh notifikasi terkait booking ini
            Notifications::where('booking_id', $booking->id)->update([
                'is_archived' => true,
                'archived_at' => now(),
                'read_at' => now(),
            ]);

            AdminDashboardController::bumpDashboardCache();

            ActivityLogger::log('update', "Mengonfirmasi reservasi #{$booking->booking_code} dari {$currStatus} ke confirmed via notifikasi (Otomatis Diarsipkan).", $booking, [
                'notification_id' => $notification->id,
                'booking_code' => $booking->booking_code,
                'old_status' => $currStatus,
                'new_status' => 'confirmed',
                'auto_archived' => true,
            ]);

            ToastHelper::success("Reservasi #{$booking->booking_code} berhasil dikonfirmasi! Notifikasi otomatis dipindahkan ke arsip.");
        } else {
            $notification->archive();
            ToastHelper::info("Data reservasi tidak ditemukan. Notifikasi telah diarsipkan.");
        }

        return back();
    }

    /**
     * Batalkan reservasi booking langsung dari notifikasi (Auto-Canceled & Auto-Arsip Notifikasi).
     */
    public function cancel(Request $request, Notifications $notification)
    {
        $validated = $request->validate([
            'cancel_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $booking = $notification->booking;

        if ($booking) {
            $currStatus = is_object($booking->status) ? $booking->status->value : (string) $booking->status;

            if (in_array($currStatus, ['completed', 'canceled', 'cancelled'], true)) {
                ToastHelper::error("Reservasi #{$booking->booking_code} sudah berstatus {$currStatus}.");
                $notification->archive();

                return back();
            }

            $reason = $validated['cancel_reason'] ?? 'Dibatalkan oleh admin melalui notifikasi';

            $booking->status = 'canceled';
            $booking->cancel_reason = $reason;
            $booking->canceled_at = now();
            $booking->save();

            // Auto-arsip seluruh notifikasi terkait booking ini
            Notifications::where('booking_id', $booking->id)->update([
                'is_archived' => true,
                'archived_at' => now(),
                'read_at' => now(),
            ]);

            AdminDashboardController::bumpDashboardCache();

            ActivityLogger::log('update', "Membatalkan reservasi #{$booking->booking_code} via notifikasi (Alasan: {$reason}) (Otomatis Diarsipkan).", $booking, [
                'notification_id' => $notification->id,
                'booking_code' => $booking->booking_code,
                'cancel_reason' => $reason,
                'auto_archived' => true,
            ]);

            ToastHelper::success("Reservasi #{$booking->booking_code} berhasil dibatalkan! Notifikasi otomatis dipindahkan ke arsip.");
        } else {
            $notification->archive();
            ToastHelper::info("Data reservasi tidak ditemukan. Notifikasi telah diarsipkan.");
        }

        return back();
    }

    /**
     * Arsipkan satu notifikasi secara manual.
     */
    public function archive(Notifications $notification)
    {
        $notification->archive();

        ActivityLogger::log('archive', "Mengarsipkan notifikasi #{$notification->id}.", $notification);

        ToastHelper::success("Notifikasi berhasil diarsipkan.");

        return back();
    }

    /**
     * Kembalikan satu notifikasi dari arsip ke aktif.
     */
    public function unarchive(Notifications $notification)
    {
        $notification->unarchive();

        ActivityLogger::log('unarchive', "Mengembalikan notifikasi #{$notification->id} ke notifikasi aktif.", $notification);

        ToastHelper::success("Notifikasi berhasil dipindahkan ke notifikasi aktif.");

        return back();
    }

    /**
     * Hapus satu notifikasi (Soft delete ke Tong Sampah).
     */
    public function destroy(Notifications $notification)
    {
        $id = $notification->id;
        $notification->delete();

        ActivityLogger::log('delete', "Memindahkan notifikasi #{$id} ke tong sampah (soft delete).", $notification);

        ToastHelper::success("Notifikasi berhasil dipindahkan ke tong sampah.");

        return back();
    }

    /**
     * Arsipkan notifikasi terpilih secara massal (Bulk Archive).
     */
    public function bulkArchive(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            ToastHelper::error("Pilih setidaknya satu notifikasi untuk diarsipkan.");

            return back();
        }

        Notifications::whereIn('id', $ids)->update([
            'is_archived' => true,
            'archived_at' => now(),
            'read_at' => now(),
        ]);

        ActivityLogger::log('archive', "Mengarsipkan massal ".count($ids)." notifikasi.");

        ToastHelper::success(count($ids)." notifikasi berhasil diarsipkan.");

        return back();
    }

    /**
     * Hapus notifikasi terpilih secara massal (Bulk Soft Delete ke Tong Sampah).
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            ToastHelper::error("Pilih setidaknya satu notifikasi untuk dihapus.");

            return back();
        }

        Notifications::whereIn('id', $ids)->delete();

        ActivityLogger::log('delete', "Memindahkan massal ".count($ids)." notifikasi ke tong sampah (soft delete).");

        ToastHelper::success(count($ids)." notifikasi berhasil dipindahkan ke tong sampah.");

        return back();
    }

    /**
     * Hapus notifikasi secara menyeluruh (Mass Soft Delete ke Tong Sampah).
     */
    public function clearAll(Request $request)
    {
        $tab = $request->input('tab', 'all');

        $query = Notifications::query();
        if ($tab === 'active') {
            $query->active();
        } elseif ($tab === 'archived') {
            $query->archived();
        }

        $count = $query->count();

        if ($count === 0) {
            ToastHelper::info("Tidak ada notifikasi untuk dihapus.");

            return back();
        }

        $query->delete();

        ActivityLogger::log('delete', "Menghapus secara menyeluruh {$count} notifikasi ke tong sampah (Kategori: {$tab}).");

        ToastHelper::success("Sebanyak {$count} notifikasi berhasil dipindahkan ke tong sampah.");

        return back();
    }

    /**
     * Arsipkan seluruh notifikasi aktif yang sudah dibaca.
     */
    public function archiveAllRead()
    {
        $count = Notifications::active()
            ->read()
            ->update([
                'is_archived' => true,
                'archived_at' => now(),
            ]);

        if ($count > 0) {
            ActivityLogger::log('archive', "Mengarsipkan seluruh notifikasi yang sudah dibaca ({$count} notifikasi).");
            ToastHelper::success("Sebanyak {$count} notifikasi yang sudah dibaca berhasil dipindahkan ke arsip.");
        } else {
            ToastHelper::info("Tidak ada notifikasi aktif yang berstatus sudah dibaca untuk diarsipkan.");
        }

        return back();
    }

    /**
     * Tandai seluruh notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        $count = Notifications::unread()->count();
        Notifications::unread()->update(['read_at' => now()]);

        ActivityLogger::log('update', "Menandai seluruh notifikasi ({$count} belum dibaca) sebagai telah dibaca.");

        ToastHelper::success("Seluruh notifikasi ({$count}) berhasil ditandai sebagai dibaca.");

        return back();
    }
}
