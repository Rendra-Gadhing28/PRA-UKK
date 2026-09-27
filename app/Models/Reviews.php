<?php

namespace App\Models;

use App\Support\ImageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reviews extends Model
{
    protected $fillable = [
        'booking_id',
        'user_id',
        'beautician_id',
        'rating',
        'beautician_rating',
        'beautician_tags',
        'comment',
        'photo',
        'is_approved',
        'admin_reply',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'rating' => 'integer',
        'beautician_rating' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    public function Bookings(): BelongsTo
    {
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function beautician(): BelongsTo
    {
        return $this->belongsTo(Beauticians::class, 'beautician_id');
    }

    public function Beauticians(): BelongsTo
    {
        return $this->belongsTo(Beauticians::class, 'beautician_id');
    }

    protected function photoUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->photo ? ImageHelper::url($this->photo) : null,
        );
    }
}
