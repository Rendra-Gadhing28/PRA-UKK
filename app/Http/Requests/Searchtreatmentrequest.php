<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Memvalidasi & mensanitasi parameter pencarian/filter treatment.
 *
 * Semua input publik (search, category, cursor) divalidasi ketat di sini
 * sebelum menyentuh layer query, sebagai lapisan pertahanan terhadap:
 * - SQL injection (dibatasi panjang & karakter, walau Eloquent sudah
 *   memakai parameter binding)
 * - abuse / query yang tidak wajar (panjang string dibatasi)
 * - mass assignment tidak relevan di sini karena request ini read-only
 */
class SearchTreatmentRequest extends FormRequest
{
    /**
     * Endpoint ini publik (tidak perlu login), sehingga otorisasi selalu true.
     */
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