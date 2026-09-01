<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyUserSeeder extends Seeder
{
    /**
     * Run the database seeds for 50 dummy customer users.
     */
    public function run(): void
    {
        $indonesianNames = [
            'Siti Nurhaliza', 'Anisa Rahma', 'Dewi Sartika', 'Bunga Citra', 'Rina Nose',
            'Nia Ramadhani', 'Dian Sastrowardoyo', 'Chelsea Islan', 'Raisa Andriana', 'Isyana Sarasvati',
            'Maudy Ayunda', 'Prilly Latuconsina', 'Amanda Manopo', 'Nagita Slavina', 'Aurel Hermansyah',
            'Ashanty Siddik', 'Paula Verhoeven', 'Gisella Anastasia', 'Luna Maya', 'Mikha Tambayong',
            'Natasha Wilona', 'Jessica Mila', 'Felicya Angelista', 'Sandra Dewi', 'Titi Kamal',
            'Laura Basuki', 'Tatjana Saphira', 'Pevita Pearce', 'Cinta Laura', 'Anya Geraldine',
            'Rachel Vennya', 'Fujianti Utami', 'Marion Jola', 'Ziva Magnolya', 'Tiara Andini',
            'Lyodra Ginting', 'Mahalini Raharja', 'Keisya Levronka', 'Novia Bachmid', 'Brisia Jodie',
            'Yura Yunita', 'Nadin Amizah', 'Danilla Riyadi', 'Eva Celia', 'Sherina Munaf',
            'Gita Gutawa', 'Tasya Kamila', 'Yuki Kato', 'Enzy Storia', 'Febby Rastanty'
        ];

        $cities = [
            'Jl. Pandanaran No. 12, Ampel, Boyolali',
            'Jl. Pemuda No. 45, Boyolali Kota',
            'Jl. Ahmad Yani No. 88, Kartasura, Sukoharjo',
            'Jl. Slamet Riyadi No. 102, Surakarta',
            'Jl. Veteran No. 34, Pasar Kliwon, Solo',
            'Jl. Diponegoro No. 56, Salatiga',
            'Jl. Raya Semarang-Solo Km 15, Banyumanik, Semarang',
            'Jl. Pahlawan No. 78, Mojosongo, Boyolali',
            'Jl. Suharso No. 23, Jaten, Karanganyar',
            'Jl. Mayor Kusmanto No. 90, Klaten Utara'
        ];

        $tiers = ['regular', 'silver', 'gold', 'purple'];

        $passwordHash = Hash::make('password123');

        foreach ($indonesianNames as $index => $name) {
            $num = $index + 1;
            $slug = Str::slug($name);
            $email = "{$slug}{$num}@gmail.com";
            $phone = '08' . rand(11, 99) . rand(1000, 9999) . rand(100, 999);
            $address = $cities[array_rand($cities)];
            
            // Random tier & points allocation
            $tier = $tiers[array_rand($tiers)];
            $points = match($tier) {
                'purple' => rand(600, 1000),
                'gold' => rand(300, 599),
                'silver' => rand(100, 299),
                default => rand(0, 99),
            };

            $totalBookings = rand(1, 15);
            $totalSpending = $totalBookings * rand(75000, 350000);

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'password' => $passwordHash,
                    'email_verified_at' => now(),
                    'address' => $address,
                    'latitude' => -7.5300000 + (rand(-500, 500) / 10000),
                    'longitude' => 110.5900000 + (rand(-500, 500) / 10000),
                    'membership_level' => $tier,
                    'total_points' => $points,
                    'tier_points' => $points,
                    'total_bookings' => $totalBookings,
                    'total_spending' => $totalSpending,
                    'is_active' => true,
                    'role' => 'user',
                ]
            );
        }
    }
}
