<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-1.5 h-7 bg-gradient-to-b from-[#b01f44] to-[#f45472] rounded-full inline-block shadow-[0_2px_10px_rgba(244,84,114,0.45)]"></span>
                    Tong Sampah Sistem (Trash)
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data terhapus (Soft Delete). Data disimpan selama 30 hari sebelum dibersihkan otomatis secara permanen.</p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                {{-- Tombol Purge Expired >30 Hari --}}
                <form action="{{ route('admin.trash.purge-expired') }}" method="POST"
                      onsubmit="return confirm('Hapus permanen seluruh data sampah yang sudah melewati batas retensi 30 hari?');">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2.5 bg-rose-50 text-[#b01f44] border border-rose-200 text-xs font-bold rounded-xl hover:bg-rose-100 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-broom text-[#b01f44]"></i>
                        Bersihkan Sampah >30H
                    </button>
                </form>

                {{-- Pulihkan Semua --}}
                <form action="{{ route('admin.trash.restore-all', ['type' => $currentType]) }}" method="POST"
                      onsubmit="return confirm('Pulihkan seluruh data sampah (Kategori: {{ strtoupper($currentType) }})?');">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2.5 bg-emerald-600 text-white text-xs font-bold rounded-xl hover:bg-emerald-700 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-rotate-left"></i>
                        Pulihkan Semua
                    </button>
                </form>

                {{-- Kosongkan Sampah --}}
                <form action="{{ route('admin.trash.empty-trash', ['type' => $currentType]) }}" method="POST"
                      onsubmit="return confirm('PERINGATAN: Kosongkan seluruh data sampah (Kategori: {{ strtoupper($currentType) }})? Tindakan ini tidak dapat dibatalkan!');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="px-4 py-2.5 bg-red-600 text-white text-xs font-bold rounded-xl hover:bg-red-700 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-trash-can"></i>
                        Kosongkan Sampah
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- CATEGORY TABS --}}
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-rose-100">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                    @php
                        $types = [
                            'all' => ['label' => 'Semua', 'icon' => 'fa-layer-group'],
                            'users' => ['label' => 'User', 'icon' => 'fa-users'],
                            'treatments' => ['label' => 'Treatments', 'icon' => 'fa-spa'],
                            'beauticians' => ['label' => 'Beauticians', 'icon' => 'fa-user-nurse'],
                            'bookings' => ['label' => 'Reservasi', 'icon' => 'fa-calendar-check'],
                            'vouchers' => ['label' => 'Vouchers', 'icon' => 'fa-ticket'],
                            'reviews' => ['label' => 'Ulasan', 'icon' => 'fa-comments'],
                            'expenses' => ['label' => 'Pengeluaran', 'icon' => 'fa-receipt'],
                        ];
                    @endphp

                    @foreach($types as $key => $meta)
                        <a href="{{ route('admin.trash.index', ['type' => $key, 'search' => request('search')]) }}"
                           class="px-4 py-2.5 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $currentType === $key ? 'bg-[#b01f44] text-white shadow-md shadow-rose-900/10' : 'bg-gray-100 text-gray-700 hover:bg-rose-50 hover:text-[#b01f44]' }}">
                            <i class="fa-solid {{ $meta['icon'] }} text-xs"></i>
                            <span>{{ $meta['label'] }}</span>
                            <span class="px-2 py-0.5 text-[10px] rounded-full {{ $currentType === $key ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">
                                {{ $counts[$key] ?? 0 }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- SEARCH BAR --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.trash.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    <input type="hidden" name="type" value="{{ $currentType }}">

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                            Cari Data di Tong Sampah
                        </label>
                        <input type="text" name="search" value="{{ $search }}" 
                               placeholder="Ketik untuk mencari nama, kode, merchant, email..." 
                               class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] text-gray-800 placeholder-gray-400 bg-gray-50/50">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-all flex justify-center items-center gap-2">
                            <i class="fa-solid fa-filter text-xs"></i>
                            Cari
                        </button>
                        <a href="{{ route('admin.trash.index', ['type' => $currentType]) }}" class="px-3 py-2.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl hover:bg-gray-200 transition-all flex items-center justify-center" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <thead class="bg-gray-50/70 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Kategori & ID</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Informasi Data</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Waktu Dihapus</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Sisa Retensi</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($items as $item)
                                @php
                                    $itemType = $currentType !== 'all' ? $currentType : ($item->resource_type ?? 'unknown');
                                    $categoryLabel = match($itemType) {
                                        'users' => 'User Akun',
                                        'treatments' => 'Treatment',
                                        'beauticians' => 'Beautician',
                                        'bookings' => 'Reservasi',
                                        'vouchers' => 'Voucher',
                                        'reviews' => 'Ulasan',
                                        'expenses' => 'Pengeluaran',
                                        default => ucfirst($itemType),
                                    };

                                    $categoryColor = match($itemType) {
                                        'users' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'treatments' => 'bg-rose-50 text-[#b01f44] border-rose-200',
                                        'beauticians' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'bookings' => 'bg-pink-50 text-pink-700 border-pink-200',
                                        'vouchers' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'reviews' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'expenses' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                        default => 'bg-gray-50 text-gray-700 border-gray-200',
                                    };

                                    $daysRemaining = max(0, 30 - ($item->deleted_at ? (int)$item->deleted_at->diffInDays(now()) : 0));
                                @endphp
                                <tr class="hover:bg-rose-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $categoryColor }}">
                                                {{ $categoryLabel }}
                                            </span>
                                            <span class="text-xs font-mono text-gray-400">#{{ $item->id }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($itemType === 'users')
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs">
                                                    {{ substr($item->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-xs text-gray-900">{{ $item->name }}</p>
                                                    <p class="text-[10px] text-gray-500">{{ $item->email }}</p>
                                                </div>
                                            </div>
                                        @elseif($itemType === 'treatments')
                                            <div>
                                                <p class="font-bold text-xs text-gray-900">{{ $item->name }}</p>
                                                <p class="text-[10px] text-gray-500">Rp {{ number_format((float)$item->price, 0, ',', '.') }} &bull; {{ $item->duration_minutes }} Menit</p>
                                            </div>
                                        @elseif($itemType === 'beauticians')
                                            <div>
                                                <p class="font-bold text-xs text-gray-900">{{ $item->name }}</p>
                                                <p class="text-[10px] text-gray-500">{{ $item->specialization ?? 'Beautician' }} &bull; {{ $item->phone ?? '-' }}</p>
                                            </div>
                                        @elseif($itemType === 'bookings')
                                            <div>
                                                <p class="font-bold text-xs text-gray-900 font-mono">{{ $item->booking_code }}</p>
                                                <p class="text-[10px] text-gray-500">{{ $item->user?->name ?? 'Guest' }} &bull; Rp {{ number_format((float)$item->total_amount, 0, ',', '.') }} &bull; {{ $item->booking_date ? $item->booking_date->format('d/m/Y') : '-' }}</p>
                                            </div>
                                        @elseif($itemType === 'vouchers')
                                            <div>
                                                <p class="font-bold text-xs text-gray-900 font-mono">{{ $item->code }}</p>
                                                <p class="text-[10px] text-gray-500">{{ $item->name }} &bull; Diskon {{ $item->value }}{{ $item->type === 'percentage' ? '%' : ' IDR' }}</p>
                                            </div>
                                        @elseif($itemType === 'reviews')
                                            <div class="max-w-xs truncate">
                                                <p class="font-bold text-xs text-gray-900">Rating: ⭐ {{ $item->rating }}/5</p>
                                                <p class="text-[10px] text-gray-500 truncate">{{ $item->comment ?? 'Tanpa komentar' }}</p>
                                            </div>
                                        @elseif($itemType === 'expenses')
                                            <div>
                                                <p class="font-bold text-xs text-gray-900">{{ $item->merchant }}</p>
                                                <p class="text-[10px] text-gray-500">Total: Rp {{ number_format((float)$item->total_amount, 0, ',', '.') }} &bull; {{ $item->payment_method }}</p>
                                            </div>
                                        @else
                                            <div>
                                                <p class="font-bold text-xs text-gray-900">{{ $item->name ?? $item->title ?? 'Item #'.$item->id }}</p>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-xs font-bold text-gray-900">{{ $item->deleted_at ? $item->deleted_at->format('d/m/Y H:i') : '-' }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $item->deleted_at ? $item->deleted_at->diffForHumans() : '' }}</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold {{ $daysRemaining <= 5 ? 'bg-red-50 text-red-700 border border-red-200 animate-pulse' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                                            {{ $daysRemaining }} Hari lagi
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Pulihkan (Restore) --}}
                                            <form action="{{ route('admin.trash.restore', ['type' => $itemType, 'id' => $item->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-3 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-xl hover:bg-emerald-100 transition-all flex items-center gap-1.5 shadow-sm"
                                                        title="Pulihkan Data">
                                                    <i class="fa-solid fa-rotate-left text-xs"></i>
                                                    Pulihkan
                                                </button>
                                            </form>

                                            {{-- Hapus Permanen --}}
                                            <form action="{{ route('admin.trash.force-delete', ['type' => $itemType, 'id' => $item->id]) }}" method="POST"
                                                  onsubmit="return confirm('Hapus permanen data ini? Tindakan ini tidak dapat dibatalkan!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-xl transition-all"
                                                        title="Hapus Permanen">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 rounded-full bg-rose-50 flex items-center justify-center text-[#b01f44] mb-3">
                                                <i class="fa-solid fa-trash-can text-2xl"></i>
                                            </div>
                                            <p class="font-bold text-gray-800 text-sm">Tong sampah bersih</p>
                                            <p class="text-xs text-gray-500 mt-1">Tidak ada data sampah pada kategori ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($items->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
