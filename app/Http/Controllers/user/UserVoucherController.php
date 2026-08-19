<?php

namespace App\Http\Controllers\User;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\UserVouchers;
use App\Models\Vouchers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserVoucherController extends Controller
{
    /**
     * Tampilkan daftar voucher untuk user (Semua, Tukar Points PTS, Event Claim, dan Voucher Saya).
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil ID voucher yang sudah diklaim user
        $claimedVoucherIds = UserVouchers::where('user_id', $user->id)
            ->pluck('voucher_id')
            ->toArray();

        // Query dasar voucher aktif dan belum kadaluarsa
        $baseQuery = Vouchers::query()
            ->where('is_active', true)
            ->where('valid_until', '>=', now()->toDateString());

        // Search jika ada
        if ($request->filled('search')) {
            $search = trim($request->search);
            $baseQuery->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%");
            });
        }

        // Clone query untuk kategori-kategori tab
        $allVouchers = (clone $baseQuery)->orderBy('id', 'desc')->get();
        $pointVouchers = (clone $baseQuery)->where('points_required', '>', 0)->orderBy('points_required', 'asc')->get();
        $eventVouchers = (clone $baseQuery)->where('is_event', true)->orderBy('id', 'desc')->get();

        // Voucher milik user (Voucher Saya)
        $myVouchers = UserVouchers::with('voucher')
            ->where('user_id', $user->id)
            ->orderBy('is_used', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('user.vouchers.index', compact(
            'user',
            'allVouchers',
            'pointVouchers',
            'eventVouchers',
            'myVouchers',
            'claimedVoucherIds'
        ));
    }

    /**
     * Proses klaim voucher (Biasa, Tukar PTS Poin, atau Event Special).
     */
    public function claim(Request $request, Vouchers $voucher)
    {
        $user = Auth::user();

        // 1. Cek status aktif & kadaluarsa
        if (!$voucher->is_active || \Carbon\Carbon::parse($voucher->valid_until)->isPast()) {
            ToastHelper::error("Voucher '{$voucher->code}' tidak aktif atau sudah kadaluarsa.");
            return redirect()->back();
        }

        // 2. Cek sisa kuota
        if ($voucher->used_count >= $voucher->quota) {
            ToastHelper::error("Maaf, kuota voucher '{$voucher->code}' sudah habis.");
            return redirect()->back();
        }

        // 3. Cek apakah sudah pernah klaim
        $alreadyClaimed = UserVouchers::where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyClaimed) {
            ToastHelper::info("Anda sudah pernah mengklaim voucher '{$voucher->code}' ini.");
            return redirect()->back();
        }

        // 4. Jika butuh poin PTS, cek saldo poin user
        if ($voucher->points_required > 0) {
            if ($user->total_points < $voucher->points_required) {
                ToastHelper::error("Poin PTS Anda ({$user->total_points} PTS) tidak mencukupi untuk menukar voucher ini ({$voucher->points_required} PTS).");
                return redirect()->back();
            }

            // Potong poin PTS user
            $user->decrement('total_points', $voucher->points_required);
        }

        // 5. Buat record UserVouchers
        UserVouchers::create([
            'user_id'    => $user->id,
            'voucher_id' => $voucher->id,
            'is_used'    => false,
        ]);

        // 6. Tampilkan pesan sukses sesuai tipe klaim
        if ($voucher->points_required > 0) {
            ToastHelper::success("Berhasil menukarkan {$voucher->points_required} PTS dengan voucher '{$voucher->code}'! 🎉");
        } elseif ($voucher->is_event) {
            ToastHelper::success("Selamat! Voucher Event '{$voucher->name}' (Kode: {$voucher->code}) berhasil diklaim! 🎁");
        } else {
            ToastHelper::success("Voucher '{$voucher->code}' berhasil diklaim dan tersimpan di akun Anda! 🎟️");
        }

        return redirect()->back();
    }
}
