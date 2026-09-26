<?php

namespace App\Models;

use App\Support\ImageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beauticians extends Model
{
    use HasFactory;

    public const PHOTO_DIRECTORY = 'beauticians';

    protected static function booted(): void
    {
        $clearCache = static function (self $beautician): void {
            if ($beautician->photo) {
                ImageHelper::clearCache('beauticians/'.$beautician->photo);
                ImageHelper::clearCache($beautician->photo);
            }
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }

    protected $fillable = [
        'name',
        'phone',
        'email',
        'photo',
        'bio',
        'service_area',
        'total_bookings',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_bookings' => 'integer',
    ];

    /**
     * Total booking yang ditangani beautician (mengutamakan count aktual relasi bookings).
     */
    protected function totalBookings(): Attribute
    {
        return Attribute::make(
            get: function (?int $value): int {
                if (isset($this->attributes['bookings_count'])) {
                    return (int) $this->attributes['bookings_count'];
                }

                if ($this->relationLoaded('bookings')) {
                    return $this->bookings->count();
                }

                if (! is_null($value) && $value > 0) {
                    return (int) $value;
                }

                return (int) $this->bookings()->count();
            }
        );
    }

    /**
     * URL publik foto profil beautician.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if (blank($this->photo)) {
                    return 'https://ui-avatars.com/api/?name='.urlencode($this->name ?? 'Beautician').'&background=f45472&color=fff';
                }

                $path = $this->photo;
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                    return $path;
                }

                $cleanPath = ltrim($path, '/');
                if (! str_starts_with($cleanPath, 'beauticians/')) {
                    $cleanPath = self::PHOTO_DIRECTORY.'/'.$cleanPath;
                }

                return ImageHelper::url($cleanPath);
            }
        );
    }

    /**
     * Relasi ke bookings yang ditangani oleh beautician ini.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Bookings::class, 'beautician_id');
    }

    /**
     * Relasi ke ulasan pelanggan.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Reviews::class, 'beautician_id');
    }

    /**
     * Rata-rata rating ulasan beautician (fallback ke 5.0 jika belum ada).
     */
    protected function averageRating(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                if ($this->relationLoaded('reviews') && $this->reviews->isNotEmpty()) {
                    $avg = $this->reviews->avg('beautician_rating') ?: $this->reviews->avg('rating');

                    return round((float) ($avg ?: 5.0), 1);
                }

                $avg = $this->reviews()->avg('beautician_rating') ?: $this->reviews()->avg('rating');

                return round((float) ($avg ?: 5.0), 1);
            }
        );
    }

    /**
     * Relasi ke jadwal kerja mingguan (dipakai untuk auto-assign booking).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(BeauticiansSchedules::class, 'beautician_id');
    }
}
