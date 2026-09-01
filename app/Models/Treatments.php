<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Treatment / layanan salon yang dapat dibooking.
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $benefits
 * @property float $price
 * @property int $duration_minutes
 * @property string|null $images nama file gambar yang sudah dikonversi ke .webp
 * @property string $badge
 * @property bool $is_active
 * @property int $sort_order
 * @property float $rating
 * @property int $rating_count
 * @property-read Categories $category
 * @property-read string $image_url URL publik gambar (fallback ke placeholder bila kosong)
 */
class Treatments extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        $clearCache = static function (): void {
            \Illuminate\Support\Facades\Cache::forget('treatments:active-with-category');
            \Illuminate\Support\Facades\Cache::forget('dashboard:top-treatments-v5');
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }

    /**
     * Direktori penyimpanan gambar treatment di disk "public".
     */
    public const IMAGE_DIRECTORY = 'treatments';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'benefits',
        'price',
        'duration_minutes',
        'images',
        'badge',
        'is_active',
        'sort_order',
        'rating',
        'rating_count',
        'points',
    ];

    /**
     * Aksesor yang otomatis ikut disertakan saat model dikonversi ke array/JSON/cache.
     *
     * @var list<string>
     */
    protected $appends = [
        'image_url',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration_minutes' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'rating' => 'decimal:1',
            'rating_count' => 'integer',
        ];
    }

    
    /**
     * Relasi ke kategori. Selalu eager-load ini via ->with('category')
     * di layer query supaya tidak memicu N+1 saat menampilkan listing.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class);
    }

    /**
     * Relasi ke bookings (Many-to-Many via booking_treatments).
     */
    public function bookings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Bookings::class, 'booking_treatments', 'treatment_id', 'booking_id')
            ->withPivot(['quantity', 'price_per_unit', 'subtotal']);
    }

    /**
     * Relasi ke booking_treatments pivot.
     */
    public function bookingTreatments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(BookingTreatments::class, 'treatment_id');
    }


    /**
     * URL publik gambar treatment. Mengembalikan placeholder bila
     * treatment belum memiliki gambar, sehingga view tidak perlu
     * melakukan pengecekan null berulang kali.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => \App\Support\ImageHelper::url($this->images),
        );
    }

    /**
     * Scope: hanya treatment yang aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter berdasarkan pencarian nama/deskripsi.
     * Menggunakan parameter binding (bukan raw string) untuk mencegah SQL injection.
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: filter berdasarkan slug kategori.
     */
    public function scopeInCategory(Builder $query, ?string $categorySlug): Builder
    {
        if (blank($categorySlug) || $categorySlug === 'all') {
            return $query;
        }

        return $query->whereHas('category', function (Builder $q) use ($categorySlug): void {
            $q->where('slug', $categorySlug);
        });
    }
}