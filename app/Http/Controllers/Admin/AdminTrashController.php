<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Beauticians;
use App\Models\Bookings;
use App\Models\Expense;
use App\Models\Notifications;
use App\Models\Reviews;
use App\Models\Treatments;
use App\Models\User;
use App\Models\Vouchers;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AdminTrashController extends Controller
{
    /**
     * Map tipe resource ke Model class.
     */
    protected array $modelMap = [
        'users' => User::class,
        'treatments' => Treatments::class,
        'beauticians' => Beauticians::class,
        'bookings' => Bookings::class,
        'vouchers' => Vouchers::class,
        'reviews' => Reviews::class,
        'expenses' => Expense::class,
        'notifications' => Notifications::class,
    ];

    /**
     * Tampilkan Directory Sampah seluruh aplikasi.
     */
    public function index(Request $request)
    {
        $currentType = $request->get('type', 'all');
        $search = trim($request->get('search', ''));

        $counts = [];
        foreach ($this->modelMap as $key => $modelClass) {
            $counts[$key] = $modelClass::onlyTrashed()->count();
        }
        $counts['all'] = array_sum($counts);

        $items = new Collection();

        if ($currentType !== 'all' && isset($this->modelMap[$currentType])) {
            $modelClass = $this->modelMap[$currentType];
            $query = $modelClass::onlyTrashed()->latest('deleted_at');

            if (! empty($search)) {
                $query = $this->applySearch($query, $currentType, $search);
            }

            $results = $query->paginate(15)->withQueryString();
            $paginatedItems = $results;
        } else {
            // Gabungkan semua data dari seluruh model
            foreach ($this->modelMap as $type => $modelClass) {
                $query = $modelClass::onlyTrashed()->latest('deleted_at');
                if (! empty($search)) {
                    $query = $this->applySearch($query, $type, $search);
                }
                foreach ($query->get() as $record) {
                    $record->resource_type = $type;
                    $items->push($record);
                }
            }

            // Sort gabungan berdasarkan deleted_at terbaru
            $sorted = $items->sortByDesc('deleted_at')->values();

            $page = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 15;
            $currentItems = $sorted->slice(($page - 1) * $perPage, $perPage)->values();

            $paginatedItems = new LengthAwarePaginator(
                $currentItems,
                $sorted->count(),
                $perPage,
                $page,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        }

        return view('admin.trash.index', [
            'items' => $paginatedItems,
            'currentType' => $currentType,
            'counts' => $counts,
            'search' => $search,
        ]);
    }

    /**
     * Restore satu item dari tong sampah.
     */
    public function restore(string $type, int $id)
    {
        if (! isset($this->modelMap[$type])) {
            ToastHelper::error('Tipe resource tidak valid.');

            return back();
        }

        $modelClass = $this->modelMap[$type];
        $item = $modelClass::onlyTrashed()->find($id);

        if (! $item) {
            ToastHelper::error('Data tidak ditemukan di tong sampah.');

            return back();
        }

        $item->restore();

        $name = $this->getItemDisplayName($item, $type);
        ActivityLogger::log('restore', "Memulihkan data {$type} '{$name}' dari tong sampah.", $item);

        ToastHelper::success("Data '{$name}' berhasil dipulihkan.");

        return back();
    }

    /**
     * Hapus permanen (force delete) satu item.
     */
    public function forceDelete(string $type, int $id)
    {
        if (! isset($this->modelMap[$type])) {
            ToastHelper::error('Tipe resource tidak valid.');

            return back();
        }

        $modelClass = $this->modelMap[$type];
        $item = $modelClass::onlyTrashed()->find($id);

        if (! $item) {
            ToastHelper::error('Data tidak ditemukan.');

            return back();
        }

        $name = $this->getItemDisplayName($item, $type);

        // Hapus file fisik jika ada (avatar/gambar/foto/struk)
        $this->deleteAttachedFiles($item, $type);

        $item->forceDelete();

        ActivityLogger::log('force_delete', "Menghapus permanen data {$type} '{$name}' dari tong sampah.");

        ToastHelper::success("Data '{$name}' berhasil dihapus permanen.");

        return back();
    }

    /**
     * Restore seluruh item (atau per tipe).
     */
    public function restoreAll(Request $request)
    {
        $type = $request->get('type', 'all');
        $targetModels = ($type !== 'all' && isset($this->modelMap[$type]))
            ? [$type => $this->modelMap[$type]]
            : $this->modelMap;

        $restoredCount = 0;
        foreach ($targetModels as $t => $modelClass) {
            $items = $modelClass::onlyTrashed()->get();
            foreach ($items as $item) {
                $item->restore();
                $restoredCount++;
            }
        }

        ActivityLogger::log('restore', "Memulihkan massal {$restoredCount} data dari tong sampah (Tipe: {$type}).");
        ToastHelper::success("Sebanyak {$restoredCount} data berhasil dipulihkan.");

        return back();
    }

    /**
     * Kosongkan tong sampah (force delete semua).
     */
    public function emptyTrash(Request $request)
    {
        $type = $request->get('type', 'all');
        $targetModels = ($type !== 'all' && isset($this->modelMap[$type]))
            ? [$type => $this->modelMap[$type]]
            : $this->modelMap;

        $deletedCount = 0;
        foreach ($targetModels as $t => $modelClass) {
            $items = $modelClass::onlyTrashed()->get();
            foreach ($items as $item) {
                $this->deleteAttachedFiles($item, $t);
                $item->forceDelete();
                $deletedCount++;
            }
        }

        ActivityLogger::log('force_delete', "Mengosongkan tong sampah sebanyak {$deletedCount} data permanen (Tipe: {$type}).");
        ToastHelper::success("Tong sampah berhasil dikosongkan. {$deletedCount} data dihapus permanen.");

        return back();
    }

    /**
     * Purge otomatis data yang sudah lewat 30 hari.
     */
    public function purgeExpired()
    {
        $purged = ActivityLogger::purgeExpiredTrash();
        $total = array_sum($purged);

        if ($total > 0) {
            ActivityLogger::log('force_delete', "Otomatis menghapus permanen {$total} data sampah kadaluarsa >30 hari.");
            ToastHelper::success("Pembersihan otomatis berhasil! {$total} data >30 hari dihapus permanen.");
        } else {
            ToastHelper::info('Tidak ada data sampah yang melewati masa retensi 30 hari.');
        }

        return back();
    }

    /**
     * Fitur pengujian: Simulasi skip 30 hari pada data tong sampah.
     */
    public function simulateSkip30Days(Request $request)
    {
        $type = $request->get('type');
        $id = $request->get('id');

        $count = ActivityLogger::simulateSkipTrash30Days($type, $id);

        // Jalankan pembersihan otomatis untuk item yang sudah expired
        $purged = ActivityLogger::purgeExpiredTrash();
        $purgedTotal = array_sum($purged);

        ToastHelper::success("Simulasi Skip 30 Hari berhasil! {$count} data sampah dimajukan 31 hari ke belakang, dan {$purgedTotal} data yang telah >30 hari langsung dihapus otomatis.");

        return back();
    }

    /**
     * Helper nama tampilan objek.
     */
    public function getItemDisplayName($item, string $type): string
    {
        return match ($type) {
            'users', 'beauticians' => $item->name ?? 'Tanpa Nama',
            'treatments' => $item->name ?? 'Treatment #'.$item->id,
            'bookings' => 'Reservasi #'.$item->booking_code.' ('.($item->user?->name ?? 'Guest').')',
            'vouchers' => $item->code ?? $item->name ?? 'Voucher #'.$item->id,
            'reviews' => 'Ulasan #'.$item->id.' (Rating: '.$item->rating.')',
            'expenses' => 'Pengeluaran '.$item->merchant.' (Rp '.number_format($item->total_amount ?? 0, 0, ',', '.').')',
            'notifications' => 'Notifikasi #'.$item->id.' ('.($item->data['title'] ?? 'Reservasi').')',
            default => '#'.$item->id,
        };
    }

    /**
     * Hapus file yang terkait saat force delete.
     */
    protected function deleteAttachedFiles($item, string $type): void
    {
        try {
            if ($type === 'users' && ! empty($item->avatar)) {
                if (Storage::disk('public')->exists($item->avatar)) {
                    Storage::disk('public')->delete($item->avatar);
                }
            } elseif ($type === 'treatments' && ! empty($item->images)) {
                if (Storage::disk('public')->exists('treatments/'.$item->images)) {
                    Storage::disk('public')->delete('treatments/'.$item->images);
                }
            } elseif ($type === 'beauticians' && ! empty($item->photo)) {
                if (Storage::disk('public')->exists('beauticians/'.$item->photo)) {
                    Storage::disk('public')->delete('beauticians/'.$item->photo);
                }
            } elseif ($type === 'reviews' && ! empty($item->photo)) {
                if (Storage::disk('public')->exists($item->photo)) {
                    Storage::disk('public')->delete($item->photo);
                }
            } elseif ($type === 'expenses' && ! empty($item->receipt_image_path)) {
                if (Storage::disk('public')->exists($item->receipt_image_path)) {
                    Storage::disk('public')->delete($item->receipt_image_path);
                }
            } elseif ($type === 'bookings') {
                if (! empty($item->payment_proof) && Storage::disk('public')->exists($item->payment_proof)) {
                    Storage::disk('public')->delete($item->payment_proof);
                }
                if (! empty($item->photo_assign) && Storage::disk('public')->exists($item->photo_assign)) {
                    Storage::disk('public')->delete($item->photo_assign);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Helper query search per tipe model.
     */
    protected function applySearch($query, string $type, string $search)
    {
        return match ($type) {
            'users' => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }),
            'treatments' => $query->where('name', 'like', "%{$search}%"),
            'bookings' => $query->where(function ($q) use ($search) {
                $q->where('booking_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    });
            }),
            'beauticians' => $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }),
            'vouchers' => $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            }),
            'reviews' => $query->where('comment', 'like', "%{$search}%"),
            'expenses' => $query->where('merchant', 'like', "%{$search}%"),
            'notifications' => $query->where(function ($q) use ($search) {
                $q->where('data->title', 'like', "%{$search}%")
                    ->orWhere('data->booking_code', 'like', "%{$search}%")
                    ->orWhere('data->customer_name', 'like', "%{$search}%");
            }),
            default => $query,
        };
    }
}
