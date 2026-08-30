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

            {{-- BEAUTICIANS PROFESSIONAL ID CARD GRID SECTION --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($beauticians as $b)
                
                {{-- PROFESSIONAL EMPLOYEE ID CARD ITEM --}}
                <div class="bg-white rounded-3xl border border-rose-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between overflow-hidden relative group">
                    
                    {{-- ID Card Lanyard Hole Top Bar --}}
                    <div class="bg-rose-50/60 py-2 border-b border-rose-100 flex justify-center items-center">
                        <div class="w-12 h-2.5 bg-rose-200/80 rounded-full border border-rose-300/60 shadow-inner flex items-center justify-center">
                            <div class="w-8 h-1 bg-rose-300 rounded-full"></div>
                        </div>
                    </div>

                    {{-- ID Card Header Banner --}}
                    <div class="bg-gradient-to-r from-gray-900 via-rose-950 to-gray-900 px-6 py-3 text-white flex items-center justify-between border-b-2 border-[#f45472] relative">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-white/10 p-1 border border-white/20 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-spa text-rose-300 text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-xs uppercase tracking-widest text-rose-300 font-headline">Yalia Beauty</h4>
                                <p class="text-xs text-gray-300 uppercase tracking-wider font-mono">Official Staff Card</p>
                            </div>
                        </div>

                        {{-- Employee Badge ID --}}
                        <div class="text-right">
                            <span class="font-mono text-xs font-bold text-rose-300 tracking-wider">#STAFF-{{ sprintf('%03d', $b->id) }}</span>
                            <span class="block text-xs text-gray-400 font-semibold uppercase">ID Number</span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-6 space-y-4">
                        
                        {{-- Photo Avatar with Status Ring & Main Info --}}
                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <div class="w-16 h-16 rounded-2xl p-0.5 bg-gradient-to-br from-[#f45472] via-rose-300 to-amber-300 shadow-sm">
                                    <img src="{{ $b->photo_url }}" alt="{{ $b->name }}" 
                                         class="w-full h-full rounded-xl object-cover border border-white bg-white">
                                </div>
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $b->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"
                                      title="{{ $b->is_active ? 'Status: Aktif' : 'Status: Off' }}"></span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-base text-gray-900 leading-snug truncate font-headline">{{ $b->name }}</h3>
                                <p class="text-xs font-semibold text-[#f45472] truncate mt-0.5">{{ $b->specialization }}</p>
                                
                                <div class="flex items-center gap-1.5 mt-1 text-xs text-amber-500 font-bold">
                                    <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                                    <span class="tabular-nums">{{ number_format($b->rating ?? 5.0, 1) }}</span>
                                    <span class="text-gray-400 font-normal text-xs">({{ $b->rating_count ?? 0 }} Ulasan)</span>
                                </div>
                            </div>
                        </div>

                        {{-- Contact Details & Bio --}}
                        <div class="space-y-1.5 pt-3 border-t border-rose-50 text-xs text-gray-600">
                            @if($b->phone)
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-phone text-rose-400 text-xs w-4"></i>
                                    <span class="font-medium text-gray-800">{{ $b->phone }}</span>
                                </div>
                            @endif
                            @if($b->bio)
                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed italic">"{{ $b->bio }}"</p>
                            @endif
                        </div>

                        {{-- Total Bookings & Status Badge --}}
                        <div class="flex items-center justify-between pt-3 border-t border-rose-50">
                            <div>
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Total Penugasan</span>
                                <span class="font-extrabold text-[#f45472] text-sm tabular-nums">
                                    {{ number_format($b->bookings_count ?? $b->total_bookings) }} Layanan
                                </span>
                            </div>

                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ $b->is_active ? 'bg-emerald-100 text-emerald-950 border border-emerald-200' : 'bg-rose-50 text-rose-950 border border-rose-200' }}">
                                    <i class="fa-solid {{ $b->is_active ? 'fa-circle-check text-emerald-600' : 'fa-circle-pause text-rose-400' }} text-xs"></i>
                                    <span>{{ $b->is_active ? 'Aktif' : 'Off' }}</span>
                                </span>
                            </div>
                        </div>

                    </div>

                    {{-- ID Card Actions Footer --}}
                    <div class="p-4 bg-rose-50/40 border-t border-rose-100 flex items-center justify-between gap-2">
                        
                        {{-- Status Toggle Button --}}
                        <form method="POST" action="{{ route('admin.beauticians.toggle-active', $b->id) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="py-2 px-3 rounded-full text-xs font-bold transition-all border flex items-center gap-1.5 shadow-2xs {{ $b->is_active ? 'bg-emerald-50 text-emerald-950 border-emerald-200 hover:bg-emerald-100' : 'bg-rose-50 text-rose-950 border border-rose-200 hover:bg-rose-100' }}"
                                    title="{{ $b->is_active ? 'Nonaktifkan Staf' : 'Aktifkan Staf' }}"
                                    aria-label="{{ $b->is_active ? 'Nonaktifkan staf ' . $b->name : 'Aktifkan staf ' . $b->name }}">
                                <i class="fa-solid {{ $b->is_active ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-gray-400' }} text-xs"></i>
                                <span>{{ $b->is_active ? 'Aktif' : 'Off' }}</span>
                            </button>
                        </form>

                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.beauticians.show', $b->id) }}" 
                               class="py-2 px-3 rounded-full bg-white border border-rose-200 text-rose-950 text-xs font-bold hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center gap-1 shadow-2xs"
                               title="Lihat Detail ID Staf"
                               aria-label="Lihat detail ID {{ $b->name }}">
                                <i class="fa-solid fa-id-card text-xs"></i>
                                <span>Detail</span>
                            </a>

                            <a href="{{ route('admin.beauticians.edit', $b->id) }}" 
                               class="py-2 px-3 rounded-full bg-white border border-rose-200 text-rose-950 text-xs font-bold hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center gap-1 shadow-2xs"
                               title="Edit Staf"
                               aria-label="Edit {{ $b->name }}">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                <span>Edit</span>
                            </a>

                            <form method="POST" action="{{ route('admin.beauticians.destroy', $b->id) }}" 
                                  x-data="{ showConfirm: false }"
                                  @submit.prevent="showConfirm = true">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-2 rounded-full bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition-all border border-rose-200" 
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
