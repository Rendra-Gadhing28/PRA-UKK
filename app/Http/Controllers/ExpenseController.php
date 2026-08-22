<?php

namespace App\Http\Controllers;

use App\Helpers\ToastHelper;
use App\Models\Expense;
use App\Models\ExpenseItem;
use App\Services\ReceiptScannerService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    protected ReceiptScannerService $scannerService;

    public function __construct(ReceiptScannerService $scannerService)
    {
        $this->scannerService = $scannerService;
    }

    /**
     * Tampilkan halaman utama tracker pengeluaran & daftar transaksi.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        $expenses = Expense::with('items')
            ->where('user_id', $userId)
            ->latest('transaction_date')
            ->paginate(10);

        $totalSpendingThisMonth = Expense::where('user_id', $userId)
            ->whereMonth('transaction_date', date('m'))
            ->whereYear('transaction_date', date('Y'))
            ->sum('total_amount');

        $totalTransactionsCount = Expense::where('user_id', $userId)->count();

        return view('expenses.index', compact(
            'expenses',
            'totalSpendingThisMonth',
            'totalTransactionsCount'
        ));
    }

    /**
     * Scan gambar struk via Gemini API dan kembalikan JSON hasil parsing.
     */
    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'receipt' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // Max 5MB
        ], [
            'receipt.required' => 'Silakan pilih file foto struk belanja.',
            'receipt.image'    => 'File harus berupa gambar.',
            'receipt.mimes'    => 'Format gambar harus berupa JPG, JPEG, PNG, atau WEBP.',
            'receipt.max'      => 'Ukuran gambar maksimal adalah 5 MB.',
        ]);

        try {
            $file = $request->file('receipt');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Simpan gambar di folder temporary
            $tempRelativePath = $file->storeAs('temp/receipts', $filename, 'public');
            $absolutePath = storage_path('app/public/' . $tempRelativePath);

            // Ekstrak data struk menggunakan LLM Gemini
            $parsedData = $this->scannerService->parseReceipt($absolutePath);

            return response()->json([
                'success'   => true,
                'message'   => 'Struk belanja berhasil di-scan oleh AI.',
                'temp_path' => $tempRelativePath,
                'image_url' => asset('storage/' . $tempRelativePath),
                'data'      => $parsedData,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses struk belanja: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Simpan transaksi pengeluaran dan item belanja terverifikasi ke database.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'merchant'         => ['required', 'string', 'max:255'],
            'branch'           => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'total_amount'     => ['required', 'numeric', 'min:0'],
            'payment_method'   => ['nullable', 'string', 'max:100'],
            'temp_path'        => ['nullable', 'string'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.item_name'  => ['required', 'string', 'max:255'],
            'items.*.qty'        => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.subtotal'   => ['required', 'numeric', 'min:0'],
            'items.*.category'   => ['required', 'string', 'max:100'],
        ]);

        try {
            $permanentPath = null;

            // Pindahkan file temporary ke folder permanen jika ada
            if (! empty($validated['temp_path']) && Storage::disk('public')->exists($validated['temp_path'])) {
                $filename = basename($validated['temp_path']);
                $permanentPath = 'expenses/receipts/' . $filename;
                Storage::disk('public')->move($validated['temp_path'], $permanentPath);
            }

            DB::transaction(function () use ($validated, $permanentPath) {
                /** @var Expense $expense */
                $expense = Expense::create([
                    'user_id'            => Auth::id(),
                    'merchant'           => $validated['merchant'],
                    'branch'             => $validated['branch'] ?? null,
                    'receipt_image_path' => $permanentPath,
                    'transaction_date'   => $validated['transaction_date'],
                    'total_amount'       => $validated['total_amount'],
                    'payment_method'     => $validated['payment_method'] ?? 'Cash',
                ]);

                foreach ($validated['items'] as $item) {
                    ExpenseItem::create([
                        'expense_id' => $expense->id,
                        'item_name'  => $item['item_name'],
                        'qty'        => $item['qty'],
                        'unit_price' => $item['unit_price'],
                        'subtotal'   => $item['subtotal'],
                        'category'   => $item['category'] ?? 'Kebutuhan',
                    ]);
                }
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi pengeluaran berhasil disimpan ke database.',
                    'redirect_url' => route('expenses.index'),
                ]);
            }

            ToastHelper::success('Transaksi pengeluaran berhasil disimpan.');
            return redirect()->route('expenses.index');

        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan transaksi: ' . $e->getMessage(),
                ], 500);
            }

            ToastHelper::error('Gagal menyimpan transaksi: ' . $e->getMessage());
            return back()->withInput();
        }
    }
}
