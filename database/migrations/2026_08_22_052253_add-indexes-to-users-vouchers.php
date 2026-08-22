<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Optimasi index tabel user_vouchers.
 * Asumsi tabel sudah ada dengan kolom: id, user_id, voucher_id, is_used, timestamps.
 * Jalankan setelah migration create_user_vouchers_table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_vouchers', function (Blueprint $table) {
            // Composite untuk exists() check di claim() dan pluck() di index()
            // Lebih efisien dari dua single-index terpisah
            $table->index(['user_id', 'voucher_id'], 'idx_user_vouchers_uid_vid');

            // ORDER BY is_used di query myVouchers
            $table->index('is_used', 'idx_user_vouchers_is_used');
        });
    }

    public function down(): void
    {
        Schema::table('user_vouchers', function (Blueprint $table) {
            $table->dropIndex('idx_user_vouchers_uid_vid');
            $table->dropIndex('idx_user_vouchers_is_used');
        });
    }
};