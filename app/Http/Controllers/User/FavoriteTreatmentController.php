<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Treatments;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteTreatmentController extends Controller
{
    /**
     * Toggle status favorit treatment untuk user yang sedang login.
     */
    public function toggle(Request $request, Treatments $treatment): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu untuk menyimpan perawatan favorit.',
                'redirect' => route('login'),
            ], 401);
        }

        $exists = $user->favoriteTreatments()->where('treatment_id', $treatment->id)->exists();

        if ($exists) {
            $user->favoriteTreatments()->detach($treatment->id);
            $favorited = false;
            $message = 'Perawatan dihapus dari daftar favorit Anda.';
        } else {
            $user->favoriteTreatments()->attach($treatment->id);
            $favorited = true;
            $message = 'Perawatan berhasil ditambahkan ke daftar favorit Anda!';
        }

        $totalFavorites = $user->favoriteTreatments()->count();

        $actionText = $favorited ? 'menambahkan ke' : 'menghapus dari';
        ActivityLogger::log('toggle_favorite', "User {$actionText} favorit treatment '{$treatment->name}'.", $treatment, [
            'favorited' => $favorited,
        ], $user);

        return response()->json([
            'success' => true,
            'favorited' => $favorited,
            'total_favorites' => $totalFavorites,
            'message' => $message,
        ]);
    }
}
