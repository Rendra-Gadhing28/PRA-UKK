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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()
            ->comment('Kode voucher (unique)');

            $table->string('name')
            ->comment('Nama voucher');

            $table->text('description')->nullable()
            ->comment('Deskripsi dan syarat ketentuan');

            $table->enum('type', ['percentage', 'fixed'])
            ->comment('Tips: persentase atau nominal');

            $table->decimal('value', 15, 2)
            ->comment('Nilai diskon (persen atau nominal');
            $table->decimal('min_purchase', 15, 2)->nullable()
            ->comment('Maksimal diskon (untuk tipe persentase)');

            $table->decimal('max_discount', 15, 2)->nullable()
            ->comment('Maksimal diskon untuk tipe persentase)');
            $table->date('valid_from')
            ->comment('Tanggal mulai berlaku');

            $table->date('valid_until')
            ->comment('Tanggal kadaluarsa');

            $table->integer('quota')
            ->comment('Total kuota voucher');

            $table->integer('used_count')->default(0)
            ->comment('Jumlah yang digunakan');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('code');
            $table->index('type');
            $table->index('valid_from');
            $table->index('valid_until');
            $table->index('is_active');
            $table->index(['valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
