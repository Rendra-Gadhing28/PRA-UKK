<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_code', 50)->unique();
            $table->foreignId('user_id')
            ->constrained('users')
            ->onDelete('cascade')
            ->comment('Customer yang booking');
            $table->foreignId('beautician_id')
            ->constrained('beauticians')
            ->onDelete('cascade')
            ->comment('Beautician yang menangani');
            $table->enum('booking_type', ['salon', 'home'])
            ->comment('Tipe kunjungan: datang ke salon atau home service');
            $table->enum('status', [
                'pending',
                'confirmed',
                'in_progress',
                'completed', 
                'canceled'
            ])->default('pending');
            $table->date('booking_date');
            $table->time('time_start');
            $table->time('time_end');

            $table->decimal('subtotal', 20, 2)
            ->comment('Subtotal treatment sebelum diskon & ongkir');
            $table->decimal('discount_amount', 20, 2)->default(0.00)
            ->comment('Jumlah diskon (membership/voucher)');
            $table->decimal('transport_fee', 20, 2)->default(0.00)
            ->comment('Biaya transport untuk home service');
            $table->decimal('total_amount', 20, 2)
            ->comment('Total akhir yang harus dibayar');
            
            // Payment details
            $table->string('payment_method', 50)
            ->comment('cash, qris, transfer');


            $table->enum('payment_status', [
                'unpaid',
                'pending', 
                'paid', 
                'refunded'
            ])->default('unpaid');

            $table->string('qris_code')->nullable()
            ->comment('Kode QRIS yang digenerate');
            $table->string('qris_image_url')->nullable()
            ->comment('URL gambar QR code');
            $table->string('payment_proof')->nullable()
            ->comment('Path bukti pembayaran yang diupload user');
            $table->timestamp('payment_verified_at')->nullable();
            $table->foreignId('payment_verified_by')
            ->nullable()
            ->constrained('users')
            ->onDelete('set null')
            ->comment('Admin yang verifikasi pembayaran');
            
            $table->text('notes')->nullable()
            ->comment('Catatan khusus dari customer');
            $table->text('cancel_reason')->nullable()
            ->comment('Alasan pembatalan');
            $table->timestamp('canceled_at')->nullable();
         
            $table->integer('version')->default(1)
            ->comment('Untuk optimistic locking, cegah race condition');
            $table->timestamps();

            $table->index('booking_code');
            $table->index('user_id');
            $table->index('beautician_id');
            $table->index('status');
            $table->index('booking_date');
            $table->index('payment_status');
            $table->index('payment_method');
            $table->index(['booking_date', 'status']);
            $table->index(['booking_date', 'beautician_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
