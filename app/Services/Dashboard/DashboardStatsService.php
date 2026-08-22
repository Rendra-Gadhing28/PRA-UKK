<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Models\Bookings;
use Illuminate\Support\Facades\Cache;

/**
 * Menghitung statistik ringkas untuk halaman Dashboard user.
 *
 * Diekstrak dari DashboardController agar controller tetap tipis
 * (thin controller, fat service). Logika & perilaku PERSIS sama dengan
 * sebelumnya (cache key, TTL, dan query agregat tidak diubah) — cuma
 * dipindah lokasinya supaya bisa diuji/dipakai ulang terpisah dari HTTP
 * layer (misal nanti dibutuhkan endpoint API mobile).
 */
class DashboardStatsService
{
    private const CACHE_TTL_SECONDS = 60;

    /**
     * @return array{total_bookings:int, upcoming_count:int, total_spending:float}
     */
    public function forUser(int $userId): array
    {
        return Cache::remember(
            "user:{$userId}:dashboard-stats",
            now()->addSeconds(self::CACHE_TTL_SECONDS),
            fn () => $this->buildStats($userId)
        );
    }

    /**
     * Hitung statistik ringan pakai satu query agregat (bukan 3 query
     * terpisah untuk total_bookings / upcoming_count / total_spending).
     *
     * @return array{total_bookings:int, upcoming_count:int, total_spending:float}
     */
    private function buildStats(int $userId): array
    {
        $row = Bookings::query()
            ->ownedBy($userId)
            ->selectRaw("
                COUNT(*) as total_bookings,
                SUM(CASE WHEN status IN ('pending','confirmed','in_progress') THEN 1 ELSE 0 END) as upcoming_count,
                SUM(CASE WHEN payment_status = 'paid' OR status = 'completed' THEN total_amount ELSE 0 END) as total_spending
            ")
            ->first();

        return [
            'total_bookings' => (int) ($row->total_bookings ?? 0),
            'upcoming_count' => (int) ($row->upcoming_count ?? 0),
            'total_spending' => (float) ($row->total_spending ?? 0),
        ];
    }
}