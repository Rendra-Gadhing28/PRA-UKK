<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTreatments extends Model
{
    protected $fillable = [
        'booking_id',
        'treatment_id',
        'quantity',
        'price_per_unit',
        'subtotal',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatments::class, 'treatment_id');
    }

    public function Bookings(): BelongsTo
    {
        return $this->booking();
    }

    public function Treatments(): BelongsTo
    {
        return $this->treatment();
    }
}
