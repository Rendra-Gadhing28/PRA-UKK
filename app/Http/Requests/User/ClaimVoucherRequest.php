<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form Request untuk endpoint klaim voucher DAN index (search param).
 * Validasi masuk terpusat di sini, bukan di controller atau service.
 */
class ClaimVoucherRequest extends FormRequest
{
    /**
     * Hanya user yang sudah login yang boleh mengakses endpoint ini.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Aturan validasi.
     * — `search` hanya relevan untuk method GET /index.
     * — Tidak ada field tambahan untuk method POST /claim
     *   karena voucher sudah di-resolve via Route Model Binding.
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:100'],
        ];
    }

    /**
     * Pesan error yang ramah pengguna (bahasa Indonesia).
     */
    public function messages(): array
    {
        return [
            'search.max' => 'Kata kunci pencarian maksimal 100 karakter.',
        ];
    }
}
