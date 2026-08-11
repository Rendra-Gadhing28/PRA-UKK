<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Transactions;
use App\Models\Treatments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        // 1. Pemasukan (Income) Bulan ini vs Bulan Lalu
        $incomeThisMonth = (float) Transactions::where('type', 'income')
            ->whereBetween('transaction_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('amount');
        
        // Fallback jika tidak ada transactions record terpisah, hitung dari Bookings paid/completed
        if ($incomeThisMonth == 0) {
            $incomeThisMonth = (float) Bookings::whereIn('status', ['completed', 'confirmed'])
                ->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
                ->sum('total_amount');
        }

        $incomeLastMonth = (float) Transactions::where('type', 'income')
            ->whereBetween('transaction_date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])
            ->sum('amount');

        if ($incomeLastMonth == 0) {
            $incomeLastMonth = (float) Bookings::whereIn('status', ['completed', 'confirmed'])
                ->whereBetween('booking_date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])
                ->sum('total_amount');
        }

        $incomeGrowth = $this->calculatePercentageChange($incomeLastMonth, $incomeThisMonth);

        // 2. Pengeluaran (Expense) Bulan ini vs Bulan Lalu
        $expenseThisMonth = (float) Transactions::where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('amount');

        $expenseLastMonth = (float) Transactions::where('type', 'expense')
            ->whereBetween('transaction_date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])
            ->sum('amount');

        $expenseGrowth = $this->calculatePercentageChange($expenseLastMonth, $expenseThisMonth);

        // 3. Completed Bookings Bulan ini vs Bulan Lalu
        $completedThisMonth = Bookings::where('status', 'completed')
            ->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->count();

        $completedLastMonth = Bookings::where('status', 'completed')
            ->whereBetween('booking_date', [$lastMonthStart->toDateString(), $lastMonthEnd->toDateString()])
            ->count();

        $completedGrowth = $this->calculatePercentageChange($completedLastMonth, $completedThisMonth);

        // 4. Net Profit
        $netProfitThisMonth = $incomeThisMonth - $expenseThisMonth;
        $netProfitLastMonth = $incomeLastMonth - $expenseLastMonth;
        $netProfitGrowth = $this->calculatePercentageChange($netProfitLastMonth, $netProfitThisMonth);

        // 5. Data Chart (12 Hari Terakhir atau 30 Hari Bulan Ini)
        $chartLabels = [];
        $chartIncome = [];
        $chartExpense = [];

        $daysInMonth = $now->daysInMonth;
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dateStr = $now->copy()->day($i)->toDateString();
            $chartLabels[] = $i . ' ' . $now->translatedFormat('M');

            $dayInc = (float) Transactions::where('type', 'income')->whereDate('transaction_date', $dateStr)->sum('amount');
            if ($dayInc == 0) {
                $dayInc = (float) Bookings::whereIn('status', ['completed', 'confirmed'])->whereDate('booking_date', $dateStr)->sum('total_amount');
            }
            $chartIncome[] = $dayInc;

            $dayExp = (float) Transactions::where('type', 'expense')->whereDate('transaction_date', $dateStr)->sum('amount');
            $chartExpense[] = $dayExp;
        }

        // 6. Top Treatments
        $topTreatments = Treatments::withCount(['bookings' => function ($q) use ($startOfMonth, $endOfMonth) {
            $q->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
        }])
        ->orderBy('bookings_count', 'desc')
        ->take(5)
        ->get();

        // 7. Recent Bookings
        $recentBookings = Bookings::with(['user', 'treatments'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'incomeThisMonth',
            'incomeGrowth',
            'expenseThisMonth',
            'expenseGrowth',
            'completedThisMonth',
            'completedGrowth',
            'netProfitThisMonth',
            'netProfitGrowth',
            'chartLabels',
            'chartIncome',
            'chartExpense',
            'topTreatments',
            'recentBookings'
        ));
    }

    public function exportPdf(Request $request)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $income = (float) Bookings::whereIn('status', ['completed', 'confirmed'])
            ->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('total_amount');

        $expense = (float) Transactions::where('type', 'expense')
            ->whereBetween('transaction_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->sum('amount');

        $bookings = Bookings::with(['user', 'treatments'])
            ->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('booking_date', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.reports.pdf', compact('now', 'income', 'expense', 'bookings'));
        
        return $pdf->download('Laporan_Bulanan_Yalia_Beauty_' . $now->format('Y_m') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        $bookings = Bookings::with(['user', 'treatments'])
            ->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('booking_date', 'desc')
            ->get();

        $fileName = 'Laporan_Bulanan_Yalia_Beauty_' . $now->format('Y_m') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Kode Booking', 'Pelanggan', 'Treatment', 'Tanggal Booking', 'Waktu', 'Tipe', 'Total Harga', 'Status']);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->booking_code,
                    $b->user?->name ?? 'Guest',
                    $b->treatments->pluck('name')->join(', ') ?: 'N/A',
                    $b->booking_date ? $b->booking_date->format('Y-m-d') : '-',
                    ($b->time_start ?? '') . ' - ' . ($b->time_end ?? ''),
                    $b->booking_type,
                    $b->total_amount,
                    $b->status,
                ]);
            }

            fclose($file);
        };


        return response()->stream($callback, 200, $headers);
    }

    private function calculatePercentageChange(float $previous, float $current): array
    {
        if ($previous == 0) {
            $percentage = $current > 0 ? 100 : 0;
            return [
                'value' => $percentage,
                'is_positive' => $current >= 0,
                'formatted' => '+' . number_format($percentage, 1) . '%'
            ];
        }

        $change = (($current - $previous) / $previous) * 100;
        $isPositive = $change >= 0;

        return [
            'value' => round(abs($change), 1),
            'is_positive' => $isPositive,
            'formatted' => ($isPositive ? '+' : '-') . number_format(abs($change), 1) . '%'
        ];
    }
}
