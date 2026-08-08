<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchTreatmentRequest;
use App\Services\TreatmentQueryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Menangani halaman publik daftar treatment (menu layanan salon).
 *
 * Dua entry point:
 * - index(): render halaman penuh (SSR) untuk kunjungan pertama / SEO.
 * - search(): endpoint AJAX ringan yang dipanggil dari Alpine.js
 *   (dengan debounce) setiap kali user mengetik kata kunci, memilih
 *   kategori, atau menekan "Muat Lebih Banyak". Mengembalikan JSON
 *   berisi HTML partial grid + status pagination, sehingga tidak perlu
 *   reload halaman penuh.
 */
class TreatmentController extends Controller
{
    public function __construct(
        private readonly TreatmentQueryService $treatments,
    ) {}

    /**
     * Menampilkan halaman daftar treatment beserta filter kategori.
     * Data awal (halaman pertama, tanpa filter) diambil dari cache
     * agar request pertama tetap cepat meski traffic tinggi.
     */
    public function index(SearchTreatmentRequest $request): View
    {
        $paginatedTreatments = $this->treatments->paginateActiveTreatments(
            search: $request->search(),
            categorySlug: $request->categorySlug(),
            cursor: $request->cursor(),
        );

        $categories = $this->treatments->getActiveCategories();

        return view('user.treatments.index', [
            'treatments' => $paginatedTreatments,
            'categories' => $categories,
            'currentSearch' => $request->search(),
            'currentCategory' => $request->categorySlug() ?? 'all',
        ]);
    }

    /**
     * Endpoint AJAX untuk pencarian & filter real-time (dipanggil dengan
     * debounce dari frontend). Dibatasi rate limit di route (throttle)
     * untuk mencegah penyalahgunaan/spam request.
     */
    public function search(SearchTreatmentRequest $request): JsonResponse
    {
        $paginatedTreatments = $this->treatments->paginateActiveTreatments(
            search: $request->search(),
            categorySlug: $request->categorySlug(),
            cursor: $request->cursor(),
        );

        return response()->json([
            'html' => view('user.treatments.partials.grid', [
                'treatments' => $paginatedTreatments,
            ])->render(),
            'next_cursor' => $paginatedTreatments->nextCursor()?->encode(),
            'has_more' => $paginatedTreatments->hasMorePages(),
        ]);
    }
}