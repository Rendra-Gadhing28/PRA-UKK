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

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'metadata' => 'array',
    ];

    public function Bookings()
    {
        return $this->belongsTo(Bookings::class, 'booking_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
