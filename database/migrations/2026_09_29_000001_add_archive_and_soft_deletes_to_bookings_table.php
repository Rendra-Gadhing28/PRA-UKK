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
            if (! Schema::hasColumn('bookings', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('is_m30_reminded');
            }
            if (! Schema::hasColumn('bookings', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('is_archived');
            }
            if (! Schema::hasColumn('bookings', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            $table->index(['is_archived', 'booking_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'is_archived')) {
                $table->dropIndex(['is_archived', 'booking_date']);
                $table->dropColumn(['is_archived', 'archived_at']);
            }
            if (Schema::hasColumn('bookings', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
