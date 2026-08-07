<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;

/**
 * Helper untuk menampilkan toast notification via session flash.
 *
 * Digunakan di controller sebelum redirect untuk mengirim
 * pesan notifikasi yang ditampilkan di pojok kanan bawah halaman.
 *
 * Contoh penggunaan:
 *   ToastHelper::success('Data berhasil disimpan.');
 *   ToastHelper::error('Terjadi kesalahan.');
 */
class ToastHelper
{
    /**
     * Tampilkan toast sukses (hijau).
     *
     * @param  string  $pesan   Teks yang ditampilkan dalam toast.
     */
    public static function success(string $pesan): void
    {
        self::flash('success', $pesan);
    }

    /**
     * Tampilkan toast error (merah).
     *
     * @param  string  $pesan   Teks yang ditampilkan dalam toast.
     */
    public static function error(string $pesan): void
    {
        self::flash('error', $pesan);
    }

    /**
     * Tampilkan toast peringatan (kuning).
     *
     * @param  string  $pesan   Teks yang ditampilkan dalam toast.
     */
    public static function warning(string $pesan): void
    {
        self::flash('warning', $pesan);
    }

    /**
     * Tampilkan toast informasi (biru).
     *
     * @param  string  $pesan   Teks yang ditampilkan dalam toast.
     */
    public static function info(string $pesan): void
    {
        self::flash('info', $pesan);
    }

    /**
     * Simpan pesan toast ke session flash.
     *
     * Format data: ['type' => 'success', 'message' => 'Pesan...']
     *
     * @param  string  $tipe   Tipe toast: success|error|warning|info
     * @param  string  $pesan  Teks pesan toast
     */
    private static function flash(string $tipe, string $pesan): void
    {
        Session::flash('toast', [
            'type'    => $tipe,
            'message' => $pesan,
        ]);
    }
}