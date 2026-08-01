<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $fillable = [
        'booking_id', 
        'user_id', 
        'beautician_id', 
        'rating', 
        'comment', 
        'photo', 
        'is_approved', 
        'admin_reply', 
    ];
}
