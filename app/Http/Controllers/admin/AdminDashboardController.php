<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Models\Transactions;
use App\Models\Treatments;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDashboardController extends Controller
{
    private const VERSION_KEY = 'admin.dashboard:version';

    /**
     * Invalidasi cache dashboard saat ada perubahan transaksi/booking/treatment.
     */
    public static function bumpDashboardCache(): void
    {
        $version = (int) Cache::get(self::VERSION_KEY, 1);
        Cache::forever(self::VERSION_KEY, $version + 1);
    }

    private function currentVersion(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    public function index(Request $request)
    {
        $version = $this->currentVersion();
        $todayStr = Carbon::now()->toDateString();
        $cacheKey = "admin.dashboard.v{$version}.{$todayStr}";

        // Simpan hasil perhitungan dashboard selama 5 menit (300 detik) / sampai di-bump
        $dashboardData = Cache::remember($cacheKey, 300, function () {
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

            // 5. Data Chart Monitoring Keuangan (7 Hari Terakhir / Rolling 7-Day Window)
            $chartLabels = [];
            $chartIncome = [];
            $chartExpense = [];

            $startDate7Days = $now->copy()->subDays(6)->startOfDay();
            $endDate7Days = $now->copy()->endOfDay();

            $transactionsGrouped = Transactions::query()
                ->whereBetween('transaction_date', [$startDate7Days->toDateString(), $endDate7Days->toDateString()])
                ->selectRaw('DATE(transaction_date) as date, type, SUM(amount) as total')
                ->groupBy(DB::raw('DATE(transaction_date)'), 'type')
                ->get()
                ->groupBy('date');

            $bookingsGrouped = Bookings::query()
                ->whereIn('status', ['completed', 'confirmed'])
                ->whereBetween('booking_date', [$startDate7Days->toDateString(), $endDate7Days->toDateString()])
                ->selectRaw('DATE(booking_date) as date, SUM(total_amount) as total')
                ->groupBy(DB::raw('DATE(booking_date)'))
                ->pluck('total', 'date');

            for ($i = 6; $i >= 0; $i--) {
                $date = $now->copy()->subDays($i);
                $dateStr = $date->toDateString();
                
                // Format label: "16 Agt", "17 Agt", ...
                $chartLabels[] = $date->format('j') . ' ' . $date->translatedFormat('M');

                $txDay = $transactionsGrouped->get($dateStr);
                $dayInc = (float) ($txDay?->where('type', 'income')->sum('total') ?? 0);
                if ($dayInc == 0) {
                    $dayInc = (float) ($bookingsGrouped->get($dateStr) ?? 0);
                }
                $chartIncome[] = $dayInc;

                $dayExp = (float) ($txDay?->where('type', 'expense')->sum('total') ?? 0);
                $chartExpense[] = $dayExp;
            }

            // 6. Top Treatments & Data Diagram Lingkaran Persentase Booking
            $topTreatments = Treatments::withCount(['bookings' => function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()]);
            }])
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

            $totalTreatmentBookings = $topTreatments->sum('bookings_count');

            $treatmentChartLabels = [];
            $treatmentChartData = [];
            $treatmentChartPercentages = [];
            $treatmentChartColors = [
                '#b01f44', // Primary Rose Deep
                '#d23b5b', // Bright Rose Accent
                '#ff8fa4', // Soft Rose Pink
                '#785341', // Rich Warm Brown
                '#946c58', // Light Brown Accent
            ];

            foreach ($topTreatments as $t) {
                $count = $t->bookings_count;
                $percentage = $totalTreatmentBookings > 0 ? round(($count / $totalTreatmentBookings) * 100, 1) : 0;
                
                $treatmentChartLabels[] = $t->name;
                $treatmentChartData[] = $count;
                $treatmentChartPercentages[] = $percentage;
            }

            // 7. Recent Bookings
            $recentBookings = Bookings::with(['user', 'treatments'])
                ->orderBy('created_at', 'desc')
                ->take(6)
                ->get();

            return compact(
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
                'totalTreatmentBookings',
                'treatmentChartLabels',
                'treatmentChartData',
                'treatmentChartPercentages',
                'treatmentChartColors',
                'recentBookings'
            );
        });

        return view('admin.dashboard', $dashboardData);
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

        $bookings = Bookings::with(['user', 'beautician', 'treatments'])
            ->whereBetween('booking_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('booking_date', 'desc')
            ->get();

        $totalRevenue = $bookings->whereIn('status', ['completed', 'confirmed'])->sum('total_amount');
        $fileName = 'Laporan_Keuangan_Yalia_Beauty_' . $now->format('Y_m') . '.csv';

        return response()->streamDownload(function () use ($bookings, $now, $totalRevenue) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk MS Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // === HEADER ATAS LAPORAN ===
            fputcsv($file, ['LAPORAN KEUANGAN & RESERVASI - YALIA BEAUTY SALON']);
            fputcsv($file, ['Alamat Salon: GHV9+F2 Candi, Kabupaten Boyolali, Jawa Tengah | WA: 0822-2702-3362']);
            fputcsv($file, ['Periode Laporan:', $now->translatedFormat('F Y')]);
            fputcsv($file, ['Waktu Diunduh:', $now->translatedFormat('l, d F Y H:i') . ' WIB']);
            fputcsv($file, ['Ringkasan:', 'Total Reservasi: ' . $bookings->count(), 'Total Omset: Rp ' . number_format($totalRevenue, 0, ',', '.')]);
            fputcsv($file, []); // Baris Kosong Pemisah

            // === TABEL DATA & FIELD AKURAT API/DATABASE ===
            fputcsv($file, [
                'No',
                'Kode Booking',
                'Nama Pelanggan',
                'No. Handphone',
                'Terapis / Beautician',
                'Layanan Treatment',
                'Tanggal Booking',
                'Waktu Layanan',
                'Tipe Kunjungan',
                'Total Harga (Rp)',
                'Status Pembayaran',
                'Status Booking'
            ]);

            $no = 1;
            foreach ($bookings as $b) {
                $statusText = is_object($b->status) 
                    ? (method_exists($b->status, 'badgeLabel') ? $b->status->badgeLabel() : $b->status->value) 
                    : (string) $b->status;

                fputcsv($file, [
                    $no++,
                    $b->booking_code,
                    $b->user?->name ?? 'Guest',
                    $b->user?->phone ?? '-',
                    $b->beautician?->name ?? 'Auto Assign',
                    $b->treatments->pluck('name')->join(', ') ?: 'N/A',
                    $b->booking_date ? $b->booking_date->format('Y-m-d') : '-',
                    ($b->time_start ?? '') . ' - ' . ($b->time_end ?? ''),
                    $b->booking_type === 'home' ? 'Home Service' : 'Ke Salon',
                    $b->total_amount,
                    $b->payment_status ? ucfirst($b->payment_status) : 'Lunas',
                    $statusText,
                ]);
            }

            // === BARIS TOTAL SUMMARY ===
            fputcsv($file, []); // Baris Kosong
            fputcsv($file, ['', '', '', '', '', '', '', '', 'TOTAL PEMASUKAN', $totalRevenue, '', '']);

            fclose($file);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
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
