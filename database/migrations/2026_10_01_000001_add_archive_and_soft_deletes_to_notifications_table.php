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
        Schema::table('notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('notifications', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->after('notifiable_id')->constrained('bookings')->nullOnDelete();
            }
            if (! Schema::hasColumn('notifications', 'is_archived')) {
                $table->boolean('is_archived')->default(false)->after('read_at');
            }
            if (! Schema::hasColumn('notifications', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->after('is_archived');
            }
            if (! Schema::hasColumn('notifications', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }

            $table->index(['is_archived', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'booking_id')) {
                $table->dropForeign(['booking_id']);
                $table->dropColumn('booking_id');
            }
            if (Schema::hasColumn('notifications', 'is_archived')) {
                $table->dropIndex(['is_archived', 'created_at']);
                $table->dropColumn(['is_archived', 'archived_at']);
            }
            if (Schema::hasColumn('notifications', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
