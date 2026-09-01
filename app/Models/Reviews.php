<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $fillable = [
        'booking_id', 
        'user_id', 
        'beautician_id', 
        'rating', 
        'comment', 
        'photo', 
        'is_approved', 
        'admin_reply', 
    ];
    //ambil data Booking dari foreign key booking_id
    public function Bookings(){
        return $this->belongsTo(Bookings::class, 'booking_id');
    }
    //ambil data user dari foreign key user_id
    public function Users(){
        return $this->belongsTo(User::class, 'user_id');
    }
    //ambil data beautician dari foreign key beautician_id
    public function Beauticians(){
        return $this->belongsTo(Beauticians::class, 'beautician_id');
    }
}
