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
        'points_required',
        'is_event',
        'event_name',
        'valid_from', 
        'valid_until', 
        'quota', 
        'used_count', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_event'  => 'boolean',
        'points_required' => 'integer',
        'quota'     => 'integer',
        'used_count'=> 'integer',
        'valid_from'=> 'date',
        'valid_until'=> 'date',
    ];
}
