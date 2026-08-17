<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Exceptions\OutOfServiceAreaException;

/**
 * Menghitung jarak antara salon dan lokasi user (rumus Haversine, garis
 * lurus) serta tarif ongkir Home Service berdasarkan jarak tersebut.
 */
class DistanceCalculatorService
{
    private const EARTH_RADIUS_KM = 6371.0;

    /**
     * Hitung jarak garis lurus (km) antara dua koordinat memakai rumus
     * Haversine.
     */
    public function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos($lat1Rad) * cos($lat2Rad) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Jarak dari salon (koordinat di config('booking.salon')) ke lokasi user.
     *
     * @throws OutOfServiceAreaException bila melebihi radius layanan.
     */
    public function distanceFromSalonKm(float $userLat, float $userLng): float
    {
        $salon = config('booking.salon');

        $distance = $this->haversineKm(
            (float) $salon['latitude'],
            (float) $salon['longitude'],
            $userLat,
            $userLng,
        );

        $maxRadius = (float) config('booking.service_radius_km');

        if ($distance > $maxRadius) {
            throw new OutOfServiceAreaException(
                "Lokasi berada {$this->round1($distance)} km dari salon, di luar jangkauan layanan Home Service (maks {$maxRadius} km)."
            );
        }

        return $distance;
    }

    /**
     * Hitung tarif ongkir dari jarak (km).
     *
     * 1 km pertama flat, sisanya per km, dibulatkan ke atas per kelipatan
     * round_up_step_km supaya tarif tidak "kemahalan" untuk jarak dekat
     * dan tetap wajar untuk jarak jauh.
     */
    public function calculateTransportFee(float $distanceKm): int
    {
        $config = config('booking.transport_fee');
        $firstKmFlat = (int) $config['first_km_flat'];
        $perKmAfter = (int) $config['per_km_after'];
        $step = (float) $config['round_up_step_km'];

        if ($distanceKm <= 1.0) {
            return $firstKmFlat;
        }

        $remainingKm = $distanceKm - 1.0;
        $roundedRemainingKm = ceil($remainingKm / $step) * $step;

        return $firstKmFlat + (int) round($roundedRemainingKm * $perKmAfter);
    }

    private function round1(float $value): float
    {
        return round($value, 1);
    }
}
