<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'notes', 
        'cancel_reason', 
        'canceled_at', 
        'version', 
    ];

     protected $casts = [
        'booking_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function beautician(){
        return $this->belongsTo(Beauticians::class, 'beautician_id');
    }

    public function bookingTreatments(){
        return $this->hasMany(BookingTreatments::class, 'booking_id');
    }



    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatments::class, 'booking_treatments')
            ->withPivot(['quantity', 'price_per_unit', 'subtotal']);
    }

      public function review(): HasOne
    {
        return $this->hasOne(Reviews::class);
    }

     public function scopeUpcoming(Builder $query): Builder
    {
        return $query->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->where('booking_date', '>=', today())
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
        return $query->where('status', 'canceled')
            ->orderByDesc('updated_at');
    }
 
    public function scopeOwnedBy(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
