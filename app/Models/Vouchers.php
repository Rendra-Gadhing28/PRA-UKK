<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Vouchers Model
 *
 * Kolom dari migration asli (create_vouchers_table):
 *   id, code, name, description, type (enum: percentage|fixed),
 *   value, min_purchase, max_discount, valid_from, valid_until,
 *   quota, used_count, is_active, timestamps
 *
 * Kolom tambahan (add_points_event_columns_to_vouchers_table):
 *   points_required, is_event, event_name, image
 */
class Vouchers extends Model
{
    // -------------------------------------------------------------------------
    // Mass Assignment
    // -------------------------------------------------------------------------

    protected $fillable = [
        // Kolom asli
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'valid_from',
        'valid_until',
        'quota',
        'used_count',
        'is_active',
        // Kolom tambahan
        'points_required',
        'is_event',
        'event_name',
        'image',
    ];

    // -------------------------------------------------------------------------
    // Casts
    // -------------------------------------------------------------------------

    protected $casts = [
        // Boolean
        'is_active'       => 'boolean',
        'is_event'        => 'boolean',
        // Integer
        'points_required' => 'integer',
        'quota'           => 'integer',
        'used_count'      => 'integer',
        // Decimal — cast ke string agar tidak hilang presisi float PHP
        'value'           => 'decimal:2',
        'min_purchase'    => 'decimal:2',
        'max_discount'    => 'decimal:2',
        // Date
        'valid_from'      => 'date',
        'valid_until'     => 'date',
    ];

    // -------------------------------------------------------------------------
    // Relations
    // -------------------------------------------------------------------------

    public function userVouchers(): HasMany
    {
        return $this->hasMany(UserVouchers::class, 'voucher_id');
    }

    // -------------------------------------------------------------------------
    // Query Scopes — reusable, bisa di-chain, menggantikan where() berulang
    // -------------------------------------------------------------------------

    /**
     * Hanya voucher aktif dan belum expired.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true)
              ->where('valid_until', '>=', now()->toDateString());
    }

    /**
     * Voucher yang bisa diklaim dengan menukar poin.
     */
    public function scopeRequiresPoints(Builder $query): void
    {
        $query->where('points_required', '>', 0);
    }

    /**
     * Voucher bertipe event khusus.
     */
    public function scopeIsEvent(Builder $query): void
    {
        $query->where('is_event', true);
    }

    /**
     * Full-text search — hanya berjalan jika $term diisi.
     */
    public function scopeSearch(Builder $query, ?string $term): void
    {
        if (! filled($term)) {
            return;
        }

        $like = '%' . trim($term) . '%';

        $query->where(function (Builder $q) use ($like) {
            $q->where('code',        'like', $like)
              ->orWhere('name',       'like', $like)
              ->orWhere('description','like', $like)
              ->orWhere('event_name', 'like', $like);
        });
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Sisa kuota yang belum terpakai.
     * Pemakaian di view: $voucher->remaining_quota
     */
    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->quota - $this->used_count);
    }

    /**
     * Apakah kuota voucher sudah penuh.
     * Pemakaian di service/view: $voucher->is_quota_out
     */
    public function getIsQuotaOutAttribute(): bool
    {
        return $this->used_count >= $this->quota;
    }

    /**
     * Hitung nilai diskon berdasarkan tipe dan nilai pembelian.
     * Pemakaian di checkout: $voucher->calculateDiscount($subtotal)
     *
     * @param  float|int  $purchaseAmount  Nilai pembelian sebelum diskon
     * @return float  Nilai diskon yang akan dikurangi dari total
     */
    public function calculateDiscount(float $purchaseAmount): float
    {
        // Cek minimum pembelian
        if ($this->min_purchase && $purchaseAmount < (float) $this->min_purchase) {
            return 0.0;
        }

        if ($this->type === 'percentage') {
            $discount = $purchaseAmount * ((float) $this->value / 100);

            // Terapkan batas maksimal diskon jika ada
            if ($this->max_discount) {
                $discount = min($discount, (float) $this->max_discount);
            }

            return round($discount, 2);
        }

        // type === 'fixed' — diskon tidak boleh melebihi nilai pembelian
        return min(round((float) $this->value, 2), $purchaseAmount);
    }

    /**
     * Label tipe voucher yang ramah tampilan.
     * Pemakaian di view: $voucher->type_label
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'percentage' => "Diskon {$this->value}%",
            'fixed'      => 'Potongan Rp ' . number_format((float) $this->value, 0, ',', '.'),
            default      => $this->type,
        };
    }
}