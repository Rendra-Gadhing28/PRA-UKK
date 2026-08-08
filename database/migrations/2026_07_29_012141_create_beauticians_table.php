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
        Schema::create('beauticians', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable()->unique();
            $table->longtext('photo')->nullable();
            $table->text('bio');
            $table->integer('total_bookings')->default(0)->comment('Total booking yang telah dilakukan oleh beautician');
            $table->longText('service_area')->nullable()->comment('Daftar area layanan (array)');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('phone');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beauticians');
    }
};
