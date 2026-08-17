<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Menambahkan kolom yang dibutuhkan fitur Home Service (alamat & koordinat
     * hasil GPS + reverse geocoding) dan tracking pembayaran QRIS via Midtrans
     * Core API (order id, transaction id, dan batas waktu bayar 15 menit).
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // --- Home Service: alamat & koordinat ---
            $table->text('home_address')->nullable()->after('booking_type')
                ->comment('Alamat hasil reverse geocoding / input manual user (khusus home service)');
            $table->decimal('home_latitude', 10, 7)->nullable()->after('home_address')
                ->comment('Koordinat GPS user (khusus home service)');
            $table->decimal('home_longitude', 10, 7)->nullable()->after('home_latitude')
                ->comment('Koordinat GPS user (khusus home service)');
            $table->decimal('distance_km', 6, 2)->nullable()->after('home_longitude')
                ->comment('Jarak dari salon ke lokasi user, dipakai untuk hitung transport_fee');

            // --- Payment tracking (Midtrans Core API - QRIS) ---
            $table->string('midtrans_order_id', 100)->nullable()->unique()->after('payment_proof')
                ->comment('order_id yang dikirim ke Midtrans saat charge, unik per booking');
            $table->string('midtrans_transaction_id', 100)->nullable()->after('midtrans_order_id')
                ->comment('transaction_id yang dikembalikan Midtrans untuk booking ini');
            $table->timestamp('payment_expires_at')->nullable()->after('midtrans_transaction_id')
                ->comment('Batas waktu bayar QRIS (created_at + 15 menit), lewat ini booking auto-dibatalkan');

            $table->index('payment_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['payment_expires_at']);
            $table->dropColumn([
                'home_address',
                'home_latitude',
                'home_longitude',
                'distance_km',
                'midtrans_order_id',
                'midtrans_transaction_id',
                'payment_expires_at',
            ]);
        });
    }
};
