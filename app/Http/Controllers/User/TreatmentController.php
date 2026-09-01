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
            categorySlug: $request->categorySlug()
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
     * AUDIT: method ini disebut di docblock class di atas ("search(): endpoint
     * AJAX ringan... mengembalikan JSON berisi HTML partial grid + status
     * pagination") tapi SEBELUMNYA TIDAK PERNAH diimplementasikan — badan
     * method-nya kosong. Ini saya isi berdasarkan deskripsi docblock tersebut,
     * memakai service & request yang sama persis dengan index() supaya hasil
     * pencarian AJAX konsisten dengan hasil SSR.
     *
     * ASUMSI yang PERLU DIVERIFIKASI terhadap Blade Anda:
     * - Nama partial view: 'user.treatments.partials.grid'. Ganti kalau
     *   Blade Anda pakai path/nama lain.
     * - Bentuk payload JSON (html, currentPage, lastPage, hasMorePages,
     *   total) — sesuaikan dengan apa yang dibaca Alpine.js di frontend.
     * - Route belum tentu terdaftar di routes/web.php — pastikan ada,
     *   misalnya:
     *   Route::get('/treatments/search', [TreatmentController::class, 'search'])
     *       ->name('user.treatments.search');
     *
     * Nomor halaman ('page') tidak perlu ditangani manual di sini — Eloquent
     * paginator otomatis membaca query string ?page= dari request.
     */
    public function search(SearchTreatmentRequest $request): JsonResponse
    {
        $paginatedTreatments = $this->treatments->paginateActiveTreatments(
            search: $request->search(),
            categorySlug: $request->categorySlug()
        );

        return response()->json([
            'html' => view('user.treatments.partials.grid', [
                'treatments' => $paginatedTreatments,
            ])->render(),
            'currentPage' => $paginatedTreatments->currentPage(),
            'lastPage' => $paginatedTreatments->lastPage(),
            'hasMorePages' => $paginatedTreatments->hasMorePages(),
            'total' => $paginatedTreatments->total(),
        ]);
    }
}