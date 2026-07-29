<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transactions extends Model
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
}
