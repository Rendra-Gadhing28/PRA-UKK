<?php

namespace App\Services\User;

use App\Models\UserVouchers;
use App\Models\Vouchers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserVoucherService
{
    // =========================================================================
    // INDEX / LISTING
    // =========================================================================

    /**
     * Ambil semua data yang dibutuhkan halaman index voucher user.
     *
     * Menggunakan Query Scopes dari model Vouchers agar konsisten
     * dan tidak ada duplikasi kondisi WHERE di service.
     *
     * @return array{
     *     allVouchers: \Illuminate\Support\Collection,
     *     pointVouchers: \Illuminate\Support\Collection,
     *     eventVouchers: \Illuminate\Support\Collection,
     *     myVouchers: \Illuminate\Support\Collection,
     *     claimedVoucherIds: array<int>
     * }
     */
    public function getIndexData(Authenticatable $user, ?string $search = null): array
    {
        // Kolom yang dibutuhkan view — tidak SELECT *
        $columns = [
            'id', 'code', 'name', 'description', 'type', 'value',
            'min_purchase', 'max_discount', 'event_name', 'is_event',
            'points_required', 'quota', 'used_count', 'valid_from', 'valid_until',
        ];

        // ID voucher yang sudah diklaim — untuk disable tombol di view
        $claimedVoucherIds = UserVouchers::where('user_id', $user->id)
            ->pluck('voucher_id')
            ->all();

        // Base query menggunakan scope Active + scope Search
        $base = Vouchers::active()->search($search);

        $allVouchers = (clone $base)
            ->select($columns)
            ->orderByDesc('id')
            ->get();

        $pointVouchers = (clone $base)
            ->select($columns)
            ->requiresPoints()                // scope: WHERE points_required > 0
            ->orderBy('points_required')
            ->get();

        $eventVouchers = (clone $base)
            ->select($columns)
            ->isEvent()                       // scope: WHERE is_event = 1
            ->orderByDesc('id')
            ->get();

        // Eager-load relasi 'voucher' dengan kolom spesifik — eliminasi N+1
        $myVouchers = UserVouchers::with([
                'voucher:id,code,name,description,type,value,is_event,points_required,valid_until',
            ])
            ->where('user_id', $user->id)
            ->select('id', 'user_id', 'voucher_id', 'is_used', 'created_at')
            ->orderBy('is_used')
            ->orderByDesc('id')
            ->get();

        return compact(
            'allVouchers',
            'pointVouchers',
            'eventVouchers',
            'myVouchers',
            'claimedVoucherIds',
        );
    }

    // =========================================================================
    // CLAIM
    // =========================================================================

    /**
     * Proses klaim voucher dengan atomic DB transaction.
     *
     * @return array{success: bool, type: string, message: string}
     */
    public function claim(Authenticatable $user, Vouchers $voucher): array
    {
        // --- Guard: aktif & belum expired ---
        if (! $voucher->is_active || $voucher->valid_until->isPast()) {
            return $this->result(false, 'expired',
                "Voucher '{$voucher->code}' tidak aktif atau sudah kadaluarsa."
            );
        }

        // --- Guard: kuota (pakai accessor is_quota_out dari model) ---
        if ($voucher->is_quota_out) {
            return $this->result(false, 'quota',
                "Maaf, kuota voucher '{$voucher->code}' sudah habis."
            );
        }

        // --- Guard: double claim ---
        $alreadyClaimed = UserVouchers::where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyClaimed) {
            return $this->result(false, 'duplicate',
                "Anda sudah pernah mengklaim voucher '{$voucher->code}' ini."
            );
        }

        // --- Guard: saldo poin tidak cukup ---
        if ($voucher->points_required > 0 && $user->total_points < $voucher->points_required) {
            return $this->result(false, 'insufficient_points',
                "Poin PTS Anda ({$user->total_points} PTS) tidak mencukupi "
                . "untuk menukar voucher ini ({$voucher->points_required} PTS)."
            );
        }

        // ---------------------------------------------------------------
        // Atomic transaction — potong poin + buat record + increment
        // used_count dalam satu unit kerja. Jika gagal → semua rollback,
        // tidak ada data korup (poin terpotong tapi voucher tidak masuk).
        // ---------------------------------------------------------------
        try {
            DB::transaction(function () use ($user, $voucher) {
                if ($voucher->points_required > 0) {
                    // Decrement langsung di DB — thread-safe, bukan lewat object
                    $user->decrement('total_points', $voucher->points_required);
                }

                UserVouchers::create([
                    'user_id'    => $user->id,
                    'voucher_id' => $voucher->id,
                    'is_used'    => false,
                ]);

                // Increment used_count — wajib agar kuota berkurang
                $voucher->increment('used_count');
            });
        } catch (\Throwable $e) {
            Log::error('VoucherClaimFailed', [
                'user_id'    => $user->id,
                'voucher_id' => $voucher->id,
                'error'      => $e->getMessage(),
            ]);

            return $this->result(false, 'server_error',
                'Terjadi kesalahan sistem saat memproses klaim. Silakan coba lagi.'
            );
        }

        // --- Pesan sukses sesuai tipe klaim ---
        if ($voucher->points_required > 0) {
            $message = "Berhasil menukarkan {$voucher->points_required} PTS "
                     . "dengan voucher '{$voucher->code}'! 🎉";
            $type    = 'points';
        } elseif ($voucher->is_event) {
            $message = "Selamat! Voucher Event '{$voucher->name}' "
                     . "(Kode: {$voucher->code}) berhasil diklaim! 🎁";
            $type    = 'event';
        } else {
            $message = "Voucher '{$voucher->code}' berhasil diklaim "
                     . "dan tersimpan di akun Anda! 🎟️";
            $type    = 'regular';
        }

        return $this->result(true, $type, $message);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /** @return array{success: bool, type: string, message: string} */
    private function result(bool $success, string $type, string $message): array
    {
        return compact('success', 'type', 'message');
    }
}