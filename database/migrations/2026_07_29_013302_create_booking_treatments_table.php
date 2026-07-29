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
        Schema::create('booking_treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
            ->constrained('bookings')
            ->onDelete('cascade')
            ->comment('Foreign key ke bookings');

            $table->foreignId('treatment_id')
            ->constrained('treatments')
            ->onDelete('restrict')
            ->comment('Foreign key ke treatments (restrict agar treatment tida bisa dihapus jika ada yang booking)');
            $table->integer('quantity')->default(1)
            ->comment('Jumlah treatment yang dibooking');
            $table->decimal('price_per_unit', 20, 2)
            ->comment('Harga per unit saat booking (snapshoot harga)');
            $table->decimal('subtotal', 20, 2)
            ->comment('Subtotal = quantity * price_per_unit');
            $table->timestamps();

            $table->index('booking_id');
            $table->index('treatment_id');
            $table->unique(
                ['booking_id', 'treatment_id'],
                'unique_booking_treatment'
            )->comment('Satu treatment hanya bisa sekali dalam satu booking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_treatments');
    }
};
