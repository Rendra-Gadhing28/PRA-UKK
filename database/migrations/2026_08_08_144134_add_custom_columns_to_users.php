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
          
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
         
        });
    }
};