<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notifications extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'booking_id',
        'data',
        'read_at',
        'is_archived',
        'archived_at',
    ];

    protected $casts = [
        'data' => 'array',
        'is_archived' => 'boolean',
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke Booking yang terkait (bisa trashed jika booking di soft-delete).
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Bookings::class, 'booking_id')->withTrashed();
    }

    /**
     * Relasi polymorphic notifiable (misal User atau Admin).
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope notifikasi aktif (belum diarsipkan).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope notifikasi yang sudah diarsipkan.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope notifikasi yang belum dibaca.
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope notifikasi yang sudah dibaca.
     */
    public function scopeRead(Builder $query): Builder
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Tandai notifikasi sebagai dibaca.
     */
    public function markAsRead(): bool
    {
        if (! $this->read_at) {
            $this->read_at = now();

            return $this->save();
        }

        return true;
    }

    /**
     * Arsipkan notifikasi.
     */
    public function archive(): bool
    {
        $this->is_archived = true;
        $this->archived_at = now();
        if (! $this->read_at) {
            $this->read_at = now();
        }

        return $this->save();
    }

    /**
     * Kembalikan notifikasi dari arsip.
     */
    public function unarchive(): bool
    {
        $this->is_archived = false;
        $this->archived_at = null;

        return $this->save();
    }

    /**
     * Helper pembuatan notifikasi reservasi baru untuk admin.
     */
    public static function createForBooking(Bookings $booking, string $title = 'Reservasi Baru Masuk', ?string $message = null): self
    {
        $booking->loadMissing(['user', 'treatments']);
        $userName = $booking->user?->name ?? 'Guest';
        $userPhone = $booking->user?->phone ?? '-';
        $treatmentsList = $booking->treatments->pluck('name')->join(', ');

        $defaultMessage = $message ?? "Reservasi #{$booking->booking_code} ({$userName}) membutuhkan konfirmasi admin.";

        return static::create([
            'type' => 'booking_created',
            'booking_id' => $booking->id,
            'notifiable_type' => User::class,
            'notifiable_id' => $booking->user_id,
            'data' => [
                'title' => $title,
                'message' => $defaultMessage,
                'booking_id' => $booking->id,
                'booking_code' => $booking->booking_code,
                'customer_name' => $userName,
                'customer_phone' => $userPhone,
                'treatments' => $treatmentsList,
                'booking_date' => $booking->booking_date ? $booking->booking_date->format('Y-m-d') : null,
                'time_start' => $booking->time_start,
                'booking_type' => $booking->booking_type,
                'total_amount' => (float) $booking->total_amount,
                'payment_type' => $booking->payment_type,
                'payment_status' => $booking->payment_status,
                'status' => is_object($booking->status) ? $booking->status->value : (string) $booking->status,
            ],
            'is_archived' => false,
        ]);
    }
}
