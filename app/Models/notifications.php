<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class notifications extends Model
{
    protected $fillable = [
        'id', 
        'type', 
        'notifiable', 
        'data', 
        'read_at'
    ];
}
