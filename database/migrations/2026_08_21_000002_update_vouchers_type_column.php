<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            // Change type to string to support 'free_shipping', 'percentage', 'fixed'
            $table->string('type', 50)->default('percentage')->change();
        });

        // Insert default Gratis Ongkir voucher if not exists
        if (!DB::table('vouchers')->where('code', 'FREESHIPYALIA')->exists()) {
            DB::table('vouchers')->insert([
                'code'            => 'FREESHIPYALIA',
                'name'            => 'Voucher Gratis Ongkir Home Service',
                'description'     => 'Gratis ongkir biaya transport untuk pemesanan Home Service ke lokasi Anda.',
                'type'            => 'free_shipping',
                'value'           => 100.00, // 100% transport fee waived
                'min_purchase'    => 50000.00,
                'max_discount'    => 25000.00,
                'points_required' => 0,
                'is_event'        => false,
                'event_name'      => null,
                'valid_from'      => '2026-08-01',
                'valid_until'     => '2026-12-31',
                'quota'           => 100,
                'used_count'      => 0,
                'is_active'       => true,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Revert DB if needed
    }
};
