<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    <span class="w-3 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Booking Pelanggan
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola reservasi, filter tanggal, konfirmasi beautician, & verifikasi pembayaran</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.bookings.export.pdf', request()->query()) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-sm font-semibold shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export PDF
                </a>
                <a href="{{ route('admin.bookings.export.excel', request()->query()) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-semibold shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Export Excel (CSV)
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                    
                    {{-- Filter Start Date --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                    </div>

                    {{-- Filter End Date --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                    </div>

                    {{-- Filter Status --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status Booking</label>
                        <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
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
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Beautician</label>
                        <select name="beautician_id" class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            <option value="all">Semua Beautician</option>
                            @foreach($beauticians as $b)
                                <option value="{{ $b->id }}" {{ request('beautician_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Cari Booking</label>
                        <input type="text" name="search" placeholder="Kode / Pelanggan..." value="{{ request('search') }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm">
                            Filter
                        </button>
                        <a href="{{ route('admin.bookings.index') }}" class="py-2 px-3 rounded-xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-gray-200 transition-all">
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- BOOKINGS TABLE SECTION --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-rose-100">
                                <th class="py-4 px-4">Kode & Tanggal</th>
                                <th class="py-4 px-4">Pelanggan</th>
                                <th class="py-4 px-4">Beautician</th>
                                <th class="py-4 px-4">Treatment</th>
                                <th class="py-4 px-4">Total</th>
                                <th class="py-4 px-4">Pembayaran</th>
                                <th class="py-4 px-4">Status</th>
                                <th class="py-4 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($bookings as $b)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                {{-- Kode & Tanggal --}}
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-[#f45472] block">{{ $b->booking_code }}</span>
                                    <span class="text-xs text-gray-500">{{ $b->booking_date ? $b->booking_date->format('d M Y') : '-' }} ({{ $b->time_start ?? '' }})</span>
                                </td>

                                {{-- Pelanggan --}}
                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-gray-900 block">{{ $b->user?->name ?? 'Guest' }}</span>
                                    <span class="text-xs text-gray-500">{{ $b->user?->phone ?? '-' }}</span>
                                </td>

                                {{-- Beautician --}}
                                <td class="py-3.5 px-4">
                                    @if($b->beautician)
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-rose-100 text-rose-600 font-bold text-xs flex items-center justify-center">
                                                {{ substr($b->beautician->name, 0, 1) }}
                                            </div>
                                            <span class="text-xs font-semibold text-gray-800">{{ $b->beautician->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs italic text-gray-400">Belum Ditentukan</span>
                                    @endif
                                </td>

                                {{-- Treatment --}}
                                <td class="py-3.5 px-4 text-xs text-gray-700 max-w-xs truncate">
                                    {{ $b->treatments->pluck('name')->join(', ') ?: 'N/A' }}
                                </td>

                                {{-- Total --}}
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    Rp {{ number_format($b->total_amount, 0, ',', '.') }}
                                </td>

                                {{-- Status Bayar --}}
                                <td class="py-3.5 px-4">
                                    @php
                                        $payBg = match($b->payment_status) {
                                            'paid' => 'bg-emerald-100 text-emerald-700',
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $payBg }}">
                                        {{ ucfirst($b->payment_status) }}
                                    </span>
                                </td>

                                {{-- Status Booking --}}
                                <td class="py-3.5 px-4">
                                    @php
                                        $statusBg = match($b->status) {
                                            'completed' => 'bg-emerald-100 text-emerald-700',
                                            'confirmed' => 'bg-blue-100 text-blue-700',
                                            'in_progress' => 'bg-amber-100 text-amber-700',
                                            'canceled' => 'bg-rose-100 text-rose-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusBg }}">
                                        {{ ucfirst($b->status) }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.bookings.show', $b->id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold transition-colors">
                                            Detail
                                        </a>
                                        <a href="{{ route('admin.bookings.receipt', $b->id) }}" target="_blank"
                                           class="px-3 py-1.5 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-xs font-bold transition-colors">
                                            Struk
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400 text-sm">
                                    Tidak ada data booking yang sesuai filter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $bookings->links() }}
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
