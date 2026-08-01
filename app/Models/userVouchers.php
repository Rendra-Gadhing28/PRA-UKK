<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVouchers extends Model
{
    protected $fillable = [
        'user_id', 
        'voucher_id', 
        'booking_id', 
        'is_used', 
        'used_at', 
    ];
}
