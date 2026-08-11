<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    <span class="w-3 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Treatment & Layanan
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola daftar perawatan, kategori, harga, durasi, dan badge visual</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.treatments.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-semibold shadow-md transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Treatment Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.treatments.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                    
                    {{-- Filter Category --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Kategori Treatment</label>
                        <select name="category_id" class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            <option value="all">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->icon ?? '🌸' }} {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Cari Treatment</label>
                        <input type="text" name="search" placeholder="Nama atau deskripsi treatment..." value="{{ request('search') }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm">
                            Filter
                        </button>
                        <a href="{{ route('admin.treatments.index') }}" class="py-2 px-3 rounded-xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-gray-200 transition-all">
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- TREATMENTS TABLE SECTION --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-rose-100">
                                <th class="py-4 px-4">Treatment</th>
                                <th class="py-4 px-4">Kategori</th>
                                <th class="py-4 px-4">Harga</th>
                                <th class="py-4 px-4">Durasi</th>
                                <th class="py-4 px-4">Badge</th>
                                <th class="py-4 px-4">Rating</th>
                                <th class="py-4 px-4">Status</th>
                                <th class="py-4 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($treatments as $tr)
                            <tr class="hover:bg-rose-50/30 transition-colors">
                                {{-- Treatment & Image --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $tr->image_url }}" alt="{{ $tr->name }}" 
                                             class="w-12 h-12 rounded-2xl object-cover border border-rose-100 shrink-0">
                                        <div>
                                            <span class="font-bold text-gray-900 block">{{ $tr->name }}</span>
                                            <span class="text-xs text-gray-400 font-mono">{{ $tr->slug }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td class="py-3.5 px-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100">
                                        {{ $tr->category?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>

                                {{-- Harga --}}
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    Rp {{ number_format($tr->price, 0, ',', '.') }}
                                </td>

                                {{-- Durasi --}}
                                <td class="py-3.5 px-4 text-xs font-semibold text-gray-700">
                                    ⏱️ {{ $tr->duration_minutes }} Menit
                                </td>

                                {{-- Badge --}}
                                <td class="py-3.5 px-4">
                                    @php
                                        $badgeBg = match($tr->badge) {
                                            'best_seller' => 'bg-amber-100 text-amber-800',
                                            'new' => 'bg-emerald-100 text-emerald-800',
                                            'promo' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-gray-100 text-gray-500',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badgeBg }}">
                                        {{ $tr->badge === 'best_seller' ? 'Best Seller' : ucfirst($tr->badge) }}
                                    </span>
                                </td>

                                {{-- Rating --}}
                                <td class="py-3.5 px-4 text-xs">
                                    @if((float)$tr->rating === 0.0)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">New</span>
                                    @else
                                        <span class="font-bold text-amber-500">⭐ {{ number_format($tr->rating, 1) }}</span>
                                        <span class="text-gray-400">({{ $tr->rating_count }})</span>
                                    @endif
                                </td>

                                {{-- Status Aktif --}}
                                <td class="py-3.5 px-4">
                                    <form method="POST" action="{{ route('admin.treatments.toggle-active', $tr->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ $tr->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200' }}">
                                            {{ $tr->is_active ? '● Aktif' : '○ Nonaktif' }}
                                        </button>
                                    </form>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.treatments.edit', $tr->id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-gray-100 hover:bg-rose-50 hover:text-rose-600 text-gray-700 text-xs font-bold transition-colors">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.treatments.destroy', $tr->id) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus treatment {{ $tr->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400 text-sm">
                                    Tidak ada treatment yang ditemukan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Custom Numbered Pagination Links --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $treatments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
