<?php

declare(strict_types=1);

namespace App\Services\Booking;

use App\Models\Bookings;
use GdImage;
use Illuminate\Http\UploadedFile;

/**
 * Menangani pemrosesan foto hasil treatment (photo_assign):
 * konversi ke WebP + resize agar sisi terpanjang tidak melebihi
 * MAX_DIMENSION px.
 *
 * Diekstrak dari BookingController::uploadPhotoAssign() supaya
 * controller tetap tipis (thin controller, fat service) dan logika
 * pemrosesan gambar bisa diuji / dipakai ulang secara terpisah
 * (misalnya kalau nanti perlu dijadikan Queue Job saat traffic upload
 * tinggi — struktur ini sudah siap dibungkus jadi Job tanpa mengubah
 * controller lagi).
 */
class PhotoAssignService
{
    private const MAX_DIMENSION = 1200;

    private const WEBP_QUALITY = 88;

    private const STORAGE_SUBPATH = 'bookings/photos';

    /**
     * Proses upload, simpan sebagai WebP, dan update record booking.
     *
     * @return string path relatif yang disimpan ke kolom photo_assign
     */
    public function process(Bookings $booking, UploadedFile $file): string
    {
        $mime = $file->getMimeType();

        $image = $this->createImageResource($file->getRealPath(), $mime);
        $image = $this->preserveTransparency($image, $mime);
        $image = $this->resizeIfNeeded($image);

        $storageDir = storage_path('app/public/'.self::STORAGE_SUBPATH);
        if (! is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $filename = 'photo_assign_'.$booking->id.'_'.time().'.webp';
        $fullPath = $storageDir.'/'.$filename;
        $storagePath = self::STORAGE_SUBPATH.'/'.$filename;

        $this->deleteOldPhoto($booking);

        imagewebp($image, $fullPath, self::WEBP_QUALITY);
        imagedestroy($image);

        $booking->update(['photo_assign' => $storagePath]);

        return $storagePath;
    }

    private function createImageResource(string $path, ?string $mime): GdImage
    {
        return match (true) {
            str_contains((string) $mime, 'jpeg') => imagecreatefromjpeg($path),
            str_contains((string) $mime, 'png') => imagecreatefrompng($path),
            str_contains((string) $mime, 'gif') => imagecreatefromgif($path),
            str_contains((string) $mime, 'webp') => imagecreatefromwebp($path),
            default => imagecreatefromjpeg($path),
        };
    }

    private function preserveTransparency(GdImage $image, ?string $mime): GdImage
    {
        if (in_array($mime, ['image/png', 'image/gif'], true)) {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        return $image;
    }

    /**
     * Resize proporsional agar sisi terpanjang maksimal MAX_DIMENSION px.
     * Tidak melakukan upscale untuk gambar yang sisi terpanjangnya
     * sudah lebih kecil/sama dengan batas.
     */
    private function resizeIfNeeded(GdImage $image): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $longestSide = max($width, $height);

        if ($longestSide <= self::MAX_DIMENSION) {
            return $image;
        }

        $ratio = self::MAX_DIMENSION / $longestSide;
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        imagecopyresampled(
            $resized,
            $image,
            0,
            0,
            0,
            0,
            $newWidth,
            $newHeight,
            $width,
            $height
        );

        imagedestroy($image);

        return $resized;
    }

    private function deleteOldPhoto(Bookings $booking): void
    {
        if ($booking->photo_assign && file_exists(storage_path('app/public/'.$booking->photo_assign))) {
            @unlink(storage_path('app/public/'.$booking->photo_assign));
        }
    }
}