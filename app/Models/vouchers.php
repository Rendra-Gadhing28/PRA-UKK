<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vouchers extends Model
{
    protected $fillable = [
        'code', 
        'name', 
        'description', 
        'type', 
        'value', 
        'min_purchase', 
        'max_discount', 
        'valid_from', 
        'valid_until', 
        'quota', 
        'used_count', 
        'is_active'
    ];
}
