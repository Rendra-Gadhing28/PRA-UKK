<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Treatments extends Model
{
    protected $fillable = [
        'category_id', 
        'name', 
        'slug', 
        'description', 
        'benefits',
        'price', 
        'duration_minutes', 
        'images', 
        'badge', 
        'is_active', 
        'sort_order', 
        'rating', 
        'rating_count', 
    ];

    public function Categories(){
        return $this->belongsTo(Categories::class, 'category_id');
    }
}
