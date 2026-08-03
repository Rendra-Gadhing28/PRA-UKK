<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserVouchers extends Model
{
    protected $fillable = [
        'user_id', 
        'voucher_id', 
        'booking_id', 
        'is_used', 
        'used_at', 
    ];

    public function Users(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function Vouchers(){
        return $this->belongsTo(Vouchers::class, 'voucher_id');
    }

    public function Bookings(){
        return $this->belongsTo(Bookings::class, 'booking_id');
    }
}
