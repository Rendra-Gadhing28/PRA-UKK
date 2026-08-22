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
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
                <a href="{{ route('admin.export.excel') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Excel (CSV)
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
                
                {{-- Left: 7-Day Finance Line Chart (8 Cols / ~70% width) --}}
                <div class="lg:col-span-8 bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 font-headline">Grafik Monitoring Keuangan (7 Hari)</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tren Pemasukan vs Pengeluaran 7 Hari Terakhir</p>
                        </div>
                        <div class="flex items-center gap-4 text-xs font-semibold">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-[#b01f44]"></span>
                                <span>Pemasukan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span>Pengeluaran</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative w-full h-72">
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
                            <span class="text-[9px] uppercase font-bold text-gray-400">Total Booking</span>
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
                        @forelse($topTreatments as $index => $treatment)
                        <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-rose-50/50 transition-colors">
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
                                        @endphp
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badgeBg }}">
                                            {{ is_object($booking->status) && method_exists($booking->status, 'badgeLabel') ? $booking->status->badgeLabel() : ucfirst($stVal) }}
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
            // 1. Line Chart 7-Hari Monitoring Keuangan
            const ctxLine = document.getElementById('financeChart').getContext('2d');
            
            // Soft Gradient background (Palette warna dari tailwind.config.js)
            const incomeGradient = ctxLine.createLinearGradient(0, 0, 0, 280);
            incomeGradient.addColorStop(0, 'rgba(176, 31, 68, 0.35)');  // Soft #b01f44 (primary)
            incomeGradient.addColorStop(1, 'rgba(176, 31, 68, 0.01)');

            const expenseGradient = ctxLine.createLinearGradient(0, 0, 0, 280);
            expenseGradient.addColorStop(0, 'rgba(251, 191, 36, 0.30)'); // Soft #fbbf24 (amber)
            expenseGradient.addColorStop(1, 'rgba(251, 191, 36, 0.01)');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [
                        {
                            label: 'Pemasukan (Rp)',
                            data: @json($chartIncome),
                            borderColor: '#b01f44',
                            backgroundColor: incomeGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2.5,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#b01f44',
                            pointHoverBorderWidth: 3,
                            pointHitRadius: 10
                        },
                        {
                            label: 'Pengeluaran (Rp)',
                            data: @json($chartExpense),
                            borderColor: '#fbbf24',
                            backgroundColor: expenseGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            borderDash: [4, 4],
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointHoverBackgroundColor: '#ffffff',
                            pointHoverBorderColor: '#fbbf24',
                            pointHoverBorderWidth: 3,
                            pointHitRadius: 10
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#2b1a1f',
                            padding: 12,
                            titleFont: { size: 12, weight: 'bold' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' }, color: '#594043' }
                        },
                        y: {
                            grid: { color: 'rgba(244, 221, 225, 0.5)' },
                            ticks: {
                                font: { size: 10 },
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

