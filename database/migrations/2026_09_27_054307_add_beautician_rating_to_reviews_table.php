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
        Schema::table('reviews', function (Blueprint $table) {
            $table->tinyInteger('beautician_rating')
                ->nullable()
                ->after('rating')
                ->comment('Rating khusus untuk beautician 1-5');

            $table->text('beautician_tags')
                ->nullable()
                ->after('beautician_rating')
                ->comment('Tag apresiasi untuk beautician (JSON atau koma)');

            $table->index('beautician_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropIndex(['beautician_rating']);
            $table->dropColumn(['beautician_rating', 'beautician_tags']);
        });
    }
};
