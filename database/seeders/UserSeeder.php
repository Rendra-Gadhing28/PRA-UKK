<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'=> 'Yalia Beauty',
            'email'=> 'reghayuli@gmail.com',
            'phone'=> '082227023362',
            'email_verified_at'=> now(),
            'password'=> Hash::make('yaliabeauty123'),
            'avatar'=> null,
            'avatar_url'=> null,
            'address'=> 'Jl. Raya Ampel, Asrimulyo, Area Sawah/Kebun, Candi, Kec. Ampel, Kabupaten Boyolali, Jawa Tengah 57352',
            'latitude' => null,
            'longitude' => null,
            'membership_level'=> 'Platinum',
            'total_points'=> '0',
            'total_bookings'=> '0',
            'total_spending'=> '0',
            'is_active'=> true,
            'role'=> 'admin',
        ]);
    }
}
