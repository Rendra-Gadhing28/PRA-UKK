<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Bookings extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code', 
        'user_id', 
        'beautician_id', 
        'booking_type', 
        'status', 
        'booking_date', 
        'time_start', 
        'time_end', 

        // Home Service: alamat & koordinat
        'home_address',
        'home_latitude',
        'home_longitude',
        'distance_km',

        'subtotal', 
        'discount_amount', 
        'transport_fee', 
        'total_amount', 
        'payment_method',
        'payment_status',
        'qris_code', 
        'qris_image_url', 
        'payment_proof', 
        'payment_verified_at',
        'payment_verified_by', 

        // Midtrans tracking
        'midtrans_order_id',
        'midtrans_transaction_id',
        'payment_expires_at',

        'notes', 
        'cancel_reason', 
        'canceled_at', 
        'version', 
        'points_added',
        'photo_assign',
    ];

    protected $casts = [
        'status' => \App\Enums\BookingStatus::class,
        'booking_date' => 'date',
        'total_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'transport_fee' => 'decimal:2',
        'home_latitude' => 'decimal:7',
        'home_longitude' => 'decimal:7',
        'distance_km' => 'decimal:2',
        'payment_verified_at' => 'datetime',
        'payment_expires_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Users(): BelongsTo
    {
        return $this->user();
    }

    public function beautician(): BelongsTo
    {
        return $this->belongsTo(Beauticians::class, 'beautician_id');
    }

    public function Beauticians(): BelongsTo
    {
        return $this->beautician();
    }

    public function bookingTreatments(): HasMany
    {
        return $this->hasMany(BookingTreatments::class, 'booking_id');
    }

    public function BookingTreatment(): HasMany
    {
        return $this->bookingTreatments();
    }

    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatments::class, 'booking_treatments', 'booking_id', 'treatment_id')
            ->withPivot(['quantity', 'price_per_unit', 'subtotal']);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Reviews::class, 'booking_id');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->whereDate('booking_date', '>=', today())
            ->orderBy('booking_date')
            ->orderBy('time_start');
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->where('status', 'completed')
            ->orderByDesc('booking_date');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->whereIn('status', ['canceled', 'cancelled'])
            ->orderByDesc('updated_at');
    }

    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Accessor untuk mendapatkan treatment pertama (karena relation bernama treatments)
     */
    public function getTreatmentAttribute(): ?Treatments
    {
        return $this->treatments->first();
    }

    /**
     * Accessor untuk format total harga
     */
    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->total_amount, 0, ',', '.');
    }

    /**
     * Alias accessor untuk start_time -> time_start
     */
    public function getStartTimeAttribute(): ?string
    {
        return $this->attributes['time_start'] ?? null;
    }

    /**
     * Alias accessor untuk end_time -> time_end
     */
    public function getEndTimeAttribute(): ?string
    {
        return $this->attributes['time_end'] ?? null;
    }

    /**
     * Hitung perolehan poin dari transaksi booking.
     * Aturan:
     * - >= Rp 100.000: 100 pts
     * - Rp 50.000 s/d Rp 99.999: 50 pts
     * - < Rp 50.000: 15 pts
     */
    public function calculateEarnedPoints(): int
    {
        $amount = (float) $this->total_amount;
        if ($amount >= 100000) {
            return 100;
        }
        if ($amount >= 50000) {
            return 50;
        }
        return 15;
    }
}
