<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_id',
        'item_name',
        'qty',
        'unit_price',
        'subtotal',
        'category',
    ];

    protected $casts = [
        'qty' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relationship: ExpenseItem belongs to an Expense.
     */
    public function expense(): BelongsTo
    {
        return $this->belongsTo(Expense::class);
    }
}
