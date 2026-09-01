<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar user dengan pencarian.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Toggle status aktif/nonaktif user.
     */
    public function toggleActive(User $user)
    {
        // Cegah admin menonaktifkan dirinya sendiri
        if (auth()->id() === $user->id) {
            ToastHelper::error('Tidak dapat menonaktifkan akun sendiri.');
            return back();
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ToastHelper::success("Akun user {$user->name} berhasil {$status}.");

        return back();
    }

    /**
     * Hapus user beserta avatarnya.
     */
    public function destroy(User $user)
    {
        // Cegah admin menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            ToastHelper::error('Tidak dapat menghapus akun sendiri.');
            return back();
        }

        // Hapus avatar jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $userName = $user->name;
        $user->delete();

        ToastHelper::success("Akun user {$userName} berhasil dihapus permanen.");

        return back();
    }
}
