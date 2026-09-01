<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Booking Pelanggan
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola reservasi, filter tanggal, konfirmasi beautician, & verifikasi pembayaran</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.bookings.export.pdf', request()->query()) }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold shadow-xs transition-all">
                    <i class="fa-solid fa-file-pdf text-rose-500 text-xs"></i>
                    <span>Export PDF</span>
                </a>
                <a href="{{ route('admin.bookings.export.excel', request()->query()) }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-xs font-bold shadow-md transition-all">
                    <i class="fa-solid fa-file-excel text-xs"></i>
                    <span>Export Excel (CSV)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 space-y-4">
                <form method="GET" action="{{ route('admin.bookings.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                        
                        {{-- Filter Start Date --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-day text-rose-500 text-xs"></i>
                                Dari Tanggal
                            </label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                   class="w-full px-3 py-2 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Filter End Date --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-calendar-days text-rose-500 text-xs"></i>
                                Sampai Tanggal
                            </label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                   class="w-full px-3 py-2 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Filter Status --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-filter text-rose-500 text-xs"></i>
                                Status Booking
                            </label>
                            <select name="status" class="w-full px-3 py-2 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                                <option value="all">Semua Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="canceled" {{ request('status') === 'canceled' ? 'selected' : '' }}>Canceled</option>
                            </select>
                        </div>

                        {{-- Filter Beautician --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-sparkles text-rose-500 text-xs"></i>
                                Beautician
                            </label>
                            <select name="beautician_id" class="w-full px-3 py-2 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                                <option value="all">Semua Beautician</option>
                                @foreach($beauticians as $b)
                                    @php
                                        $bId = is_object($b) ? $b->id : (is_array($b) ? ($b['id'] ?? '') : $b);
                                        $bName = is_object($b) ? $b->name : (is_array($b) ? ($b['name'] ?? '') : $b);
                                    @endphp
                                    <option value="{{ $bId }}" {{ request('beautician_id') == $bId ? 'selected' : '' }}>{{ $bName }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Booking
                            </label>
                            <input type="text" name="search" placeholder="Kode / Pelanggan..." value="{{ request('search') }}" 
                                   class="w-full px-3 py-2 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-xs flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-sliders text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.bookings.index') }}" class="py-2 px-3 rounded-xl bg-rose-100/70 text-rose-950 text-xs font-semibold hover:bg-rose-200 transition-all flex items-center justify-center" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                            </a>
                        </div>

                    </div>

                    {{-- Quick Status Filter Pills --}}
                    <div class="pt-3 border-t border-rose-50 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                        <span class="text-xs font-bold text-gray-400 shrink-0 uppercase tracking-wider">Quick Status:</span>
                        
                        @php
                            $stCurrent = request('status', 'all');
                        @endphp

                        <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->query(), ['status' => 'all']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'all' ? 'bg-rose-500 text-white border-rose-500 shadow-xs' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50' }}">
                            <i class="fa-solid fa-border-all text-xs"></i>
                            <span>Semua Status</span>
                        </a>

                        <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->query(), ['status' => 'pending']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'pending' ? 'bg-amber-500 text-white border-amber-500 shadow-xs' : 'bg-white text-amber-900 border-gray-200 hover:bg-amber-50' }}">
                            <i class="fa-solid fa-clock text-xs"></i>
                            <span>Pending</span>
                        </a>

                        <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->query(), ['status' => 'confirmed']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'confirmed' ? 'bg-blue-600 text-white border-blue-600 shadow-xs' : 'bg-white text-blue-900 border-gray-200 hover:bg-blue-50' }}">
                            <i class="fa-solid fa-calendar-check text-xs"></i>
                            <span>Confirmed</span>
                        </a>

                        <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->query(), ['status' => 'in_progress']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'in_progress' ? 'bg-purple-600 text-white border-purple-600 shadow-xs' : 'bg-white text-purple-900 border-gray-200 hover:bg-purple-50' }}">
                            <i class="fa-solid fa-spa text-xs"></i>
                            <span>In Progress</span>
                        </a>

                        <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->query(), ['status' => 'completed']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'completed' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-emerald-900 border-gray-200 hover:bg-emerald-50' }}">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            <span>Completed</span>
                        </a>

                        <a href="{{ route('admin.bookings.index', array_filter(array_merge(request()->query(), ['status' => 'canceled']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'canceled' ? 'bg-rose-700 text-white border-rose-700 shadow-xs' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50' }}">
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                            <span>Canceled</span>
                        </a>
                    </div>
                </form>
            </div>

            {{-- BOOKINGS TABLE SECTION --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-rose-950 border-b border-rose-100">
                                <th class="py-3.5 px-5">Kode & Tanggal</th>
                                <th class="py-3.5 px-5">Pelanggan</th>
                                <th class="py-3.5 px-5">Beautician</th>
                                <th class="py-3.5 px-5">Treatment</th>
                                <th class="py-3.5 px-5">Total</th>
                                <th class="py-3.5 px-5">Pembayaran</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50 text-sm">
                            @forelse($bookings as $b)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                {{-- Kode & Tanggal --}}
                                <td class="py-3.5 px-5">
                                    <span class="font-mono font-bold text-[#f45472] block">{{ $b->booking_code }}</span>
                                    <span class="text-xs text-gray-500 inline-flex items-center gap-1 mt-0.5">
                                        <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                        {{ $b->booking_date ? $b->booking_date->format('d M Y') : '-' }} ({{ $b->time_start ?? '' }})
                                    </span>
                                </td>

                                {{-- Pelanggan --}}
                                <td class="py-3.5 px-5">
                                    <span class="font-bold text-gray-900 block leading-tight">{{ $b->user?->name ?? 'Guest' }}</span>
                                    <span class="text-xs text-gray-500 inline-flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-phone text-gray-400 text-xs"></i>
                                        {{ $b->user?->phone ?? '-' }}
                                    </span>
                                </td>

                                {{-- Beautician --}}
                                <td class="py-3.5 px-5">
                                    @if($b->beautician)
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-rose-100 text-[#f45472] font-bold text-xs flex items-center justify-center shrink-0 border border-rose-200">
                                                {{ substr($b->beautician->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs font-semibold text-gray-800 truncate max-w-[120px]">{{ $b->beautician->name }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold border border-amber-200/60">
                                            <i class="fa-solid fa-user-slash text-xs"></i> Belum Ditugaskan
                                        </span>
                                    @endif
                                </td>

                                {{-- Treatment --}}
                                <td class="py-3.5 px-5 text-xs text-gray-700 max-w-xs truncate">
                                    <span class="inline-flex items-center gap-1 text-gray-800 font-medium">
                                        <i class="fa-solid fa-spa text-rose-400 text-xs"></i>
                                        {{ $b->treatments->pluck('name')->join(', ') ?: 'N/A' }}
                                    </span>
                                </td>

                                {{-- Total --}}
                                <td class="py-3.5 px-5 font-extrabold text-[#f45472] tabular-nums">
                                    Rp {{ number_format($b->total_amount, 0, ',', '.') }}
                                </td>

                                {{-- Status Bayar --}}
                                <td class="py-3.5 px-5">
                                    @php
                                        $payBg = match($b->payment_status) {
                                            'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'dp_paid' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'pending' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                                        };
                                        $payIcon = match($b->payment_status) {
                                            'paid' => 'fa-circle-check',
                                            'dp_paid' => 'fa-wallet',
                                            'pending' => 'fa-clock',
                                            default => 'fa-circle-info',
                                        };
                                        $payLabel = match($b->payment_status) {
                                            'paid' => 'Lunas',
                                            'dp_paid' => 'DP 35% (Sisa Rp ' . number_format($b->remaining_amount, 0, ',', '.') . ')',
                                            'pending' => 'Menunggu Pembayaran',
                                            default => ucfirst($b->payment_status),
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $payBg }}">
                                        <i class="fa-solid {{ $payIcon }} text-xs"></i>
                                        <span>{{ $payLabel }}</span>
                                    </span>
                                </td>

                                {{-- Status Booking --}}
                                <td class="py-3.5 px-5">
                                    @php
                                        $stVal = is_object($b->status) ? $b->status->value : (string)$b->status;
                                        $stObj = is_object($b->status) ? $b->status : \App\Enums\BookingStatus::tryFrom($stVal);
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $stObj ? $stObj->badgeClasses() : 'bg-gray-100 text-gray-700' }}">
                                        {{ $stObj ? $stObj->badgeLabel() : ucfirst($stVal) }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('admin.bookings.show', $b->id) }}" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-rose-50 text-[#f45472] hover:bg-rose-100 text-xs font-bold transition-all shadow-2xs"
                                           aria-label="Lihat detail reservasi {{ $b->booking_code }}">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                            <span>Detail</span>
                                        </a>
                                        <a href="{{ route('admin.bookings.receipt', $b->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold transition-all shadow-2xs"
                                           aria-label="Cetak struk reservasi {{ $b->booking_code }}">
                                            <i class="fa-solid fa-receipt text-xs"></i>
                                            <span>Struk</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-rose-50 flex items-center justify-center">
                                            <i class="fa-solid fa-calendar-xmark text-rose-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-500">Tidak ada data booking yang sesuai filter</p>
                                            <p class="text-xs text-gray-400 mt-1">Coba ubah tanggal, status, atau kata kunci pencarian</p>
                                        </div>
                                        <a href="{{ route('admin.bookings.index') }}" 
                                           class="mt-2 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-rose-100 text-rose-950 text-xs font-bold hover:bg-rose-200 transition-colors shadow-2xs">
                                            <i class="fa-solid fa-rotate-left text-xs"></i>
                                            <span>Reset Semua Filter</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-rose-100">
                    {{ $bookings->links() }}
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
