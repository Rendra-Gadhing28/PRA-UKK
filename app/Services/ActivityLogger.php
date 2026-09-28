<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Beauticians;
use App\Models\Expense;
use App\Models\Reviews;
use App\Models\Treatments;
use App\Models\User;
use App\Models\Vouchers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Catat aktivitas ke tabel activity_logs.
     */
    public static function log(
        string $action,
        string $description,
        ?Model $subject = null,
        array $properties = [],
        ?User $user = null
    ): ?ActivityLog {
        try {
            $userId = $user ? $user->id : Auth::id();

            return ActivityLog::create([
                'user_id' => $userId,
                'action' => $action,
                'subject_type' => $subject ? class_basename($subject) : null,
                'subject_id' => $subject ? $subject->getKey() : null,
                'description' => $description,
                'properties' => ! empty($properties) ? $properties : null,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'is_archived' => false,
            ]);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    /**
     * Otomatis mengarsipkan activity log yang sudah berumur lebih dari 30 hari.
     */
    public static function archiveExpiredLogs(): int
    {
        return ActivityLog::expired()->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);
    }

    /**
     * Otomatis membersihkan (force delete) data soft delete di seluruh model yang berumur lebih dari 30 hari.
     */
    public static function purgeExpiredTrash(): array
    {
        $cutoff = now()->subDays(30);
        $purged = [];

        $models = [
            'users' => User::class,
            'treatments' => Treatments::class,
            'beauticians' => Beauticians::class,
            'vouchers' => Vouchers::class,
            'reviews' => Reviews::class,
            'expenses' => Expense::class,
        ];

        foreach ($models as $key => $modelClass) {
            $count = 0;
            $items = $modelClass::onlyTrashed()->where('deleted_at', '<=', $cutoff)->get();

            foreach ($items as $item) {
                $item->forceDelete();
                $count++;
            }

            $purged[$key] = $count;
        }

        return $purged;
    }

    /**
     * Simulasi: Majukan umur activity log sebanyak 30 hari ke belakang.
     */
    public static function simulateSkipLogs30Days(?int $logId = null): int
    {
        $query = ActivityLog::query();

        if ($logId) {
            $query->where('id', $logId);
        }

        $logs = $query->get();
        $count = 0;

        foreach ($logs as $log) {
            $log->timestamps = false;
            $log->created_at = $log->created_at->subDays(31);
            $log->save();
            $count++;
        }

        // Jalankan auto archive untuk log yang kini telah expired
        self::archiveExpiredLogs();

        return $count;
    }

    /**
     * Simulasi: Majukan umur soft delete sebanyak 30 hari ke belakang di tong sampah.
     */
    public static function simulateSkipTrash30Days(?string $type = null, ?int $id = null): int
    {
        $models = [
            'users' => User::class,
            'treatments' => Treatments::class,
            'beauticians' => Beauticians::class,
            'vouchers' => Vouchers::class,
            'reviews' => Reviews::class,
            'expenses' => Expense::class,
        ];

        $totalShifted = 0;

        $targetModels = $type && isset($models[$type])
            ? [$type => $models[$type]]
            : $models;

        foreach ($targetModels as $modelClass) {
            $query = $modelClass::onlyTrashed();

            if ($id) {
                $query->where('id', $id);
            }

            $items = $query->get();

            foreach ($items as $item) {
                $item->timestamps = false;
                $item->deleted_at = $item->deleted_at ? $item->deleted_at->subDays(31) : now()->subDays(31);
                $item->saveQuietly();
                $totalShifted++;
            }
        }

        return $totalShifted;
    }
}
