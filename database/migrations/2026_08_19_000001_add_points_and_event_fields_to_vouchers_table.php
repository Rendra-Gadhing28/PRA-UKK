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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->integer('points_required')->default(0)->after('max_discount')
                ->comment('Jumlah poin PTS yang dibutuhkan untuk tukar voucher (0 = gratis/umum)');
            $table->boolean('is_event')->default(false)->after('points_required')
                ->comment('Apakah voucher ini voucher promo event special');
            $table->string('event_name')->nullable()->after('is_event')
                ->comment('Nama event khusus (misal: Beauty Fiesta 2026)');

            $table->index('points_required');
            $table->index('is_event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropIndex(['points_required']);
            $table->dropIndex(['is_event']);
            $table->dropColumn(['points_required', 'is_event', 'event_name']);
        });
    }
};
