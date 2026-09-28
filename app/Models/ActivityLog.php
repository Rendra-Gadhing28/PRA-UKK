<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'is_archived',
        'archived_at',
    ];

    protected $casts = [
        'properties' => 'array',
        'is_archived' => 'boolean',
        'archived_at' => 'datetime',
    ];

    /**
     * Relasi ke User yang melakukan aktivitas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }

    /**
     * Scope log aktif (belum diarsipkan).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    /**
     * Scope log yang sudah diarsipkan.
     */
    public function scopeArchived(Builder $query): Builder
    {
        return $query->where('is_archived', true);
    }

    /**
     * Scope log yang sudah melewati masa retensi 30 hari dan belum diarsip.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('is_archived', false)
            ->where('created_at', '<=', now()->subDays(30));
    }

    /**
     * Arsipkan log.
     */
    public function archive(): bool
    {
        $this->is_archived = true;
        $this->archived_at = now();

        return $this->save();
    }

    /**
     * Batalkan arsip log.
     */
    public function unarchive(): bool
    {
        $this->is_archived = false;
        $this->archived_at = null;

        return $this->save();
    }
}
