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
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
            ->constrained('categories')
            ->onDelete('cascade')
            ->comment('Foreign key ke categories');
            $table->string('name', 255);
            $table->string('slug')->unique();
            $table->string('description', 255)->nullable();
            $table->string('benefits', 255)->nullable();
            $table->decimal('price', 20, 2);
            $table->integer('duration_minutes')->comment('Durasi dalam menit');
            $table->longText('images')->nullable();
            $table->enum('badge', ['none', 'best_seller', 'new']);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->decimal('rating', 5,2)->default(0.00);
            $table->integer('rating_count')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('badge');
            $table->index('sort_order');
            $table->index('price');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatments');
    }
};
