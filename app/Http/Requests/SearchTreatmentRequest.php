<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Memvalidasi & mensanitasi parameter pencarian/filter treatment.
 */
class SearchTreatmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50', 'alpha_dash'],
        ];
    }

    /**
     * Kata kunci pencarian yang sudah divalidasi & di-trim.
     */
    public function search(): ?string
    {
        $value = $this->validated('search');

        return is_string($value) ? trim($value) : null;
    }

    /**
     * Slug kategori yang sudah divalidasi.
     */
    public function categorySlug(): ?string
    {
        return $this->validated('category');
    }
}
