<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTreatments extends Model
{
    protected $fillable = [
        'booking_id', 
        'treatment_id', 
        'quantity', 
        'price_per_unit',
        'subtotal', 
    ];

    public function booking(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    public function treatment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Treatments::class, 'treatment_id');
    }

    public function Bookings(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->booking();
    }

    public function Treatments(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->treatment();
    }
}
