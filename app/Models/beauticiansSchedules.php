<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class beauticiansSchedules extends Model
{
    protected $fillable = [
        'beautician_id', 
        'day_of_week', 
        'start_time', 
        'end_time', 
        'is_working'
    ];
}
