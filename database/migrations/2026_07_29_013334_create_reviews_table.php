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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
            ->constrained('bookings')
            ->onDelete('cascade')
            ->comment('Foreign key ke booking (one review per booking)');

            $table->foreignId('user_id')
            ->constrained('users')
            ->onDelete('cascade')
            ->comment('Customer yang memberikan review');

            $table->foreignId('beautician_id')->nullable()
            ->constrained('beauticians')
            ->onDelete('set null')
            ->comment('Beautician yang direview');

            $table->tinyInteger('rating')
            ->comment('Rating 1-5');

            $table->text('comment')->nullable()
            ->comment('Komentar review');

            $table->string('photo')->nullable()
            ->comment('Foto hasil treatment');

            $table->boolean('is_approved')->default(false);

            $table->text('admin_reply')->nullable()
            ->comment('Balasan dari admin');

            $table->timestamps();
            $table->unique('booking_id')
            ->comment('Satu booking hanya bisa satu review');
            $table->index('user_id');
            $table->index('beautician_id');
            $table->index('rating');
            $table->index('is_approved');
            $table->index(['beautician_id', 'rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
