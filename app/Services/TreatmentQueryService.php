<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Categories;
use App\Models\Treatments;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request as RequestFacade;

/**
 * Menangani seluruh logika query untuk listing Treatment:
 * - eager loading relasi (anti N+1)
 * - caching dengan invalidation berbasis versi
 * - cursor pagination (lebih efisien daripada offset pagination
 *   untuk tabel yang terus bertambah / sering diakses publik)
 *
 * Cache driver aplikasi ini adalah "database", yang TIDAK mendukung
 * cache tags. Sebagai gantinya kita pakai pola "cache versioning":
 * setiap kali data treatment berubah (create/update/delete), sebuah
 * counter versi di-bump (lihat TreatmentObserver). Versi ini ikut
 * masuk ke dalam cache key, sehingga key lama otomatis "basi" tanpa
 * perlu menghapus satu per satu.
 *
 * PENTING: kita TIDAK boleh menyimpan objek CursorPaginator utuh ke
 * dalam cache. Objek paginator membawa closure (path resolver) dan
 * referensi lain yang tidak bisa di-serialize/unserialize dengan
 * bersih lewat cache driver "database" — hasilnya adalah error
 * `__PHP_Incomplete_Class` saat cache dibaca kembali. Solusinya:
 * cache hanya array data mentah (atribut model), lalu bangun ulang
 * objek CursorPaginator secara manual di setiap request.
 */
class TreatmentQueryService
{
    private const CACHE_TTL_SECONDS = 300; // 5 menit

    private const VERSION_CACHE_KEY = 'treatments:cache-version';

    private const CATEGORIES_CACHE_KEY_PREFIX = 'categories:active:v';

    private const DEFAULT_PER_PAGE = 9;

    /**
     * Kolom yang dipakai untuk order by & sebagai parameter cursor.
     * "id" ditambahkan sebagai tiebreaker agar urutan selalu deterministik
     * walau ada beberapa treatment dengan sort_order/created_at yang sama.
     */
    private const ORDER_COLUMNS = ['sort_order', 'created_at', 'id'];

    /**
     * Mengambil daftar treatment aktif dengan filter pencarian & kategori,
     * dipaginasi menggunakan cursor pagination.
     *
     * @param  string|null  $search  kata kunci pencarian (nama/deskripsi)
     * @param  string|null  $categorySlug  slug kategori, atau null/"all" untuk semua
     * @param  string|null  $cursor  cursor pagination dari request sebelumnya
     * @param  int  $perPage  jumlah item per halaman
     */
    public function paginateActiveTreatments(
        ?string $search,
        ?string $categorySlug,
        ?string $page = null,
        int $perPage = self::DEFAULT_PER_PAGE,
    ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
        // Query akan menggunakan simple Eloquent pagination agar URL mengandung "?page=" 
        // dan menampilkan link nomor halaman lengkap.
        return $this->baseQuery($search, $categorySlug)
            ->paginate($perPage);
    }

    /**
     * Query dasar treatment aktif dengan eager loading & seleksi kolom minimal.
     */
    private function baseQuery(?string $search, ?string $categorySlug): \Illuminate\Database\Eloquent\Builder
    {
        return Treatments::query()
            ->active()
            ->search($search)
            ->inCategory($categorySlug)
            // select kolom yang benar-benar dipakai di listing untuk
            // mengurangi ukuran payload dari database
            ->select([
                'id', 'category_id', 'name', 'slug', 'description',
                'price', 'duration_minutes', 'images', 'badge',
                'rating', 'rating_count', 'sort_order', 'created_at',
            ])
            // eager load kategori agar tidak N+1 saat view mengakses $treatment->category->name
            ->with(['category:id,name,slug'])
            ->orderByDesc('sort_order')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }



    /**
     * Mengambil daftar kategori aktif untuk filter bar, di-cache karena
     * data ini jarang berubah namun diakses di setiap request listing.
     *
     * @return \Illuminate\Support\Collection<int, Categories>
     */
    public function getActiveCategories(): \Illuminate\Support\Collection
    {
        $cacheKey = self::CATEGORIES_CACHE_KEY_PREFIX.$this->currentVersion();

        // Sama seperti di atas: cache array atribut (bukan koleksi model),
        // lalu hydrate ulang, agar konsisten aman untuk driver cache "database".
        $cachedRows = Cache::remember(
            $cacheKey,
            self::CACHE_TTL_SECONDS,
            fn (): array => Categories::query()->active()->get(['id', 'name', 'slug', 'icon'])->toArray(),
        );

        return Categories::hydrate($cachedRows);
    }

    /**
     * Menaikkan versi cache. Dipanggil oleh TreatmentObserver /
     * CategoryObserver setiap kali data berubah, agar cache lama
     * otomatis tidak terpakai lagi tanpa perlu flush manual.
     */
    public function bumpCacheVersion(): void
    {
        Cache::forever(self::VERSION_CACHE_KEY, $this->currentVersion() + 1);
    }

    /**
     * Versi cache saat ini. Dimulai dari 1 bila belum pernah di-set.
     */
    private function currentVersion(): int
    {
        return (int) Cache::get(self::VERSION_CACHE_KEY, 1);
    }
}