<x-admin-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    <span class="w-3 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Admin Executive Dashboard
                </h2>
                <p class="text-sm text-gray-500 mt-1">Monitoring Performa Salon Yalia Beauty — {{ now()->translatedFormat('F Y') }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.export.pdf') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-sm font-semibold shadow-sm transition-all">
                    <i class="fa-solid fa-file-pdf text-rose-600 text-sm"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('admin.export.excel') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                    <i class="fa-solid fa-file-excel text-white text-sm"></i>
                    <span>Export Excel (CSV)</span>
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Background Decorative Blob --}}
    <div class="fixed top-0 left-0 w-full h-full pointer-events-none overflow-hidden -z-10" aria-hidden="true">
        <div class="absolute -top-32 -left-32 w-96 h-96 bg-rose-100/60 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-32 w-96 h-96 bg-amber-100/50 rounded-full blur-3xl"></div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- 1. INSIGHT CARDS WITH PERCENTAGE DIFFERENCE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                {{-- Card 1: Pemasukan Bulanan --}}
                <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl border border-rose-100 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-500">Pemasukan Bulan Ini</span>
                        <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-[#f45472]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 mb-2">
                        Rp {{ number_format($incomeThisMonth, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold">
                        <span class="px-2 py-0.5 rounded-full {{ $incomeGrowth['is_positive'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $incomeGrowth['formatted'] }}
                        </span>
                        <span class="text-gray-400">vs bulan lalu</span>
                    </div>
                </div>

                {{-- Card 2: Pengeluaran Bulanan --}}
                <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl border border-rose-100 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Pengeluaran Bulan Ini</span>
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 mb-2">
                        Rp {{ number_format($expenseThisMonth, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold">
                        <span class="px-2 py-0.5 rounded-full {{ !$expenseGrowth['is_positive'] ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $expenseGrowth['formatted'] }}
                        </span>
                        <span class="text-gray-400">vs bulan lalu</span>
                    </div>
                </div>

                {{-- Card 3: Booking Selesai --}}
                <div class="bg-white/80 backdrop-blur-md p-6 rounded-3xl border border-rose-100 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-600">Booking Selesai</span>
                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black text-gray-900 mb-2">
                        {{ number_format($completedThisMonth) }} <span class="text-sm font-normal text-gray-500">Reservasi</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold">
                        <span class="px-2 py-0.5 rounded-full {{ $completedGrowth['is_positive'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $completedGrowth['formatted'] }}
                        </span>
                        <span class="text-gray-400">vs bulan lalu</span>
                    </div>
                </div>

                {{-- Card 4: Net Profit --}}
                <div class="bg-gradient-to-br from-[#f45472] to-[#d93856] p-6 rounded-3xl text-white shadow-lg shadow-rose-200">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-100">Estimasi Laba Bersih</span>
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                    <div class="text-2xl font-black mb-2">
                        Rp {{ number_format($netProfitThisMonth, 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-semibold">
                        <span class="px-2 py-0.5 rounded-full bg-white/20 text-white">
                            {{ $netProfitGrowth['formatted'] }}
                        </span>
                        <span class="text-rose-100">vs bulan lalu</span>
                    </div>
                </div>

            </div>

            {{-- 2. MONITORING CHART & DOUGHNUT SECTION --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- Left: 7-Day Finance Line Chart (Luxury Trading Platform Style) --}}
                <div class="lg:col-span-8 bg-white/90 backdrop-blur-xl rounded-3xl p-6 md:p-8 shadow-[0_12px_40px_rgba(176,31,68,0.06)] border border-rose-100/90 flex flex-col justify-between relative overflow-hidden group">
                    {{-- Ambient background glows --}}
                    <div class="absolute -top-20 -left-20 w-64 h-64 rounded-full bg-[#b01f44]/8 blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -right-20 w-64 h-64 rounded-full bg-amber-400/10 blur-3xl pointer-events-none"></div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative z-10">
                        <div>
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100/80 text-[#b01f44] border border-rose-200/80 text-xs font-bold shadow-2xs">
                                    <span class="w-2 h-2 rounded-full bg-[#b01f44] animate-pulse"></span>
                                    <span>Real-Time Financial Analytics</span>
                                </div>
                            </div>
                            <h3 class="text-xl font-extrabold text-[#2b1a1f] tracking-tight font-headline">Grafik Monitoring Keuangan (7 Hari)</h3>
                            <p class="text-xs text-[#594043] font-medium mt-0.5">Tren Pemasukan vs Pengeluaran 7 Hari Terakhir</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-bold text-[#2b1a1f] bg-rose-50/90 border border-rose-200/70 px-4 py-2 rounded-full shadow-2xs backdrop-blur-md">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#b01f44] shadow-2xs"></span>
                                <span>Pemasukan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-400 shadow-2xs"></span>
                                <span>Pengeluaran</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full h-72 z-10">
                        <canvas id="financeChart"></canvas>
                    </div>
                </div>

                {{-- Right: Treatment Booking Percentage Doughnut Chart (4 Cols / ~30% width) --}}
                <div class="lg:col-span-4 bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 font-headline">Persentase Booking</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Proporsi Treatment Paling Banyak Dibooking</p>
                    </div>

                    <div class="relative w-full h-48 my-3 flex items-center justify-center">
                        <canvas id="treatmentDoughnutChart"></canvas>
                        {{-- Center Text overlay for total --}}
                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                            <span class="text-2xl font-black text-[#2b1a1f]">{{ $totalTreatmentBookings }}</span>
                            <span class="text-xs uppercase font-bold text-gray-400 tracking-wider">Total Booking</span>
                        </div>
                    </div>

                    {{-- Percentage breakdown legend --}}
                    <div class="space-y-2 text-xs border-t border-rose-100 pt-3">
                        @foreach($topTreatments as $index => $t)
                        @php
                            $pct = $treatmentChartPercentages[$index] ?? 0;
                            $color = $treatmentChartColors[$index % count($treatmentChartColors)];
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $color }};"></span>
                                <span class="font-medium text-gray-700 truncate">{{ $t->name }}</span>
                            </div>
                            <span class="font-bold text-gray-900 shrink-0 ml-2">{{ $pct }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- 3. BOTTOM GRID: TOP TREATMENTS & RECENT BOOKINGS --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Top Treatments (1 Column) --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 font-headline">Top Treatments</h3>
                        <span class="text-xs font-semibold text-rose-500 bg-rose-50 px-2.5 py-1 rounded-full">Favorit Bulan Ini</span>
                    </div>

                    <div class="space-y-4 flex-1">
                        @php
                            $maxCount = max(1, $topTreatments->first()?->bookings_count ?? 1);
                        @endphp
                        @forelse($topTreatments as $index => $treatment)
                        @php
                            $barWidth = min(100, max(8, round(($treatment->bookings_count / $maxCount) * 100)));
                        @endphp
                        <div class="p-3 rounded-2xl hover:bg-rose-50/50 transition-colors space-y-1.5">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-full bg-rose-100 font-bold text-rose-600 flex items-center justify-center text-sm shrink-0">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-bold text-gray-900 truncate">{{ $treatment->name }}</h4>
                                    <p class="text-xs text-gray-500">Rp {{ number_format($treatment->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="text-sm font-black text-rose-600">{{ $treatment->bookings_count }}</span>
                                    <span class="text-xs text-gray-400 block">booking</span>
                                </div>
                            </div>
                            <div class="w-full bg-rose-100/60 h-1.5 rounded-full overflow-hidden">
                                <div class="bg-[#f45472] h-full rounded-full transition-all duration-500" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-400 text-center py-8">Belum ada data treatment.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Bookings (2 Columns) --}}
                <div class="lg:col-span-2 bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 font-headline">Recent Bookings</h3>
                            <p class="text-xs text-gray-500">Daftar Reservasi Pelanggan Terbaru</p>
                        </div>
                        <a href="{{ route('user.bookings.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700">Lihat Semua →</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs font-bold uppercase tracking-wider text-gray-400 border-b border-gray-100 pb-3">
                                    <th class="py-3 px-2">Pelanggan</th>
                                    <th class="py-3 px-2">Treatment</th>
                                    <th class="py-3 px-2">Tanggal</th>
                                    <th class="py-3 px-2">Total</th>
                                    <th class="py-3 px-2 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-sm">
                                @forelse($recentBookings as $booking)
                                <tr class="hover:bg-rose-50/30 transition-colors">
                                    <td class="py-3 px-2 font-bold text-gray-900">
                                        {{ $booking->user?->name ?? 'Guest User' }}
                                    </td>
                                    <td class="py-3 px-2 text-gray-600">
                                        {{ $booking->treatments->pluck('name')->join(', ') ?: 'N/A' }}
                                    </td>

                                    <td class="py-3 px-2 text-xs text-gray-500">
                                        {{ $booking->booking_date ? $booking->booking_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="py-3 px-2 font-bold text-gray-900">
                                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-2 text-right">
                                        @php
                                            $stVal = is_object($booking->status) ? $booking->status->value : (string)$booking->status;
                                            $badgeBg = match($stVal) {
                                                'completed' => 'bg-emerald-100 text-emerald-700',
                                                'confirmed' => 'bg-blue-100 text-blue-700',
                                                'in_progress' => 'bg-amber-100 text-amber-700',
                                                'canceled' => 'bg-rose-100 text-rose-700',
                                                default => 'bg-gray-100 text-gray-700',
                                            };
                                            $badgeIcon = match($stVal) {
                                                'completed' => 'fa-solid fa-circle-check',
                                                'confirmed' => 'fa-solid fa-calendar-check',
                                                'in_progress' => 'fa-solid fa-rotate fa-spin-pulse',
                                                'canceled' => 'fa-solid fa-circle-xmark',
                                                default => 'fa-solid fa-clock',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $badgeBg }}">
                                            <i class="{{ $badgeIcon }} text-xs"></i>
                                            <span>{{ is_object($booking->status) && method_exists($booking->status, 'badgeLabel') ? $booking->status->badgeLabel() : ucfirst($stVal) }}</span>
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-400 text-sm">Belum ada reservasi terbaru.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- CHART.JS SCRIPT INTEGRATION --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Line Chart 7-Hari Monitoring Keuangan (TradingView / Binance Style)
            const ctxLine = document.getElementById('financeChart').getContext('2d');
            
            // TradingView gradient fill: #9b4054 (Burgundy) fading down to transparent
            const incomeGradient = ctxLine.createLinearGradient(0, 0, 0, 300);
            incomeGradient.addColorStop(0, 'rgba(155, 64, 84, 0.45)');   // Burgundy glow (#9b4054)
            incomeGradient.addColorStop(0.5, 'rgba(155, 64, 84, 0.12)'); 
            incomeGradient.addColorStop(1, 'rgba(155, 64, 84, 0.00)');   // Transparent area fill

            const expenseGradient = ctxLine.createLinearGradient(0, 0, 0, 300);
            expenseGradient.addColorStop(0, 'rgba(251, 191, 36, 0.35)');  // Amber glow
            expenseGradient.addColorStop(0.5, 'rgba(251, 191, 36, 0.08)');
            expenseGradient.addColorStop(1, 'rgba(251, 191, 36, 0.00)');

            // Custom Vertical Hairline Crosshair plugin
            const crosshairPlugin = {
                id: 'crosshair',
                afterDraw: (chart) => {
                    if (chart.tooltip?._active && chart.tooltip._active.length) {
                        const activePoint = chart.tooltip._active[0];
                        const ctx = chart.ctx;
                        const x = activePoint.element.x;
                        const topY = chart.scales.y.top;
                        const bottomY = chart.scales.y.bottom;

                        ctx.save();
                        ctx.beginPath();
                        ctx.setLineDash([4, 4]);
                        ctx.moveTo(x, topY);
                        ctx.lineTo(x, bottomY);
                        ctx.lineWidth = 1;
                        ctx.strokeStyle = 'rgba(176, 31, 68, 0.35)';
                        ctx.stroke();
                        ctx.restore();
                    }
                }
            };

            new Chart(ctxLine, {
                type: 'line',
                plugins: [crosshairPlugin],
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Pemasukan (Rp)',
                            data: @json($chartIncome),
                            borderColor: '#9b4054',
                            backgroundColor: incomeGradient,
                            fill: true,
                            tension: 0.42,
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#9b4054',
                            pointHoverBorderWidth: 3.5,
                            pointHitRadius: 16
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: @json($chartExpense),
                            borderColor: '#fbbf24',
                            backgroundColor: expenseGradient,
                            fill: true,
                            tension: 0.42,
                            borderWidth: 2,
                            borderDash: [5, 5],
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#fbbf24',
                            pointHoverBorderWidth: 3,
                            pointHitRadius: 16
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1200,
                        easing: 'easeOutQuart'
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#2b1a1f',
                            titleColor: '#ffd2e1',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(244, 84, 114, 0.3)',
                            borderWidth: 1,
                            padding: { top: 10, bottom: 10, left: 14, right: 14 },
                            cornerRadius: 12,
                            displayColors: true,
                            boxWidth: 8,
                            boxHeight: 8,
                            boxPadding: 6,
                            usePointStyle: true,
                            titleFont: { family: 'Work Sans, sans-serif', size: 11, weight: '600' },
                            bodyFont: { family: 'Work Sans, sans-serif', size: 13, weight: '700' },
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false, drawBorder: false },
                            ticks: { 
                                font: { family: 'Work Sans, sans-serif', size: 11, weight: '700' }, 
                                color: '#594043' 
                            }
                        },
                        y: {
                            grid: { 
                                color: 'rgba(244, 221, 225, 0.6)',
                                drawBorder: false 
                            },
                            ticks: {
                                font: { family: 'Work Sans, sans-serif', size: 10, weight: '700' },
                                color: '#594043',
                                callback: function(value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + 'M';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });

            // 2. Doughnut Chart Persentase Booking Treatment
            const ctxDoughnut = document.getElementById('treatmentDoughnutChart').getContext('2d');
            
            new Chart(ctxDoughnut, {
                type: 'doughnut',
                data: {
                    labels: @json($treatmentChartLabels),
                    datasets: [{
                        data: @json($treatmentChartData),
                        backgroundColor: @json($treatmentChartColors),
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#2b1a1f',
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const value = context.parsed;
                                    const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return context.label + ': ' + value + ' booking (' + pct + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout>

