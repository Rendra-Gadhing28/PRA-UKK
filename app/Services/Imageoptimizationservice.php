<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Mengoptimalkan gambar upload (treatment, beautician, dsb) dengan cara:
 * - resize ke lebar maksimum agar tidak menyimpan gambar beresolusi berlebih
 * - konversi ke format WEBP untuk ukuran file jauh lebih kecil dari JPEG/PNG
 *   tanpa penurunan kualitas visual yang signifikan
 *
 * Menggunakan Intervention Image v3 (intervention/image) dengan driver GD,
 * yang tersedia secara default pada hampir semua shared hosting PHP.
 */
class ImageOptimizationService
{
    private const MAX_WIDTH_PX = 1200;

    private const WEBP_QUALITY = 80;

    public function __construct(
        private readonly ImageManager $manager = new ImageManager(new Driver()),
    ) {}

    /**
     * Mengonversi file upload menjadi WEBP, menyimpannya ke disk "public",
     * dan mengembalikan nama file yang dihasilkan (bukan full path/URL).
     *
     * @param  UploadedFile  $file  file gambar hasil upload user/admin
     * @param  string  $directory  sub-direktori penyimpanan, mis. "treatments"
     * @return string nama file webp yang sudah disimpan, mis. "abc123.webp"
     *
     * @throws \RuntimeException bila file bukan gambar valid
     */
    public function convertAndStore(UploadedFile $file, string $directory): string
    {
        if (! str_starts_with((string) $file->getMimeType(), 'image/')) {
            throw new \RuntimeException('File yang diunggah bukan merupakan gambar yang valid.');
        }

        $image = $this->manager->read($file->getRealPath());

        // Batasi lebar maksimum, tinggi menyesuaikan (aspect ratio terjaga)
        // agar tidak menyimpan gambar beresolusi jauh lebih besar dari kebutuhan tampilan.
        $image->scaleDown(width: self::MAX_WIDTH_PX);

        $encoded = $image->toWebp(quality: self::WEBP_QUALITY);

        $filename = Str::uuid()->toString().'.webp';

        Storage::disk('public')->put(
            "{$directory}/{$filename}",
            (string) $encoded,
        );

        return $filename;
    }

    /**
     * Menghapus file gambar lama dari disk "public", dipanggil saat
     * gambar diganti atau record dihapus, agar tidak menumpuk file yatim.
     */
    public function delete(?string $filename, string $directory): void
    {
        if (blank($filename)) {
            return;
        }

        Storage::disk('public')->delete("{$directory}/{$filename}");
    }
}