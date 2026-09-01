<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Treatment & Layanan
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola daftar perawatan, kategori, harga, durasi, dan badge visual</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.treatments.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-semibold shadow-md transition-all">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Treatment Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.treatments.index') }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                        
                        {{-- Filter Category --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-layer-group text-rose-500 text-xs"></i>
                                Kategori Treatment
                            </label>
                            <select name="category_id" class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
                                <option value="all" {{ request('category_id', 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (string)request('category_id') === (string)$cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Treatment
                            </label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Nama atau deskripsi treatment..." value="{{ request('search') }}" 
                                       class="w-full pl-10 pr-8 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-400">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </div>
                                @if(request('search'))
                                    <a href="{{ route('admin.treatments.index', array_filter(['category_id' => request('category_id')])) }}" 
                                       class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-600 transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-sliders text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.treatments.index') }}" class="py-2.5 px-3 rounded-xl bg-rose-100 text-rose-950 text-xs font-semibold hover:bg-rose-200 transition-all flex items-center justify-center" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                            </a>
                        </div>

                    </div>

                    {{-- Quick Category Pills --}}
                    <div class="pt-3 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                        <span class="text-xs font-bold text-gray-400 shrink-0 uppercase tracking-wider">Quick Filter:</span>
                        
                        <a href="{{ route('admin.treatments.index', array_filter(['search' => request('search'), 'category_id' => 'all'])) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ request('category_id', 'all') === 'all' ? 'bg-rose-500 text-white border-rose-500 shadow-sm' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50 hover:text-rose-600' }}">
                            <i class="fa-solid fa-spa text-xs"></i>
                            <span>Semua</span>
                        </a>

                        @foreach($categories as $cat)
                            @php
                                $catId = is_object($cat) ? $cat->id : (is_array($cat) ? ($cat['id'] ?? '') : $cat);
                                $catName = is_object($cat) ? $cat->name : (is_array($cat) ? ($cat['name'] ?? '') : $cat);
                                $catIcon = is_object($cat) ? ($cat->icon ?? 'fa-solid fa-tag') : (is_array($cat) ? ($cat['icon'] ?? 'fa-solid fa-tag') : 'fa-solid fa-tag');
                            @endphp
                            <a href="{{ route('admin.treatments.index', array_filter(['search' => request('search'), 'category_id' => $catId])) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ (string)request('category_id') === (string)$catId ? 'bg-rose-500 text-white border-rose-500 shadow-sm' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50 hover:text-rose-600' }}">
                                <i class="{{ $catIcon }} text-xs"></i>
                                <span>{{ $catName }}</span>
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
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-rose-950 border-b border-rose-100">
                                <th class="py-3.5 px-5">Treatment</th>
                                <th class="py-3.5 px-5">Kategori</th>
                                <th class="py-3.5 px-5">Harga</th>
                                <th class="py-3.5 px-5">Durasi</th>
                                <th class="py-3.5 px-5">Badge</th>
                                <th class="py-3.5 px-5">Rating</th>
                                <th class="py-3.5 px-5">Status</th>
                                <th class="py-3.5 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50">
                            @forelse($treatments as $tr)
                                @php
                                    if (!is_object($tr)) {
                                        continue;
                                    }
                                @endphp
                                <tr class="hover:bg-rose-50/30 transition-colors">
                                    {{-- Info Treatment --}}
                                    <td class="py-3.5 px-5">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $tr->image_url }}" alt="{{ $tr->name }}" class="w-11 h-11 rounded-xl object-cover shrink-0 border border-rose-100">
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-900 leading-snug truncate max-w-xs">{{ $tr->name }}</p>
                                                <p class="text-xs text-gray-500 line-clamp-1 max-w-xs mt-0.5">{{ $tr->description }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Kategori --}}
                                    <td class="py-3.5 px-5 text-xs font-medium text-gray-700">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-50 text-[#f45472] font-semibold">
                                            <i class="{{ $tr->category->icon ?? 'fa-solid fa-spa' }} text-xs"></i>
                                            {{ $tr->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>

                                    {{-- Harga --}}
                                    <td class="py-3.5 px-5 text-xs font-extrabold text-[#f45472] tabular-nums">
                                        Rp {{ number_format($tr->price, 0, ',', '.') }}
                                    </td>

                                    {{-- Durasi --}}
                                    <td class="py-3.5 px-5 text-xs font-medium text-gray-600">
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fa-regular fa-clock text-gray-400 text-xs"></i>
                                            {{ $tr->duration_minutes }} mnt
                                        </span>
                                    </td>

                                    {{-- Badge --}}
                                    <td class="py-3.5 px-5 text-xs">
                                        @if($tr->badge === 'best_seller')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">
                                                <i class="fa-solid fa-fire text-xs"></i> Best Seller
                                            </span>
                                        @elseif($tr->badge === 'new')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                                                <i class="fa-solid fa-sparkles text-xs"></i> New
                                            </span>
                                        @elseif($tr->badge === 'promo')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">
                                                <i class="fa-solid fa-tag text-xs"></i> Promo
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">&mdash;</span>
                                        @endif
                                    </td>

                                    {{-- Rating --}}
                                    <td class="py-3.5 px-5 text-xs">
                                        @if((float)$tr->rating === 0.0)
                                            <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-600 font-bold text-xs">Baru</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 font-bold text-amber-600">
                                                <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                                                <span class="tabular-nums">{{ number_format($tr->rating, 1) }}</span>
                                            </span>
                                            <span class="text-gray-400 text-xs">({{ $tr->rating_count }})</span>
                                        @endif
                                    </td>

                                    {{-- Status Toggle --}}
                                    <td class="py-3.5 px-5">
                                        <form method="POST" action="{{ route('admin.treatments.toggle-active', $tr->id) }}" 
                                              x-data="{ showConfirm: false }" 
                                              @submit.prevent="showConfirm = true">
                                            @csrf
                                            
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold transition-all
                                                    {{ $tr->is_active 
                                                        ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' 
                                                        : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}"
                                                    aria-label="{{ $tr->is_active ? 'Nonaktifkan treatment ' . $tr->name : 'Aktifkan treatment ' . $tr->name }}">
                                                <i class="fa-solid {{ $tr->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }} text-xs"></i>
                                                {{ $tr->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </button>

                                            {{-- Confirmation Modal --}}
                                            <template x-if="showConfirm">
                                                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                                     @click.self="showConfirm = false"
                                                     @keydown.escape.window="showConfirm = false">
                                                    <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 border border-rose-100"
                                                         @click.stop>
                                                        <div class="flex items-center gap-3 mb-4">
                                                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $tr->is_active ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600' }}">
                                                                <i class="fa-solid {{ $tr->is_active ? 'fa-triangle-exclamation' : 'fa-circle-check' }} text-lg"></i>
                                                            </div>
                                                            <div>
                                                                <h4 class="text-sm font-bold text-gray-900">{{ $tr->is_active ? 'Nonaktifkan Treatment?' : 'Aktifkan Treatment?' }}</h4>
                                                                <p class="text-xs text-gray-500 mt-0.5">{{ $tr->name }}</p>
                                                            </div>
                                                        </div>
                                                        <p class="text-xs text-gray-600 mb-5 leading-relaxed">
                                                            {{ $tr->is_active 
                                                                ? 'Treatment ini tidak akan tampil di halaman booking pelanggan. Anda dapat mengaktifkannya kembali kapan saja.'
                                                                : 'Treatment ini akan kembali tampil di halaman booking pelanggan.' }}
                                                        </p>
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" @click="showConfirm = false"
                                                                    class="flex-1 py-2.5 px-4 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-colors">
                                                                Batal
                                                            </button>
                                                            <button type="button" 
                                                                    @click="$el.closest('form').removeEventListener('submit', arguments.callee); $el.closest('form').submit()"
                                                                    class="flex-1 py-2.5 px-4 rounded-xl text-white text-xs font-bold transition-colors
                                                                    {{ $tr->is_active ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' }}">
                                                                {{ $tr->is_active ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan' }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </form>
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3.5 px-5 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <a href="{{ route('admin.treatments.edit', $tr->id) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 text-[#f45472] hover:bg-rose-100 font-semibold text-xs transition-colors"
                                               title="Edit Treatment"
                                               aria-label="Edit {{ $tr->name }}">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                                <span>Edit</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-rose-50 flex items-center justify-center">
                                            <i class="fa-solid fa-spa text-rose-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-500">Tidak ada treatment yang ditemukan</p>
                                            <p class="text-xs text-gray-400 mt-1">Coba ubah filter pencarian atau tambahkan treatment baru</p>
                                        </div>
                                        <a href="{{ route('admin.treatments.create') }}" 
                                           class="mt-2 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-colors shadow-sm">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                            Tambah Treatment Pertama
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
                    {{ $treatments->links() }}
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>

