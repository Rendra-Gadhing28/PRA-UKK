<?php

namespace Database\Seeders;

use App\Models\Vouchers;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            // General / Free Vouchers
            [
                'code' => 'WELCOME10',
                'name' => 'Diskon Member Baru',
                'description' => 'Diskon 10% untuk booking pertama Anda di Yalia Beauty Salon.',
                'type' => 'percentage',
                'value' => 10,
                'min_purchase' => 50000,
                'max_discount' => 30000,
                'points_required' => 0,
                'is_event' => false,
                'event_name' => null,
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
                'points_required' => 0,
                'is_event' => false,
                'event_name' => null,
                'quota' => 50,
            ],
            
            // Point Exchange Vouchers (Tukar PTS)
            [
                'code' => 'PTSDISC15K',
                'name' => 'Voucher Poin Rp15.000',
                'description' => 'Tukarkan 30 Poin PTS Anda dengan potongan harga Rp15.000 tanpa min. transaksi.',
                'type' => 'fixed',
                'value' => 15000,
                'min_purchase' => 0,
                'max_discount' => null,
                'points_required' => 30,
                'is_event' => false,
                'event_name' => null,
                'quota' => 80,
            ],
            [
                'code' => 'PTSGLOW30',
                'name' => 'Voucher Poin 30% Off',
                'description' => 'Tukarkan 50 Poin PTS untuk diskon 30% perawatan kecantikan favoritmu.',
                'type' => 'percentage',
                'value' => 30,
                'min_purchase' => 100000,
                'max_discount' => 60000,
                'points_required' => 50,
                'is_event' => false,
                'event_name' => null,
                'quota' => 40,
            ],
            [
                'code' => 'PTSVIP50K',
                'name' => 'Voucher Sultan 50K',
                'description' => 'Special tukar 100 Poin PTS dengan potongan fantastis Rp50.000!',
                'type' => 'fixed',
                'value' => 50000,
                'min_purchase' => 200000,
                'max_discount' => null,
                'points_required' => 100,
                'is_event' => false,
                'event_name' => null,
                'quota' => 25,
            ],

            // Event Vouchers
            [
                'code' => 'BEAUTYFIESTA26',
                'name' => 'Beauty Fiesta Special 2026',
                'description' => 'Voucher eksklusif Event Annual Beauty Fiesta Yalia Beauty 2026.',
                'type' => 'percentage',
                'value' => 35,
                'min_purchase' => 120000,
                'max_discount' => 80000,
                'points_required' => 0,
                'is_event' => true,
                'event_name' => 'Beauty Fiesta 2026 🎉',
                'quota' => 150,
            ],
            [
                'code' => 'PAYDAYFEVER',
                'name' => 'Gajian Glow Up Payday',
                'description' => 'Klaim gratis saat promo Payday Gajian untuk semua paket facial & eyelash.',
                'type' => 'fixed',
                'value' => 35000,
                'min_purchase' => 175000,
                'max_discount' => null,
                'points_required' => 0,
                'is_event' => true,
                'event_name' => 'Payday Promo 💳',
                'quota' => 60,
            ],
        ];

        foreach ($vouchers as $v) {
            Vouchers::updateOrCreate(
                ['code' => $v['code']],
                [
                    'name' => $v['name'],
                    'description' => $v['description'],
                    'type' => $v['type'],
                    'value' => $v['value'],
                    'min_purchase' => $v['min_purchase'],
                    'max_discount' => $v['max_discount'],
                    'points_required' => $v['points_required'],
                    'is_event' => $v['is_event'],
                    'event_name' => $v['event_name'],
                    'valid_from' => now()->startOfMonth(),
                    'valid_until' => now()->addMonths(2)->endOfMonth(),
                    'quota' => $v['quota'],
                    'used_count' => 0,
                    'is_active' => true,
                ]
            );
        }
    }
}