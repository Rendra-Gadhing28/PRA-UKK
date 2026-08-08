<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table): void {
            $table->index(['is_active', 'category_id', 'sort_order'], 'treatments_listing_index');
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table): void {
            $table->dropIndex('treatments_listing_index');
        });
    }
};