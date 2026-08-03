<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $fillable = [
        'type', 
        'booking_id',
        'category', 
        'icon', 
        'title', 
        'description', 
        'amount', 
        'receipt_image', 
        'transaction_date',
        'metadata', 
        'created_by',
    ];

    public function Bookings(){
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    
}
