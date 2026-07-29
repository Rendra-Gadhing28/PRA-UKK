<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'icon', 
        'description', 
        'is_active', 
        'sort_order'
    ];
}
