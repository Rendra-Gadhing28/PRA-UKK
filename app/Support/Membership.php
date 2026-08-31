<?php

namespace App\Support;

/**
 * Membership Helper
 *
 * Menghitung tier membership & progress poin user secara stateless
 * dengan visual metadata per-tier (diskon, warna, benefit).
 */
class Membership
{
    /**
     * Definisi lengkap tier membership, urut dari terendah sampai tertinggi (paling tinggi PURPLE VIP).
     *
     * @var array<string, array<string, mixed>>
     */
    public const TIERS = [
        'regular' => [
            'key'          => 'regular',
            'name'         => 'Regular',
            'label'        => 'Regular Member',
            'min_points'   => 0,
            'discount'     => '0%',
            'discount_val' => 0,
            'color'        => 'rose',
            'gem_gradient' => 'from-rose-200 via-pink-400 to-rose-600',
            'gem_glow'     => 'rgba(244, 63, 94, 0.65)',
            'style_bg'     => 'background: linear-gradient(135deg, #380816 0%, #681028 45%, #911739 80%, #500a1d 100%);',
            'border_style' => 'border-color: rgba(255, 210, 225, 0.45);',
            'badge_cls'    => 'bg-rose-950/80 text-rose-200 border-rose-500/50',
            'card_class'   => 'text-white',
            'benefits'     => ['Akses Booking Online', 'Misi Check-in Harian', 'Diskon Layanan 0%'],
        ],
        'silver' => [
            'key'          => 'silver',
            'name'         => 'Silver',
            'label'        => 'Silver Member',
            'min_points'   => 500,
            'discount'     => '5%',
            'discount_val' => 5,
            'color'        => 'silver',
            'gem_gradient' => 'from-slate-100 via-gray-300 to-slate-400',
            'gem_glow'     => 'rgba(203, 213, 225, 0.75)',
            'style_bg'     => 'background: linear-gradient(135deg, #0f172a 0%, #1f2937 40%, #4b5563 75%, #374151 100%);',
            'border_style' => 'border-color: rgba(209, 213, 219, 0.6);',
            'badge_cls'    => 'bg-gray-900/90 text-slate-200 border-slate-400/60',
            'card_class'   => 'text-white',
            'benefits'     => ['Diskon 5% Semua Treatment', 'Point Multiplier 1.2x', 'Gratis Konsultasi Beauty'],
        ],
        'gold' => [
            'key'          => 'gold',
            'name'         => 'Gold',
            'label'        => 'Gold Member',
            'min_points'   => 1000,
            'discount'     => '10%',
            'discount_val' => 10,
            'color'        => 'gold',
            'gem_gradient' => 'from-amber-100 via-yellow-400 to-amber-600',
            'gem_glow'     => 'rgba(245, 158, 11, 0.65)',
            'style_bg'     => 'background: linear-gradient(135deg, #451a03 0%, #78350f 40%, #d97706 75%, #b45309 100%);',
            'border_style' => 'border-color: rgba(253, 224, 71, 0.55);',
            'badge_cls'    => 'bg-amber-950/80 text-amber-200 border-amber-500/50',
            'card_class'   => 'text-white',
            'benefits'     => ['Diskon 10% Semua Treatment', 'Point Multiplier 1.5x', 'Prioritas Antrean Booking'],
        ],
        'purple' => [
            'key'          => 'purple',
            'name'         => 'Royal Purple',
            'label'        => 'Royal Purple VIP',
            'min_points'   => 2000,
            'discount'     => '15%',
            'discount_val' => 15,
            'color'        => 'purple',
            'gem_gradient' => 'from-fuchsia-300 via-purple-500 to-pink-600',
            'gem_glow'     => 'rgba(217, 70, 239, 0.7)',
            'style_bg'     => 'background: linear-gradient(135deg, #2e1065 0%, #581c87 40%, #a21caf 75%, #701a75 100%);',
            'border_style' => 'border-color: rgba(232, 121, 249, 0.65);',
            'badge_cls'    => 'bg-purple-950/90 text-purple-200 border-purple-400/60',
            'card_class'   => 'text-white',
            'benefits'     => ['Diskon 15% Semua Treatment', 'Point Multiplier 2.0x', 'Akses Private VIP Lounge', 'Free Birthday Gift Special'],
        ],
    ];

    /**
     * Hitung info progress membership berdasarkan total poin user.
     *
     * @return array{
     *     current: string,
     *     current_meta: array<string, mixed>,
     *     next: string|null,
     *     next_meta: array<string, mixed>|null,
     *     current_min: int,
     *     next_min: int|null,
     *     points_needed: int,
     *     percent: int,
     *     all_tiers: array<string, array<string, mixed>>
     * }
     */
    public static function progress(int $points): array
    {
        $tiers = self::TIERS;
        $keys  = array_keys($tiers);

        $currentIndex = 0;
        foreach ($tiers as $key => $meta) {
            if ($points >= $meta['min_points']) {
                $currentIndex = array_search($key, $keys);
            }
        }

        $currentKey = $keys[$currentIndex];
        $nextKey    = $keys[$currentIndex + 1] ?? null;

        $currentMeta = $tiers[$currentKey];
        $nextMeta    = $nextKey ? $tiers[$nextKey] : null;

        $currentMin = $currentMeta['min_points'];
        $nextMin    = $nextMeta ? $nextMeta['min_points'] : null;

        $percent = $nextMin
            ? (int) round((($points - $currentMin) / ($nextMin - $currentMin)) * 100)
            : 100;

        return [
            'current'       => $currentKey,
            'current_meta'  => $currentMeta,
            'next'          => $nextKey,
            'next_meta'     => $nextMeta,
            'current_min'   => $currentMin,
            'next_min'      => $nextMin,
            'points_needed' => $nextMin ? max(0, $nextMin - $points) : 0,
            'percent'       => min(100, max(0, $percent)),
            'all_tiers'     => $tiers,
        ];
    }
}