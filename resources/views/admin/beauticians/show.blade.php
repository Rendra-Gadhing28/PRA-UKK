<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.beauticians.index') }}" class="p-2 rounded-full bg-white border border-rose-200 text-gray-600 hover:text-rose-600 hover:bg-rose-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    Profil & Kinerja — {{ $beautician->name }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Statistik reservasi yang ditangani, status penugasan, dan riwayat booking</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- PROFILE HEADER CARD --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100 flex flex-col md:flex-row items-center md:items-start gap-8">
                <div class="relative shrink-0">
                    <img src="{{ $beautician->photo_url }}" alt="{{ $beautician->name }}" 
                         class="w-32 h-32 rounded-full object-cover border-4 border-rose-100 shadow-md">
                    <span class="absolute bottom-2 right-2 w-6 h-6 rounded-full border-4 border-white {{ $beautician->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                </div>

                <div class="flex-1 text-center md:text-left">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-3">
                        <div>
                            <h3 class="text-2xl font-bold font-headline text-gray-900">{{ $beautician->name }}</h3>
                            <p class="text-xs text-[#f45472] font-semibold mt-0.5">Beautician Specialist Salon Yalia</p>
                        </div>

                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.beauticians.edit', $beautician->id) }}" class="px-4 py-2 rounded-full bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-all">
                                Edit Profil
                            </a>
                            <form method="POST" action="{{ route('admin.beauticians.toggle-active', $beautician->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ $beautician->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200' }}">
                                    {{ $beautician->is_active ? '● Aktif Bertugas' : '○ Off Penugasan' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-xs text-gray-600 mb-4 max-w-2xl leading-relaxed">
                        "{{ $beautician->bio }}"
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100 text-xs">
                        <div>
                            <span class="text-gray-400 block">No. Telepon:</span>
                            <span class="font-bold text-gray-800">{{ $beautician->phone ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Email Staf:</span>
                            <span class="font-bold text-gray-800">{{ $beautician->email ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Rating Pelanggan:</span>
                            <span class="font-bold text-amber-500">⭐ {{ number_format($avgRating, 1) }} / 5.0</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RECENT BOOKINGS TABLE ASSIGNED TO BEAUTICIAN --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4 font-headline flex items-center justify-between">
                    <span>Riwayat Reservasi Ditangani ({{ $recentBookings->count() }})</span>
                    <span class="text-xs text-rose-600 font-semibold bg-rose-50 px-3 py-1 rounded-full">
                        Total Ditangani: {{ number_format($beautician->bookings_count ?? $beautician->total_bookings) }} Layanan
                    </span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-rose-100">
                                <th class="py-3 px-4">Kode & Tanggal</th>
                                <th class="py-3 px-4">Pelanggan</th>
                                <th class="py-3 px-4">Treatment</th>
                                <th class="py-3 px-4">Total</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($recentBookings as $b)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                <td class="py-3.5 px-4">
                                    <span class="font-mono font-bold text-[#f45472] block">{{ $b->booking_code }}</span>
                                    <span class="text-xs text-gray-500">{{ $b->booking_date ? $b->booking_date->format('d M Y') : '-' }}</span>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="font-bold text-gray-900 block">{{ $b->user?->name ?? 'Guest' }}</span>
                                    <span class="text-xs text-gray-500">{{ $b->user?->phone ?? '-' }}</span>
                                </td>

                                <td class="py-3.5 px-4 text-xs text-gray-700 truncate max-w-xs">
                                    {{ $b->treatments->pluck('name')->join(', ') ?: 'N/A' }}
                                </td>

                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    Rp {{ number_format($b->total_amount, 0, ',', '.') }}
                                </td>

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

                                <td class="py-3.5 px-4 text-center">
                                    <a href="{{ route('admin.bookings.show', $b->id) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-xs font-bold transition-colors">
                                        Detail Booking
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400 text-sm">
                                    Beautician ini belum pernah ditugaskan pada reservasi.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
