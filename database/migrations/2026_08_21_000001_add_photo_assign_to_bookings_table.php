<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'photo_assign')) {
                // Foto dokumentasi treatment yang telah dijalankan (opsional, disimpan sebagai WebP)
                $table->string('photo_assign')->nullable()->after('payment_proof')
                    ->comment('Path foto hasil treatment (WebP), diupload oleh beautician/admin setelah selesai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'photo_assign')) {
                $table->dropColumn('photo_assign');
            }
        });
    }
};
