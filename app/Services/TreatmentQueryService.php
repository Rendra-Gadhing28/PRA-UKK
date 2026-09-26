<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Categories;
use App\Models\Treatments;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Menangani seluruh logika query untuk listing Treatment:
 * - eager loading relasi (anti N+1)
 * - sorting cerdas (treatment favorit user dipin di posisi paling atas)
 * - filter kategori khusus 'favorites' (hanya menampilkan favorit user)
 * - caching kategori aktif dengan invalidation berbasis versi
 */
class TreatmentQueryService
{
    private const CACHE_TTL_SECONDS = 300; // 5 menit

    private const VERSION_CACHE_KEY = 'treatments:cache-version';

    private const CATEGORIES_CACHE_KEY_PREFIX = 'categories:active:v';

    private const DEFAULT_PER_PAGE = 9;

    /**
     * Mengambil daftar treatment aktif dengan filter pencarian & kategori,
     * dipaginasi menggunakan simple Eloquent pagination.
     */
    public function paginateActiveTreatments(
        ?string $search,
        ?string $categorySlug,
        ?string $page = null,
        int $perPage = self::DEFAULT_PER_PAGE,
    ): LengthAwarePaginator {
        return $this->baseQuery($search, $categorySlug)
            ->paginate($perPage);
    }

    /**
     * Total jumlah treatment yang difavoritkan oleh user tertentu.
     */
    public function getUserFavoritesCount(?int $userId = null): int
    {
        $uid = $userId ?? Auth::id();
        if (! $uid) {
            return 0;
        }

        return \DB::table('user_favorite_treatments')
            ->where('user_id', $uid)
            ->count();
    }

    /**
     * Query dasar treatment aktif dengan eager loading, penanda is_favorite,
     * dan pengurutan prioritas (Favorit Teratas -> Sort Order -> Tanggal Terbaru).
     */
    private function baseQuery(?string $search, ?string $categorySlug): Builder
    {
        $userId = Auth::id() ?? 0;

        $query = Treatments::query()
            ->active()
            ->search($search);

        if ($categorySlug === 'favorites') {
            $query->whereExists(function ($q) use ($userId): void {
                $q->select(\DB::raw(1))
                    ->from('user_favorite_treatments')
                    ->whereColumn('user_favorite_treatments.treatment_id', 'treatments.id')
                    ->where('user_favorite_treatments.user_id', $userId);
            });
        } else {
            $query->inCategory($categorySlug);
        }

        return $query
            ->select([
                'id', 'category_id', 'name', 'slug', 'description',
                'price', 'duration_minutes', 'images', 'badge',
                'rating', 'rating_count', 'sort_order', 'created_at',
            ])
            ->selectRaw(
                $userId > 0
                    ? 'EXISTS(SELECT 1 FROM user_favorite_treatments WHERE user_favorite_treatments.treatment_id = treatments.id AND user_favorite_treatments.user_id = ?) as is_favorite'
                    : '0 as is_favorite',
                $userId > 0 ? [$userId] : []
            )
            ->with(['category:id,name,slug'])
            ->orderByDesc('is_favorite')
            ->orderByDesc('sort_order')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    /**
     * Mengambil daftar kategori aktif untuk filter bar, di-cache karena
     * data ini jarang berubah namun diakses di setiap request listing.
     *
     * @return Collection<int, Categories>
     */
    public function getActiveCategories(): Collection
    {
        $cacheKey = self::CATEGORIES_CACHE_KEY_PREFIX.$this->currentVersion();

        $cachedRows = Cache::remember(
            $cacheKey,
            self::CACHE_TTL_SECONDS,
            fn (): array => Categories::query()->active()->get(['id', 'name', 'slug', 'icon'])->toArray(),
        );

        return Categories::hydrate($cachedRows);
    }

    /**
     * Menaikkan versi cache.
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
