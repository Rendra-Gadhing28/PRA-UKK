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
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('is_h24_reminded')->default(false)->after('version');
            $table->boolean('is_h1_reminded')->default(false)->after('is_h24_reminded');
            $table->boolean('is_m30_reminded')->default(false)->after('is_h1_reminded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'is_h24_reminded',
                'is_h1_reminded',
                'is_m30_reminded'
            ]);
        });
    }
};
