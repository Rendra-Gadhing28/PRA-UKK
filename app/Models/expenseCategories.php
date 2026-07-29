<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class expenseCategories extends Model
{
    protected $fillable = [
        'name', 
        'icon', 
        'description', 
        'is_active', 
    ];
}
