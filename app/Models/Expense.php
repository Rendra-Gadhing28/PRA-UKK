<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'merchant',
        'branch',
        'receipt_image_path',
        'transaction_date',
        'total_amount',
        'payment_method',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Relationship: Expense belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Expense has many ExpenseItems.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ExpenseItem::class);
    }
}
