<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class AdminActivityLogController extends Controller
{
    /**
     * Tampilkan daftar activity log dengan filtering & pagination.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user'])->latest();

        // Filter status tab: active (default), archived, all
        $tab = $request->get('tab', 'active');
        if ($tab === 'active') {
            $query->active();
        } elseif ($tab === 'archived') {
            $query->archived();
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter aksi
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter modul / subject_type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter rentang tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(15)->withQueryString();

        // Hitung total untuk badge tab
        $activeCount = ActivityLog::active()->count();
        $archivedCount = ActivityLog::archived()->count();
        $expiredCount = ActivityLog::expired()->count();

        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $modules = ActivityLog::select('subject_type')->whereNotNull('subject_type')->distinct()->pluck('subject_type');

        return view('admin.activity_logs.index', compact(
            'logs',
            'tab',
            'activeCount',
            'archivedCount',
            'expiredCount',
            'actions',
            'modules'
        ));
    }

    /**
     * Arsipkan satu activity log secara manual.
     */
    public function archive(ActivityLog $log)
    {
        $log->archive();
        ToastHelper::success('Log aktivitas berhasil diarsipkan.');

        return back();
    }

    /**
     * Batalkan arsip satu activity log.
     */
    public function unarchive(ActivityLog $log)
    {
        $log->unarchive();
        ToastHelper::success('Log aktivitas berhasil dipindahkan ke aktif.');

        return back();
    }

    /**
     * Arsipkan activity log yang dipilih secara massal (bulk).
     */
    public function bulkArchive(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            ToastHelper::error('Pilih setidaknya satu log untuk diarsipkan.');

            return back();
        }

        ActivityLog::whereIn('id', $ids)->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        ToastHelper::success(count($ids).' log aktivitas berhasil diarsipkan.');

        return back();
    }

    /**
     * Jalankan proses pengarsipan log yang sudah melewati batas 30 hari.
     */
    public function archiveExpired()
    {
        $count = ActivityLogger::archiveExpiredLogs();

        if ($count > 0) {
            ToastHelper::success("Sebanyak {$count} log aktivitas berumur >30 hari berhasil diarsipkan.");
        } else {
            ToastHelper::info('Tidak ada log aktivitas aktif yang berumur lebih dari 30 hari.');
        }

        return back();
    }

    /**
     * Fitur pengujian: Simulasi skip 30 hari pada log untuk menguji mekanisme retensi & auto-archive.
     */
    public function simulateSkip30Days(Request $request)
    {
        $logId = $request->input('log_id');
        $count = ActivityLogger::simulateSkipLogs30Days($logId);

        ToastHelper::success("Simulasi Skip 30 Hari berhasil! {$count} log telah dimajukan umurnya dan log >30 hari otomatis diarsipkan.");

        return back();
    }
}
