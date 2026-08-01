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

}
