<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi untuk BookingController::uploadPhotoAssign().
 */
class UploadPhotoAssignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'photo_assign' => ['required', 'image', 'mimes:jpeg,jpg,png,webp,gif', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo_assign.required' => 'Pilih foto hasil treatment terlebih dahulu.',
            'photo_assign.image' => 'File harus berupa gambar.',
            'photo_assign.max' => 'Ukuran foto maksimal 5 MB.',
        ];
    }
}
