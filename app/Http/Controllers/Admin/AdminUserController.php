<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar user dengan pencarian & tab aktif/tong sampah.
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'active');
        $query = User::query();

        if ($tab === 'trashed') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy($tab === 'trashed' ? 'deleted_at' : 'created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $activeCount = User::withoutTrashed()->count();
        $trashedCount = User::onlyTrashed()->count();

        return view('admin.users.index', compact('users', 'tab', 'activeCount', 'trashedCount'));
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

        if (! $user->is_active) {
            try {
                DB::table('sessions')->where('user_id', $user->id)->delete();
            } catch (\Throwable $e) {
                // Ignore if sessions table not in use
            }
        }

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLogger::log('toggle_status', "Mengubah status user '{$user->name}' menjadi {$status}.", $user, [
            'is_active' => $user->is_active,
        ]);

        ToastHelper::success("Akun user {$user->name} berhasil {$status}.");

        return back();
    }

    /**
     * Soft delete user (Pindahkan ke tong sampah).
     */
    public function destroy(User $user)
    {
        // Cegah admin menghapus dirinya sendiri
        if (auth()->id() === $user->id) {
            ToastHelper::error('Tidak dapat menghapus akun sendiri.');

            return back();
        }

        $userName = $user->name;
        $user->delete();

        // Invalidate user session
        try {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Throwable $e) {
        }

        ActivityLogger::log('delete', "Memindahkan user '{$userName}' ke tong sampah (soft delete).", $user);

        ToastHelper::success("Akun user {$userName} berhasil dipindahkan ke tong sampah.");

        return back();
    }

    /**
     * Pulihkan user dari tong sampah.
     */
    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();

        ActivityLogger::log('restore', "Memulihkan user '{$user->name}' dari tong sampah.", $user);

        ToastHelper::success("Akun user {$user->name} berhasil dipulihkan.");

        return back();
    }

    /**
     * Hapus permanen user beserta avatar & session.
     */
    public function forceDelete(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $userName = $user->name;

        // Hapus file avatar jika ada
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        try {
            DB::table('sessions')->where('user_id', $user->id)->delete();
        } catch (\Throwable $e) {
        }

        $user->forceDelete();

        ActivityLogger::log('force_delete', "Menghapus permanen akun user '{$userName}'.");

        ToastHelper::success("Akun user {$userName} berhasil dihapus permanen.");

        return back();
    }

    /**
     * Simulasi loncat 30 hari di tong sampah user.
     */
    public function simulateSkip30Days(Request $request)
    {
        $id = $request->input('user_id');
        $count = ActivityLogger::simulateSkipTrash30Days('users', $id ? (int) $id : null);

        $purged = ActivityLogger::purgeExpiredTrash();
        $purgedCount = $purged['users'] ?? 0;

        ToastHelper::success("Simulasi Skip 30 Hari berhasil! {$count} user di tong sampah dimajukan 31 hari ke belakang. {$purgedCount} user yang >30 hari langsung dibersihkan permanen.");

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

        ActivityLogger::log('update', "Mereset membership seluruh {$affectedCount} customer kembali ke Regular.");

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

        ActivityLogger::log('update', "Simulasi 90 hari reset membership pada {$resetCount} user.");

        ToastHelper::warning("Simulasi 90 Hari (After 90D) berhasil dijalankan! {$resetCount} customer telah diproses reset kuartalan ke Regular.");

        return back();
    }
}
