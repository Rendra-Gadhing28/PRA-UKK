<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategories extends Model
{
    protected $fillable = [
        'name', 
        'icon', 
        'description', 
        'is_active', 
    ];
}
