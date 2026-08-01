<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
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
}
