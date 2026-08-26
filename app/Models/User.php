<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    
    use HasFactory, Notifiable;
    //field menampung data sementara
    
        protected $fillable = [
        'name',
        'email',
        'phone', 
        'password', 
        'avatar',
        'address', 
        'latitude',
        'longitude',
        'membership_level',
        'total_points',
        'last_daily_checkin_at',
        'total_bookings', 
        'total_spending', 
        'is_active',
        'role', 
        'email_verified_at',
        'google_id',
        'google_token',
        'google_refresh_token',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'google_token',        // Sembunyikan token
        'google_refresh_token', 
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_daily_checkin_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'total_points' => 'integer',
        'total_bookings' => 'integer',
        'total_spending' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    protected $attributes = [
        'role' => 'user',
        'membership_level' => 'regular',
        'total_points' => 0,
        'total_bookings' => 0,
        'total_spending' => 0,
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // Role and Membership Helpers
    public function getIsAdminAttribute(): bool {
        return $this->role === 'admin';
    }

    public function isAdmin(): bool {
        return $this->role === 'admin';
    }

    public function isUser(): bool {
        return $this->role === 'user';
    }


    public function hasMembership(string $level): bool {
        return $this->membership_level === $level;
    }


    public function hasMinMembership(string $level): bool {
        $levels = [
            'regular' => 0,
            'silver' => 1, 
            'gold' => 2, 
            'platinum' => 3,
        ];
        return ($level[$this->membership_level] ?? 0) >= ($level[$level] ?? 0);
    }

    public function getDiscPercen(): int {
        return match ($this->membership_level) {
            'silver' => 2 ,
            'gold' => 5 ,
            'platinum' => 10,
            default => 0
        };
    }

    public function upgradeMembership(){
        $newLevel = match(true) {
            $this->total_bookings > 20 => 'platinum',
            $this->total_bookings > 10 => 'gold', 
            $this->total_bookings > 5 => 'silver',
            default => 'regular'
        };

        if($this->membership_level !== $newLevel) {
            $this->update(['membership_level' => $newLevel]);
        };
    }

    //Relationship
    public function bookings(){
        return $this->hasMany(bookings::class);
    }

    public function reviews(){
        return $this->hasMany(reviews::class);
    }

    public function vouchers(){
        return $this->belongsToMany(vouchers::class)
        ->withPivot(['is_used', 'used_at', 'booking_id'])
        ->withTimestamps();
    }

    // Helpers Transaction by Admin
    public function createdTransactions(){
        return $this->hasMany(transactions::class, 'created_by');
    }

    public function verifiedPayments(){
        return $this->hasMany(bookings::class, 'payment_verified_by');
    }

    public function scopeAdmin($query){
        return $query->where('role', 'admin');
    }

    public function scopeCustomer($query){
        return $query->where('role', 'user');
    }

    public function scopeActive($query){
        return $query->where('is_active', 'true');
    }

    public function scopeMembership($query, string $level){
        return $query->where('membership_level', $level);
    }

      public function hasGoogleAccount() {
        return !is_null($this->google_id);
    }


}
