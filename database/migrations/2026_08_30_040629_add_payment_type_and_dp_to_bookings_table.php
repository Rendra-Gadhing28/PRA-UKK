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
            $table->string('payment_type', 20)->default('cashless')->after('payment_method');
            $table->decimal('dp_amount', 12, 2)->default(0.00)->after('total_amount');
            $table->decimal('remaining_amount', 12, 2)->default(0.00)->after('dp_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_type', 'dp_amount', 'remaining_amount']);
        });
    }
};
