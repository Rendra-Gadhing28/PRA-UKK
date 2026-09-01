<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $fillable = [
        'id', 
        'type', 
        'notifiable', 
        'data', 
        'read_at'
    ];
}
