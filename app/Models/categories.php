<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kategori treatment (Hair, Facial, Nail Art, dll).
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $icon
 * @property string|null $description
 * @property bool $is_active
 * @property int $sort_order
 */
class Categories extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Relasi ke treatments dalam kategori ini.
     */
    public function treatments(): HasMany
    {
        return $this->hasMany(Treatments::class);
    }

    /**
     * Scope: hanya kategori yang aktif, terurut sesuai sort_order.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}