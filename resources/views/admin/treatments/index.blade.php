<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-4 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Treatment & Layanan
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola daftar perawatan, kategori, harga, durasi, dan badge visual</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.treatments.create') }}" 
                   class="inline-flex items-center gap-2 px-8 py-4 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-semibold shadow-md transition-all">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Treatment Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- PURE BLADE FILTER SECTION (STRICT TAILWIND 4-MULTIPLIER SPACING) --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.treatments.index') }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                        
                        {{-- Filter Category --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="fas fa-layer-group text-rose-500 text-xs"></i>
                                Kategori Treatment
                            </label>
                            <select name="category_id" class="w-full px-4 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
                                <option value="all" {{ request('category_id', 'all') === 'all' ? 'selected' : '' }}>🌸 Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string)request('category_id') === (string)$cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="fas fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Treatment
                            </label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Nama atau deskripsi treatment..." value="{{ request('search') }}" 
                                       class="w-full pl-12 pr-8 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-400">
                                    <i class="fas fa-magnifying-glass text-xs"></i>
                                </div>
                                @if(request('search'))
                                    <a href="{{ route('admin.treatments.index', array_filter(['category_id' => request('category_id')])) }}" 
                                       class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-600">
                                        <i class="fas fa-xmark text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-4">
                            <button type="submit" class="flex-1 py-4 px-4 rounded-2xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm flex items-center justify-center gap-4">
                                <i class="fas fa-sliders text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.treatments.index') }}" class="py-4 px-4 rounded-2xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center" title="Reset Filter">
                                <i class="fas fa-rotate-left text-xs"></i>
                            </a>
                        </div>

                    </div>

                    {{-- Quick Category Pills --}}
                    <div class="pt-4 flex items-center gap-4 overflow-x-auto pb-4 scrollbar-none">
                        <span class="text-xs font-bold text-gray-400 shrink-0 uppercase tracking-wider">Quick Filter:</span>
                        
                        <a href="{{ route('admin.treatments.index', array_filter(['search' => request('search'), 'category_id' => 'all'])) }}"
                           class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-4 border {{ request('category_id', 'all') === 'all' ? 'bg-rose-500 text-white border-rose-500 shadow-sm' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-rose-50 hover:text-rose-600' }}">
                            <i class="fas fa-spa text-xs"></i>
                            <span>Semua</span>
                        </a>

                        @foreach($categories as $cat)
                            <a href="{{ route('admin.treatments.index', array_filter(['search' => request('search'), 'category_id' => $cat->id])) }}"
                               class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-4 border {{ (string)request('category_id') === (string)$cat->id ? 'bg-rose-500 text-white border-rose-500 shadow-sm' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-rose-50 hover:text-rose-600' }}">
                                <i class="{{ $cat->icon ?? 'fas fa-tag' }} text-xs"></i>
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
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
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $tr->image_url }}" alt="{{ $tr->name }}" 
                                             class="w-12 h-12 rounded-2xl object-cover border border-rose-100 shrink-0">
                                        <div>
                                            <span class="font-bold text-gray-900 block">{{ $tr->name }}</span>
                                            <span class="text-xs text-gray-400 font-mono">{{ $tr->slug }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td class="py-4 px-4">
                                    <span class="px-4 py-2 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100 inline-flex items-center gap-4">
                                        @if($tr->category?->icon)
                                            <i class="{{ $tr->category->icon }} text-xs"></i>
                                        @endif
                                        {{ $tr->category?->name ?? 'Uncategorized' }}
                                    </span>
                                </td>

                                {{-- Harga --}}
                                <td class="py-4 px-4 font-bold text-gray-900">
                                    Rp {{ number_format($tr->price, 0, ',', '.') }}
                                </td>

                                {{-- Durasi --}}
                                <td class="py-4 px-4 text-xs font-semibold text-gray-700">
                                    ⏱️ {{ $tr->duration_minutes }} Menit
                                </td>

                                {{-- Badge --}}
                                <td class="py-4 px-4">
                                    @php
                                        $badgeBg = match($tr->badge) {
                                            'best_seller' => 'bg-amber-100 text-amber-800',
                                            'new' => 'bg-emerald-100 text-emerald-800',
                                            'promo' => 'bg-rose-100 text-rose-800',
                                            default => 'bg-gray-100 text-gray-500',
                                        };
                                    @endphp
                                    <span class="px-4 py-2 rounded-full text-xs font-bold {{ $badgeBg }}">
                                        {{ $tr->badge === 'best_seller' ? 'Best Seller' : ucfirst($tr->badge) }}
                                    </span>
                                </td>

                                {{-- Rating --}}
                                <td class="py-4 px-4 text-xs">
                                    @if((float)$tr->rating === 0.0)
                                        <span class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-700 font-bold text-[10px]">New</span>
                                    @else
                                        <span class="font-bold text-amber-500">⭐ {{ number_format($tr->rating, 1) }}</span>
                                        <span class="text-gray-400">({{ $tr->rating_count }})</span>
                                    @endif
                                </td>

                                {{-- Status Aktif --}}
                                <td class="py-4 px-4">
                                    <form method="POST" action="{{ route('admin.treatments.toggle-active', $tr->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ $tr->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-rose-100 text-rose-700 hover:bg-rose-200' }}">
                                            {{ $tr->is_active ? '● Aktif' : '○ Nonaktif' }}
                                        </button>
                                    </form>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-4 px-4 text-center">
                                    <div class="flex items-center justify-center gap-4">
                                        <a href="{{ route('admin.treatments.edit', $tr->id) }}" 
                                           class="px-4 py-2 rounded-2xl bg-gray-100 hover:bg-rose-50 hover:text-rose-600 text-gray-700 text-xs font-bold transition-colors">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.treatments.destroy', $tr->id) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus treatment {{ $tr->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors">
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

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $treatments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
