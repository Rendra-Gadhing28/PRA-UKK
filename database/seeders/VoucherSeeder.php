<?php

namespace Database\Seeders;

use App\Models\Vouchers;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'WELCOME10',
                'name' => 'Diskon Member Baru',
                'description' => 'Diskon 10% untuk booking pertama Anda.',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 50000,
                'max_discount' => 30000,
                'quota' => 100,
            ],
            [
                'code' => 'HEMAT20K',
                'name' => 'Potongan Rp20.000',
                'description' => 'Potongan langsung Rp20.000 untuk transaksi minimal Rp150.000.',
                'type' => 'fixed',
                'value' => 20000,
                'min_purchase' => 150000,
                'max_discount' => null,
                'quota' => 50,
            ],
            [
                'code' => 'GOLDMEMBER15',
                'name' => 'Diskon Khusus Gold Member',
                'description' => 'Diskon 15% khusus member level Gold ke atas.',
                'type' => 'percentage',
                'value' => 15,
                'min_purchase' => 100000,
                'max_discount' => 50000,
                'quota' => 30,
            ],
            [
                'code' => 'WEEKEND25',
                'name' => 'Promo Akhir Pekan',
                'description' => 'Diskon 25% untuk booking di hari Sabtu & Minggu.',
                'type' => 'percentage',
                'value' => 25,
                'min_purchase' => 75000,
                'max_discount' => 75000,
                'quota' => 40,
            ],
        ];

        foreach ($vouchers as $v) {
            Vouchers::create([
                'code' => $v['code'],
                'name' => $v['name'],
                'description' => $v['description'],
                'type' => $v['type'],
                'value' => $v['value'],
                'min_purchase' => $v['min_purchase'],
                'max_discount' => $v['max_discount'],
                'valid_from' => now()->startOfMonth(),
                'valid_until' => now()->addMonths(1)->endOfMonth(),
                'quota' => $v['quota'],
                'used_count' => 0,
                'is_active' => true,
            ]);
        }
    }
}