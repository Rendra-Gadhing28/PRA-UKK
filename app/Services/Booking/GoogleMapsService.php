<?php

declare(strict_types=1);

namespace App\Services\Booking;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk interaksi ringan dengan Google Maps API (mis. reverse
 * geocoding dari koordinat GPS ke nama jalan / alamat manusiawi).
 */
class GoogleMapsService
{
    /**
     * Dapatkan nama alamat dari latitude + longitude via Geocoding API.
     * Mengembalikan null bila API key belum diset atau request gagal,
     * sehingga caller bisa fallback ke alamat manual/user profile.
     */
    public function reverseGeocode(float $latitude, float $longitude): ?string
    {
        $apiKey = config('booking.google_maps_key');

        if (blank($apiKey)) {
            return null;
        }

        try {
            $response = Http::timeout(5)
                ->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => "{$latitude},{$longitude}",
                    'key' => $apiKey,
                    'language' => 'id',
                ]);

            if (! $response->successful()) {
                Log::warning('GoogleMapsService: Reverse geocode HTTP failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $response->json();

            if (($data['status'] ?? null) !== 'OK' || empty($data['results'])) {
                return null;
            }

            return $data['results'][0]['formatted_address'] ?? null;
        } catch (\Throwable $e) {
            Log::error('GoogleMapsService: Exception saat reverse geocode', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
