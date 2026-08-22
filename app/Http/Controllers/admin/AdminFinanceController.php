<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ToastHelper;
use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\ExpenseCategories;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFinanceController extends Controller
{
    /**
     * Tampilkan daftar pengeluaran keuangan admin dengan filter & statistik.
     */
    public function index(Request $request)
    {
        $categories = ExpenseCategories::where('is_active', true)->get();

        $query = Transactions::query()->where('type', 'expense');

        // Filter Kategori
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filter Tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_until')) {
            $query->whereDate('transaction_date', '<=', $request->date_until);
        }

        // Filter Pencarian Judul/Deskripsi
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $expenses = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(8)
            ->withQueryString();

        // Summary Stats
        $monthlyExpense = Transactions::where('type', 'expense')
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        $monthlyIncome = (float) Transactions::where('type', 'income')
            ->whereYear('transaction_date', now()->year)
            ->whereMonth('transaction_date', now()->month)
            ->sum('amount');

        if ($monthlyIncome == 0) {
            $monthlyIncome = (float) Bookings::whereIn('status', ['completed', 'confirmed'])
                ->whereYear('booking_date', now()->year)
                ->whereMonth('booking_date', now()->month)
                ->sum('total_amount');
        }

        $totalScanStruk = Transactions::where('type', 'expense')
            ->whereNotNull('receipt_image')
            ->count();

        $totalTransactionCount = Transactions::where('type', 'expense')->count();

        return view('admin.finances.index', compact(
            'expenses',
            'categories',
            'monthlyExpense',
            'monthlyIncome',
            'totalScanStruk',
            'totalTransactionCount'
        ));
    }

    /**
     * Form tambah pencatatan pengeluaran (Manual & Scan Struk/QR).
     */
    public function create()
    {
        $categories = ExpenseCategories::where('is_active', true)->get();

        return view('admin.finances.create', compact('categories'));
    }

    /**
     * Simpan transaksi pengeluaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'category'         => ['required', 'string', 'max:150'],
            'transaction_date' => ['required', 'date'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'description'      => ['nullable', 'string'],
            'receipt_image'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'items'            => ['nullable', 'array'],
            'items.*.name'     => ['nullable', 'string'],
            'items.*.qty'      => ['nullable', 'numeric', 'min:0'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.subtotal' => ['nullable', 'numeric', 'min:0'],
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $imageName = time() . '_struk_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $receiptPath = $file->storeAs('receipts', $imageName, 'public');
        }

        // Clean items array
        $itemsList = [];
        if (!empty($validated['items'])) {
            foreach ($validated['items'] as $item) {
                if (!empty($item['name']) && (float)($item['subtotal'] ?? 0) > 0) {
                    $itemsList[] = [
                        'name'       => trim($item['name']),
                        'qty'        => (float)($item['qty'] ?? 1),
                        'unit_price' => (float)($item['unit_price'] ?? 0),
                        'subtotal'   => (float)($item['subtotal'] ?? 0),
                    ];
                }
            }
        }

        // Get icon category
        $categoryModel = ExpenseCategories::where('name', $validated['category'])->first();
        $icon = $categoryModel ? $categoryModel->icon : 'receipt';

        Transactions::create([
            'type'             => 'expense',
            'category'         => $validated['category'],
            'icon'             => $icon,
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'amount'           => $validated['amount'],
            'receipt_image'    => $receiptPath,
            'transaction_date' => $validated['transaction_date'],
            'metadata'         => [
                'items' => $itemsList,
                'scanned' => $request->boolean('is_scanned', false),
            ],
            'created_by'       => auth()->id(),
        ]);

        ToastHelper::success("Pengeluaran '{$validated['title']}' senilai Rp " . number_format($validated['amount'], 0, ',', '.') . " berhasil dicatat! 💳");

        return redirect()->route('admin.finances.index');
    }

    /**
     * Hapus transaksi pengeluaran.
     */
    public function destroy(Transactions $finance)
    {
        if ($finance->receipt_image) {
            Storage::disk('public')->delete($finance->receipt_image);
        }

        $title = $finance->title;
        $finance->delete();

        ToastHelper::success("Catatan pengeluaran '{$title}' berhasil dihapus.");

        return redirect()->route('admin.finances.index');
    }
}
