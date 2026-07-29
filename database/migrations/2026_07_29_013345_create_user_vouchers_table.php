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
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
            ->constrained('users')
            ->onDelete('cascade')
            ->comment('User yang mengklaim voucher');

            $table->foreignId('voucher_id')
            ->constrained('vouchers')
            ->onDelete('cascade')
            ->comment('Voucher yang diklaim');

            $table->foreignId('booking_id')
            ->nullable()
            ->constrained('bookings')
            ->onDelete('set null')
            ->comment('Booking dimana voucher digunakan');

            $table->boolean('is_used')->default(false)
            ->comment('Status penggunaan voucher');

            $table->timestamp('used_at')->nullable()
            ->comment('Tanggal penggunaan voucher');
            $table->timestamps();

            $table->index('user_id');
            $table->index('voucher_id');
            $table->index('booking_id');
            $table->index('is_used');
            
            // Unique constraint: satu user hanya bisa klaim satu kode voucher yang sama
            $table->unique(
                ['user_id', 'voucher_id'],
                'unique_user_voucher'
            )->comment('User hanya bisa klaim 1 voucher yang sama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_vouchers');
    }
};
