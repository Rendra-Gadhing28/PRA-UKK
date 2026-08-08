<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Treatments;
use App\Services\ImageOptimizationService;
use App\Services\TreatmentQueryService;

/**
 * Observer untuk model Treatment.
 *
 * Tanggung jawab:
 * 1. Menjaga cache listing treatment selalu segar dengan membump
 *    versi cache setiap kali data berubah (create/update/delete).
 * 2. Membersihkan file gambar lama dari storage saat treatment dihapus,
 *    agar tidak ada file "yatim" yang menumpuk di disk.
 *
 * Catatan: konversi gambar ke WEBP dilakukan secara eksplisit di layer
 * Controller/Service saat menerima file upload (bukan di observer),
 * karena observer hanya menerima path string yang sudah final, bukan
 * instance UploadedFile.
 */
class TreatmentObserver
{
    public function __construct(
        private readonly TreatmentQueryService $queryService,
        private readonly ImageOptimizationService $imageService,
    ) {}

    public function created(Treatments $treatment): void
    {
        $this->queryService->bumpCacheVersion();
    }

    public function updated(Treatments $treatment): void
    {
        $this->queryService->bumpCacheVersion();
    }

    public function deleted(Treatments $treatment): void
    {
        $this->imageService->delete($treatment->image, Treatment::IMAGE_DIRECTORY);
        $this->queryService->bumpCacheVersion();
    }
}