<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Beauticians extends Model
{
    use HasFactory;

    public const PHOTO_DIRECTORY = 'beauticians';

    protected $fillable = [
        'name', 
        'phone', 
        'email', 
        'photo',
        'bio', 
        'service_area',
        'total_bookings',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_bookings' => 'integer',
    ];

    /**
     * URL publik foto profil beautician.
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => \App\Support\ImageHelper::url(
                $this->photo ? (self::PHOTO_DIRECTORY . '/' . ltrim($this->photo, '/')) : null,
                'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'Beautician') . '&background=f45472&color=fff'
            ),
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
     * Relasi ke jadwal kerja mingguan (dipakai untuk auto-assign booking).
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(BeauticiansSchedules::class, 'beautician_id');
    }
}
