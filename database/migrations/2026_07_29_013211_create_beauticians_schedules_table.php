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
        Schema::create('beauticians_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beautician_id')
            ->constrained('beauticians')
            ->onDelete('cascade');
            $table->tinyInteger('day_of_week')
            ->comment('0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu');
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_working')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index('beautician_id');
            $table->index('day_of_week');
            $table->unique(['beautician_id', 'day_of_week'], 'unique_beautician_schedule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beauticians_schedules');
    }
};
