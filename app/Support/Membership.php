<?php

namespace App\Support;

/**
 * Membership
 *
 * Menghitung tier membership & progress poin user secara stateless
 * berdasarkan kolom `total_points` di tabel users (tidak query tambahan).
 *
 * Ambang batas poin per tier bisa diubah di $tiers tanpa migrasi baru.
 */
class Membership
{
    /**
     * Ambang batas poin minimum tiap tier, urut menaik.
     *
     * @var array<string, int>
     */
    private const TIERS = [
        'regular'  => 0,
        'silver'   => 500,
        'gold'     => 1000,
        'platinum' => 2000,
    ];

    /**
     * Hitung info progress membership berdasarkan total poin user.
     *
     * @return array{
     *     current: string,
     *     next: string|null,
     *     current_min: int,
     *     next_min: int|null,
     *     points_needed: int,
     *     percent: int
     * }
     */
    public static function progress(int $totalPoints): array
    {
        $tiers = self::TIERS;
        $keys  = array_keys($tiers);

        // Tentukan tier saat ini: tier tertinggi yang sudah tercapai.
        $currentIndex = 0;
        foreach ($tiers as $index => $minPoints) {
            if ($totalPoints >= $minPoints) {
                $currentIndex = array_search($index, $keys);
            }
        }

        $current    = $keys[$currentIndex];
        $next       = $keys[$currentIndex + 1] ?? null;
        $currentMin = $tiers[$current];
        $nextMin    = $next ? $tiers[$next] : null;

        $percent = $nextMin
            ? (int) round((($totalPoints - $currentMin) / ($nextMin - $currentMin)) * 100)
            : 100;

        return [
            'current'       => $current,
            'next'          => $next,
            'current_min'   => $currentMin,
            'next_min'      => $nextMin,
            'points_needed' => $nextMin ? max(0, $nextMin - $totalPoints) : 0,
            'percent'       => min(100, max(0, $percent)),
        ];
    }
}