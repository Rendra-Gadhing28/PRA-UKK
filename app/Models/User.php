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
        'tier_points',
        'last_tier_reset_at',
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
        'last_tier_reset_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'total_points' => 'integer',
        'tier_points' => 'integer',
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


    // Accessor Avatar URL
    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            return asset('storage/' . $this->avatar);
        }

        if (!empty($this->attributes['avatar_url'])) {
            return $this->attributes['avatar_url'];
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name ?? 'User') . '&background=f45472&color=fff&size=128';
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
        $order = [
            'regular' => 0,
            'silver' => 1, 
            'gold' => 2, 
            'purple' => 3,
            'platinum' => 3,
            'royal purple' => 3,
        ];
        $current = strtolower($this->membership_level ?? 'regular');
        $target = strtolower($level);
        return ($order[$current] ?? 0) >= ($order[$target] ?? 0);
    }

    public function getDiscPercen(): int {
        $level = strtolower($this->membership_level ?? 'regular');
        if ($level === 'platinum' || $level === 'royal purple') {
            $level = 'purple';
        }

        if (isset(\App\Support\Membership::TIERS[$level])) {
            $val = (int) \App\Support\Membership::TIERS[$level]['discount_val'];
            if ($val > 0) {
                return $val;
            }
        }

        $tier = \App\Support\Membership::progress($this->tier_points ?? 0);
        return (int) ($tier['current_meta']['discount_val'] ?? 0);
    }

    public function upgradeMembership(){
        $tier = \App\Support\Membership::progress($this->tier_points ?? 0);
        $newLevel = $tier['current'];

        if($this->membership_level !== $newLevel) {
            $this->update(['membership_level' => $newLevel]);
        }
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

    /**
     * Check if current quarter is newer than the quarter of last_tier_reset_at.
     * If yes, reset tier_points to 0.
     */
    public function syncTierReset(): void
    {
        $now = now();
        $currentQuarterStart = $now->copy()->firstOfQuarter()->startOfDay();

        if (is_null($this->last_tier_reset_at) || $this->last_tier_reset_at->lt($currentQuarterStart)) {
            $this->tier_points = 0;
            // Also downgrade membership back to regular upon reset
            $this->membership_level = 'regular';
        }
        
        $this->last_tier_reset_at = $now;
    }

    /**
     * Safely add points, ensuring both total_points and tier_points increment.
     */
    public function addPoints(int $points): void
    {
        $this->syncTierReset();
        $this->total_points += $points;
        $this->tier_points += $points;

        $tier = \App\Support\Membership::progress($this->tier_points);
        $this->membership_level = $tier['current'];

        $this->save();
    }

    public function subtractPoints(int $points): void
    {
        $this->syncTierReset();
        $this->total_points = max(0, $this->total_points - $points);
        $this->tier_points = max(0, $this->tier_points - $points);

        $tier = \App\Support\Membership::progress($this->tier_points);
        $this->membership_level = $tier['current'];

        $this->save();
    }
}
