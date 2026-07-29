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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense'])
            ->comment('Tipe tranksaksi: pemasukan atau pengeluaran');
            $table->foreignId('booking_id')
            ->nullable()
            ->constrained('bookings')
            ->onDelete('set null')
            ->comment('Foreign key ke booking (jika income dari booking)');
            $table->string('category', 150)
            ->comment('Kategori tranksaksi');
            $table->string('icon')->nullable()
            ->comment('Emoji icon untuk kategori');

            $table->string('title')
            ->comment('Judul tranksaksi');
            $table->text('description')->nullable()
            ->comment('Deskripsi detail tranksaksi');
            $table->decimal('amount', 20, 2)
            ->comment('Jumlah uang(Rp)');

            $table->string('receipt_image')->nullable()
            ->comment('Path gambar bukti/nota');
            $table->date('transaction_date')
            ->comment('Tanggal tranksaksi');
            // Additional metadata (JSON)
            $table->json('metadata')->nullable()
            ->comment('Data tambahan dalam format JSON');

            $table->foreignId('created_by')
            ->nullable()
            ->constrained('users')
            ->onDelete('set null')
            ->comment('Admin yang mencatat tranksaksi');
            $table->timestamps();

             $table->index('type');
            $table->index('category');
            $table->index('booking_id');
            $table->index('transaction_date');
            $table->index(['type', 'transaction_date']);
            $table->index(['type', 'category']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
