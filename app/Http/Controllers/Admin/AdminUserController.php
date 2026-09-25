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

        $user->is_active = ! $user->is_active;
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

    /**
     * Reset status membership seluruh customer (Silver, Gold, dsb) kembali ke Regular dan tier points ke 0.
     */
    public function resetMemberships(Request $request)
    {
        $affectedCount = User::where('role', '!=', 'admin')->update([
            'tier_points' => 0,
            'membership_level' => 'regular',
            'last_tier_reset_at' => now(),
        ]);

        ToastHelper::success("Reset kuartalan berhasil! Sebanyak {$affectedCount} akun customer telah dikembalikan ke status Regular (0 Tier Points).");

        return back();
    }

    /**
     * Simulasi pengujian lonjakan waktu 90 hari (After 90D) dan langsung mengeksekusi reset membership kuartalan.
     */
    public function simulateQuarterReset(Request $request)
    {
        // Set timestamp reset terakhir ke 91 hari yang lalu untuk simulasi
        User::where('role', '!=', 'admin')->update([
            'last_tier_reset_at' => now()->subDays(91),
        ]);

        $users = User::where('role', '!=', 'admin')->get();
        $resetCount = 0;

        foreach ($users as $user) {
            $user->syncTierReset();
            $user->save();
            $resetCount++;
        }

        ToastHelper::warning("Simulasi 90 Hari (After 90D) berhasil dijalankan! {$resetCount} customer telah diproses reset kuartalan ke Regular.");

        return back();
    }
}
