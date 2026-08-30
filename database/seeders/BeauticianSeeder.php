<?php

namespace Database\Seeders;

use App\Models\Beauticians;
use App\Models\BeauticiansSchedules;
use Illuminate\Database\Seeder;

class BeauticianSeeder extends Seeder
{
    /**
     * Konvensi day_of_week mengikuti Carbon: 0 = Minggu, 1 = Senin, ..., 6 = Sabtu.
     */
    public function run(): void
    {
        $beauticians = [
            [
                'name' => 'Yuliawati',
                'phone' => '082227023362',
                'specialties' => ['Perawatan Kuku', 'Perawatan Wajah'],
                'off_day' => 0, // libur Minggu
            ]
        ];

        foreach ($beauticians as $b) {
            $beautician = Beauticians::create([
                'name' => $b['name'],
                'phone' => $b['phone'],
                'email' => strtolower('reghayuli@gmail.com'),
                'photo' => null,
                'bio' => "Beautician berpengalaman dengan spesialisasi {$b['specialties'][0]}.",
                'total_bookings' => 0,
                'service_area' => json_encode(['Salatiga', 'Bawen', 'Boyolali']),
                'is_active' => true,
            ]);

            // Buat jadwal kerja Senin(1) - Minggu(0), jam 08:00 - 20:00, kecuali hari libur
            for ($day = 0; $day <= 6; $day++) {
                BeauticiansSchedules::create([
                    'beautician_id' => $beautician->id,
                    'day_of_week' => $day,
                    'start_time' => '08:00:00',
                    'end_time' => '20:00:00',
                    'is_working' => $day !== $b['off_day'],
                ]);
            }
        }
    }
}