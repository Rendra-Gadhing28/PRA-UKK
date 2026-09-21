<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Kartu Staf & Beautician Salon
                </h2>
                <p class="text-sm text-gray-500 mt-1">Daftar ID Card resmi beautician, keahlian, status penugasan, dan total reservasi</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.beauticians.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-xs font-bold shadow-md transition-all">
                    <i class="fa-solid fa-user-plus text-xs"></i>
                    <span>Tambah Beautician Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 space-y-4">
                <form method="GET" action="{{ route('admin.beauticians.index') }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                        
                        {{-- Filter Status Penugasan --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-filter text-rose-500 text-xs"></i>
                                Status Penugasan
                            </label>
                            <select name="status" class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
                                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif & Bertugas</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif / Off</option>
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Beautician
                            </label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Cari nama beautician, no hp, email, atau keahlian..." value="{{ request('search') }}" 
                                       class="w-full pl-10 pr-8 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-rose-400">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </div>
                                @if(request('search'))
                                    <a href="{{ route('admin.beauticians.index', array_filter(['status' => request('status')])) }}" 
                                       class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-rose-600 transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-xs flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-sliders text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.beauticians.index') }}" class="py-2.5 px-3 rounded-xl bg-rose-100/70 text-rose-950 text-xs font-semibold hover:bg-rose-200 transition-all flex items-center justify-center" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                            </a>
                        </div>

                    </div>

                    {{-- Quick Status Filter Pills --}}
                    <div class="pt-3 border-t border-rose-50 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                        <span class="text-xs font-bold text-gray-400 shrink-0 uppercase tracking-wider">Quick Filter:</span>
                        
                        @php
                            $stCurrent = request('status', 'all');
                        @endphp

                        <a href="{{ route('admin.beauticians.index', array_filter(array_merge(request()->query(), ['status' => 'all']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'all' ? 'bg-rose-500 text-white border-rose-500 shadow-xs' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50' }}">
                            <i class="fa-solid fa-users text-xs"></i>
                            <span>Semua Staf</span>
                        </a>

                        <a href="{{ route('admin.beauticians.index', array_filter(array_merge(request()->query(), ['status' => 'active']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'active' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-emerald-900 border-gray-200 hover:bg-emerald-50' }}">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            <span>Aktif Bertugas</span>
                        </a>

                        <a href="{{ route('admin.beauticians.index', array_filter(array_merge(request()->query(), ['status' => 'inactive']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'inactive' ? 'bg-gray-700 text-white border-gray-700 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                            <i class="fa-solid fa-circle-pause text-xs"></i>
                            <span>Off Penugasan</span>
                        </a>
                    </div>

                </form>
            </div>

            {{-- BEAUTICIANS PROFILE CARDS GRID (MATCHING SARAH SWIFT DESIGN REFERENCE) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6">
                @forelse($beauticians as $b)
                
                {{-- SARAH SWIFT STYLE BEAUTICIAN PROFILE CARD --}}
                <div class="bg-white rounded-[28px] p-2.5 pb-4 shadow-[0_12px_30px_rgba(20,20,20,0.06)] hover:shadow-[0_18px_40px_rgba(176,31,68,0.12)] border border-[#f4dde1]/70 transition-all duration-300 flex flex-col justify-between group">
                    
                    {{-- 1. Full-Bleed Photo Media with Rounded Corners (r: 22px) --}}
                    <div class="relative w-full h-72 sm:h-80 rounded-[22px] overflow-hidden bg-rose-50 shrink-0">
                        <img src="{{ $b->photo_url }}" alt="{{ $b->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             loading="lazy" decoding="async">

                        {{-- Floating Status Badge & Toggle Button (Top-Left) --}}
                        <div class="absolute top-3 left-3 z-10">
                            <form method="POST" action="{{ route('admin.beauticians.toggle-active', $b->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-3 py-1 rounded-full text-xs font-bold transition-all backdrop-blur-md shadow-sm flex items-center gap-1.5 border {{ $b->is_active ? 'bg-white/90 text-emerald-700 border-emerald-200 hover:bg-emerald-50' : 'bg-white/90 text-rose-700 border-rose-200 hover:bg-rose-50' }}"
                                        title="{{ $b->is_active ? 'Klik untuk Nonaktifkan Penugasan' : 'Klik untuk Aktifkan Penugasan' }}">
                                    <span class="w-2 h-2 rounded-full {{ $b->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                    <span>{{ $b->is_active ? 'Aktif' : 'Off' }}</span>
                                </button>
                            </form>
                        </div>

                        {{-- Floating Quick Action Buttons: Edit & Delete (Top-Right) --}}
                        <div class="absolute top-3 right-3 z-10 flex items-center gap-1.5">
                            <a href="{{ route('admin.beauticians.edit', $b->id) }}"
                               class="w-8 h-8 rounded-full bg-white/90 hover:bg-[#fff0f2] text-[#25181c] hover:text-[#b01f44] transition-all backdrop-blur-md shadow-sm border border-[#f4dde1] flex items-center justify-center"
                               title="Edit Profil Beautician"
                               aria-label="Edit {{ $b->name }}">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>

                            <form method="POST" action="{{ route('admin.beauticians.destroy', $b->id) }}" 
                                  x-data="{ showConfirm: false }"
                                  @submit.prevent="showConfirm = true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-8 h-8 rounded-full bg-white/90 hover:bg-[#fff0f2] text-[#25181c] hover:text-[#b01f44] transition-all backdrop-blur-md shadow-sm border border-[#f4dde1] flex items-center justify-center"
                                        title="Hapus Staf"
                                        aria-label="Hapus {{ $b->name }}">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </button>

                                {{-- Alpine.js Confirmation Modal --}}
                                <template x-if="showConfirm">
                                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                         @click.self="showConfirm = false"
                                         @keydown.escape.window="showConfirm = false">
                                        <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 border border-rose-100"
                                             @click.stop>
                                            <div class="flex items-center gap-3 mb-4">
                                                <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-gray-900">Hapus Staf Beautician?</h4>
                                                    <p class="text-xs text-gray-500 mt-0.5">{{ $b->name }}</p>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-600 mb-5 leading-relaxed">
                                                Data terapis ini akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                                            </p>
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="showConfirm = false"
                                                        class="flex-1 py-2.5 px-4 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-colors">
                                                    Batal
                                                </button>
                                                <button type="button" 
                                                        @click="$el.closest('form').removeEventListener('submit', arguments.callee); $el.closest('form').submit()"
                                                        class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-colors">
                                                    Ya, Hapus Permanen
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </form>
                        </div>

                    </div>

                    {{-- 2. Name + Verified Badge --}}
                    <div class="flex items-center gap-1.5 px-2 pt-3">
                        <h3 class="font-bold text-lg text-[#17181a] truncate" title="{{ $b->name }}">{{ $b->name }}</h3>

                        @if($b->is_active)
                            <span class="inline-flex items-center justify-center shrink-0" title="Beautician Terverifikasi">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                                    <path d="M12 2l2.2 1.3 2.5-.4 1.2 2.2 2.3 1.1-.3 2.6 1.6 2-1.6 2 .3 2.6-2.3 1.1-1.2 2.2-2.5-.4L12 22l-2.2-1.3-2.5.4-1.2-2.2-2.3-1.1.3-2.6L2.5 13l1.6-2-.3-2.6 2.3-1.1 1.2-2.2 2.5.4L12 2Z" fill="#22C55E"/>
                                    <path d="M8.5 12.2l2.2 2.2 4.3-4.6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        @endif
                    </div>

                    {{-- 3. Profession / Description (2 lines max) --}}
                    <p class="px-2 text-xs text-[#8a8f98] line-clamp-2 leading-relaxed mt-1 mb-2" title="{{ $b->bio ?: $b->specialization }}">
                        {{ $b->bio ?: ($b->specialization ?: 'Beautician & Terapis Resmi Yalia Beauty') }}
                    </p>

                    {{-- 4. Footer: Stats (Clients & Works/Rating) + Pill Button (Detail) --}}
                    <div class="flex items-center justify-between px-2 pt-2.5 border-t border-rose-50/80 mt-auto">
                        <div class="flex items-center gap-3 text-xs font-bold text-[#17181a]">
                            {{-- Client count --}}
                            <span class="inline-flex items-center gap-1 text-[#17181a]" title="{{ number_format($b->bookings_count ?? $b->total_bookings) }} Layanan Selesai">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" class="text-[#8a8f98]">
                                    <circle cx="12" cy="8" r="3.2"/>
                                    <path d="M5 20c1-3.5 4-5.5 7-5.5s6 2 7 5.5"/>
                                </svg>
                                <span>{{ number_format($b->bookings_count ?? $b->total_bookings) }}</span>
                            </span>

                            {{-- Works / Rating count --}}
                            <span class="inline-flex items-center gap-1 text-[#17181a]" title="Rating {{ number_format($b->rating ?? 5.0, 1) }}">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" class="text-[#8a8f98]">
                                    <rect x="4" y="4" width="12" height="14" rx="1.5"/>
                                    <path d="M8 20h12V8"/>
                                </svg>
                                <span>{{ number_format($b->rating ?? 5.0, 1) }}</span>
                            </span>
                        </div>

                        {{-- Pill Button (Matches Reference) --}}
                        <a href="{{ route('admin.beauticians.show', $b->id) }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#f0f1f3] hover:bg-[#b01f44] hover:text-white text-[#17181a] font-bold text-xs transition-all shadow-xs"
                           title="Lihat Detail Profil">
                            <span>Detail</span>
                            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>

                </div>

                @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center text-gray-400 border border-rose-100 space-y-4">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto text-2xl">
                        <i class="fa-solid fa-id-badge"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-base">Belum Ada Staf Beautician</h4>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto">Daftarkan terapis / beautician baru untuk menerima penugasan reservasi pelanggan Yalia Beauty.</p>
                    <a href="{{ route('admin.beauticians.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] shadow-sm transition-all">
                        <i class="fa-solid fa-plus"></i> Tambah Beautician Sekarang
                    </a>
                </div>
                @endforelse
            </div>

            {{-- Custom Numbered Pagination Links --}}
            @if($beauticians->hasPages())
                <div class="bg-white rounded-3xl p-4 shadow-sm border border-rose-100">
                    {{ $beauticians->links() }}
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
