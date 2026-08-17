<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Vouchers;
use Illuminate\Http\Request;

class AdminVoucherController extends Controller
{
    /**
     * Tampilkan daftar voucher admin dengan filter status & pencarian.
     */
    public function index(Request $request)
    {
        $query = Vouchers::query();

        // Filter pencarian berdasarkan kode atau nama
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('valid_until', '>=', now()->toDateString());
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('valid_until', '<', now()->toDateString());
            }
        }

        $vouchers = $query->orderBy('id', 'desc')
            ->paginate(8)
            ->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Form tambah voucher baru.
     */
    public function create()
    {
        return view('admin.vouchers.create');
    }

    /**
     * Simpan voucher baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:100', 'unique:vouchers,code'],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'type'         => ['required', 'in:percentage,fixed'],
            'value'        => ['required', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'valid_from'   => ['required', 'date'],
            'valid_until'  => ['required', 'date', 'after_or_equal:valid_from'],
            'quota'        => ['required', 'integer', 'min:1'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        Vouchers::create([
            'code'         => strtoupper(trim($validated['code'])),
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'value'        => $validated['value'],
            'min_purchase' => $validated['min_purchase'] ?? 0,
            'max_discount' => $validated['max_discount'] ?? null,
            'valid_from'   => $validated['valid_from'],
            'valid_until'  => $validated['valid_until'],
            'quota'        => $validated['quota'],
            'used_count'   => 0,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        ToastHelper::success("Voucher '{$validated['code']}' berhasil ditambahkan! 🎟️");

        return redirect()->route('admin.vouchers.index');
    }

    /**
     * Form edit voucher.
     */
    public function edit(Vouchers $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Update data voucher.
     */
    public function update(Request $request, Vouchers $voucher)
    {
        $validated = $request->validate([
            'code'         => ['required', 'string', 'max:100', 'unique:vouchers,code,' . $voucher->id],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'type'         => ['required', 'in:percentage,fixed'],
            'value'        => ['required', 'numeric', 'min:0'],
            'min_purchase' => ['nullable', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'valid_from'   => ['required', 'date'],
            'valid_until'  => ['required', 'date', 'after_or_equal:valid_from'],
            'quota'        => ['required', 'integer', 'min:1'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $voucher->update([
            'code'         => strtoupper(trim($validated['code'])),
            'name'         => $validated['name'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'value'        => $validated['value'],
            'min_purchase' => $validated['min_purchase'] ?? 0,
            'max_discount' => $validated['max_discount'] ?? null,
            'valid_from'   => $validated['valid_from'],
            'valid_until'  => $validated['valid_until'],
            'quota'        => $validated['quota'],
            'is_active'    => $request->boolean('is_active', true),
        ]);

        ToastHelper::success("Voucher '{$voucher->code}' berhasil diperbarui! ✨");

        return redirect()->route('admin.vouchers.index');
    }

    /**
     * Hapus voucher.
     */
    public function destroy(Vouchers $voucher)
    {
        $code = $voucher->code;
        $voucher->delete();

        ToastHelper::success("Voucher '{$code}' berhasil dihapus.");

        return redirect()->route('admin.vouchers.index');
    }

    /**
     * Toggle status aktif voucher secara instan.
     */
    public function toggleActive(Vouchers $voucher)
    {
        $voucher->is_active = ! $voucher->is_active;
        $voucher->save();

        $statusText = $voucher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ToastHelper::info("Voucher '{$voucher->code}' berhasil {$statusText}.");

        return redirect()->back();
    }
}
