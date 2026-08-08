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
        ?string $cursor,
        int $perPage = self::DEFAULT_PER_PAGE,
    ): CursorPaginator {
        // Hasil pencarian bebas (kombinasi filter tak terbatas) sengaja TIDAK
        // di-cache karena kombinasi query bisa sangat banyak. Sebagai
        // gantinya kita cache hanya query "default" (tanpa filter, halaman
        // pertama / cursor kosong) yang paling sering diakses.
        $isDefaultQuery = blank($search) && (blank($categorySlug) || $categorySlug === 'all') && blank($cursor);

        if (! $isDefaultQuery) {
            return $this->baseQuery($search, $categorySlug)
                ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
        }

        $cacheKey = sprintf('treatments:default:v%d', $this->currentVersion());

        $fetchFreshRows = fn (): array => $this->baseQuery($search, $categorySlug)
            // ambil 1 baris ekstra untuk mendeteksi hasMorePages,
            // sesuai konvensi internal CursorPaginator
            ->limit($perPage + 1)
            ->get()
            ->toArray();

        // Hanya array atribut mentah (bukan objek Model/Paginator) yang disimpan
        // ke cache, karena array plain PHP selalu aman di-serialize/unserialize.
        $cachedRows = Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, $fetchFreshRows);

        // Pengaman: jika entry cache yang terbaca ternyata bukan array plain
        // (mis. sisa cache lama dari versi kode sebelumnya yang sempat
        // menyimpan objek paginator utuh dan gagal di-unserialize dengan
        // bersih), buang entry yang korup itu dan ambil data segar dari DB
        // alih-alih membiarkan aplikasi crash.
        if (! is_array($cachedRows)) {
            Cache::forget($cacheKey);
            $cachedRows = Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, $fetchFreshRows);
        }

        return $this->hydratePaginator($cachedRows, $perPage);
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
     * Membangun ulang objek CursorPaginator dari array data mentah (baik
     * yang baru diambil dari database maupun yang berasal dari cache).
     * Cursor selalu null di sini karena hanya halaman pertama yang di-cache.
     *
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function hydratePaginator(array $rows, int $perPage): CursorPaginator
    {
        // Treatment::hydrate() mengembalikan atribut apa adanya dari array,
        // termasuk "category" yang saat ini masih berupa array biasa (hasil
        // ->toArray() sebelumnya), BUKAN relasi Model. Karena itu kita perlu
        // secara eksplisit mengonversinya kembali menjadi relasi Category
        // via setRelation(), agar $treatment->category->name di view bekerja
        // seperti relasi Eloquent normal (dan tidak memicu query tambahan,
        // sebab datanya sudah ada di tangan / tidak lazy-loaded).
        $items = Treatments::hydrate($rows)->map(function (Treatments $treatment): Treatments {
            $categoryData = $treatment->getAttribute('category');

            $treatment->setRelation(
                'category',
                is_array($categoryData) ? new Categories($categoryData) : null,
            );

            // Hapus atribut mentah "category" supaya tidak dobel/rancu
            // dengan relasi yang baru saja di-set di atas.
            $treatment->offsetUnset('category');

            return $treatment;
        });

        return new \Illuminate\Pagination\CursorPaginator(
            $items,
            $perPage,
            null,
            [
                'path' => RequestFacade::url(),
                'cursorName' => 'cursor',
                'parameters' => self::ORDER_COLUMNS,
            ],
        );
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