<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom-kolom khusus Yalia Beauty ke tabel `users` bawaan Breeze
 * (yang defaultnya hanya punya name, email, password, remember_token, timestamps).
 *
 * PENTING: jalankan migration ini SEBELUM migration lain yang menambahkan
 * unique index ke kolom phone. Jika kamu sudah punya migration terpisah
 * yang isinya hanya `$table->unique('phone')`, HAPUS migration tersebut —
 * unique constraint sudah didefinisikan langsung di sini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 30)->unique()->nullable()->after('email');
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->enum('membership_level', ['regular', 'silver', 'gold', 'platinum'])
                ->default('regular')
                ->after('longitude');
            $table->integer('total_points')->default(0)->after('membership_level');
            $table->integer('total_bookings')->default(0)->after('total_points');
            $table->decimal('total_spending', 15, 2)->default(0)->after('total_bookings');
            $table->boolean('is_active')->default(true)->after('total_spending');
            $table->boolean('is_admin')->default(false)->after('is_active');

            // Kolom untuk login via Google (Socialite)
            $table->string('google_id')->nullable()->unique()->after('is_admin');
            $table->text('google_token')->nullable()->after('google_id');
            $table->text('google_refresh_token')->nullable()->after('google_token');
            $table->string('avatar_url')->nullable()->after('google_refresh_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['phone']);
            $table->dropUnique(['google_id']);

            $table->dropColumn([
                'phone', 'avatar', 'address', 'latitude', 'longitude',
                'membership_level', 'total_points', 'total_bookings',
                'total_spending', 'is_active', 'is_admin',
                'google_id', 'google_token', 'google_refresh_token', 'avatar_url',
            ]);
        });
    }
};