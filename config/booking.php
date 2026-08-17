<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Titik Asal Salon
    |--------------------------------------------------------------------------
    |
    | Dipakai sebagai titik awal perhitungan jarak (Haversine) untuk booking
    | Home Service. Isi dengan koordinat lokasi salon Anda di .env.
    |
    */
    'salon' => [
        'latitude' => (float) env('SALON_LATITUDE', -6.9666667),
        'longitude' => (float) env('SALON_LONGITUDE', 110.4166667),
    ],

    /*
    |--------------------------------------------------------------------------
    | Radius Layanan
    |--------------------------------------------------------------------------
    |
    | Jarak maksimum (km) yang dilayani untuk Home Service. Di luar ini,
    | booking Home Service ditolak.
    |
    */
    'service_radius_km' => (float) env('BOOKING_SERVICE_RADIUS_KM', 30),

    /*
    |--------------------------------------------------------------------------
    | Tarif Ongkir (Transport Fee)
    |--------------------------------------------------------------------------
    |
    | 1 km pertama flat, sisanya dihitung per km dan dibulatkan ke atas
    | per kelipatan round_up_step_km supaya tidak "kemahalan" untuk jarak dekat.
    |
    */
    'transport_fee' => [
        'first_km_flat' => (int) env('BOOKING_TRANSPORT_FIRST_KM_FLAT', 5000),
        'per_km_after' => (int) env('BOOKING_TRANSPORT_PER_KM_AFTER', 3000),
        'round_up_step_km' => 0.5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Batas Waktu Pembayaran
    |--------------------------------------------------------------------------
    */
    'payment_expiry_minutes' => (int) env('BOOKING_PAYMENT_EXPIRY_MINUTES', 15),

    /*
    |--------------------------------------------------------------------------
    | Google Maps
    |--------------------------------------------------------------------------
    */
    'google_maps_key' => env('GOOGLE_MAPS_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans (Sandbox)
    |--------------------------------------------------------------------------
    */
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => (bool) env('MIDTRANS_IS_PRODUCTION', false),
    ],

];
