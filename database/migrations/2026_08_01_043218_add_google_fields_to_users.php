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
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth fields
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('google_token')->nullable()->after('google_id');
            $table->string('google_refresh_token')->nullable()->after('google_token');
            
            // Avatar URL dari Google (jika tidak upload manual)
            $table->string('avatar_url')->nullable()->after('avatar');
            
            // Bikin password nullable (karena login Google tidak pakai password)
            $table->string('password')->nullable()->change();
            
            // Bikin phone tidak unique sementara (opsional)
            // $table->string('phone', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'google_token',
                'google_refresh_token',
                'avatar_url',
            ]);
            
            // Kembalikan password ke required
            $table->string('password')->nullable(false)->change();
        });
    }
};
