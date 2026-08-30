<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-4 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Kartu Staf & Beautician Salon
                </h2>
                <p class="text-sm text-gray-500 mt-1">Daftar ID Card resmi beautician, keahlian, status penugasan, dan total reservasi</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.beauticians.create') }}" 
                   class="inline-flex items-center gap-4 px-8 py-4 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-bold shadow-md transition-all">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Beautician Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- PURE BLADE FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.beauticians.index') }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                        
                        {{-- Filter Status Penugasan --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="fas fa-filter text-rose-500 text-xs"></i>
                                Status Penugasan
                            </label>
                            <select name="status" class="w-full px-4 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
                                <option value="all">🎟️ Semua Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 Aktif & Bertugas</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>⚪ Nonaktif / Off</option>
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="fas fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Beautician
                            </label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Cari nama beautician, no hp, email, atau keahlian..." value="{{ request('search') }}" 
                                       class="w-full pl-12 pr-8 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-400">
                                    <i class="fas fa-magnifying-glass text-xs"></i>
                                </div>
                                @if(request('search'))
                                    <a href="{{ route('admin.beauticians.index', array_filter(['status' => request('status')])) }}" 
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
                            <a href="{{ route('admin.beauticians.index') }}" class="py-4 px-4 rounded-2xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center" title="Reset Filter">
                                <i class="fas fa-rotate-left text-xs"></i>
                            </a>
                        </div>

                    </div>

                </form>
            </div>

            {{-- BEAUTICIANS PROFESSIONAL ID CARD GRID SECTION --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($beauticians as $b)
                
                {{-- PROFESSIONAL EMPLOYEE ID CARD ITEM --}}
                <div class="bg-white rounded-3xl border border-rose-200 shadow-md hover:shadow-xl transition-all duration-300 flex flex-col justify-between overflow-hidden relative group">
                    
                    {{-- ID Card Lanyard Hole Top Bar --}}
                    <div class="bg-gray-100 py-2 border-b border-gray-200 flex justify-center items-center">
                        <div class="w-12 h-3 bg-gray-300 rounded-full border border-gray-400 shadow-inner flex items-center justify-center">
                            <div class="w-8 h-1 bg-gray-400 rounded-full"></div>
                        </div>
                    </div>

                    {{-- ID Card Header Banner --}}
                    <div class="bg-gradient-to-r from-gray-900 via-rose-950 to-gray-900 px-8 py-4 text-white flex items-center justify-between border-b-2 border-rose-400 relative">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Logo" class="w-8 h-8 rounded-full bg-white/10 p-1 border border-white/20">
                            <div>
                                <h4 class="font-bold text-xs uppercase tracking-widest text-rose-300 font-headline">Yalia Beauty</h4>
                                <p class="text-[9px] text-gray-300 uppercase tracking-wider font-mono">Official Staff Card</p>
                            </div>
                        </div>

                        {{-- Employee Badge ID --}}
                        <div class="text-right">
                            <span class="font-mono text-xs font-black text-rose-300 tracking-wider">#STAFF-{{ sprintf('%03d', $b->id) }}</span>
                            <span class="block text-[9px] text-gray-400 font-semibold uppercase">ID Number</span>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-8 space-y-4 relative">
                        
                        {{-- Photo Avatar with Metallic Ring & Status Indicator --}}
                        <div class="flex items-center justify-between">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-2xl p-1 bg-gradient-to-br from-[#f45472] via-rose-300 to-amber-300 shadow-md">
                                    <img src="{{ $b->photo_url }}" alt="{{ $b->name }}" 
                                         class="w-full h-full rounded-xl object-cover border border-white bg-white">
                                </div>
                                {{-- Status Dot Indicator --}}
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full border-2 border-white {{ $b->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"
                                      title="{{ $b->is_active ? 'Status: Aktif' : 'Status: Off' }}"></span>
                            </div>

                            {{-- Active Toggle Form Button --}}
                            <form method="POST" action="{{ route('admin.beauticians.toggle-active', $b->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-4 py-2 rounded-full text-xs font-extrabold transition-all border flex items-center gap-4 shadow-sm {{ $b->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-600 border-gray-200 hover:bg-gray-200' }}">
                                    <span class="w-2 h-2 rounded-full {{ $b->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-gray-400' }}"></span>
                                    <span>{{ $b->is_active ? 'Aktif Bertugas' : 'Off Penugasan' }}</span>
                                </button>
                            </form>
                        </div>

                        {{-- Employee Name & Role --}}
                        <div>
                        <div class="flex items-center gap-4">
                            <div class="relative shrink-0">
                                <img src="{{ $b->photo_url }}" alt="{{ $b->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-rose-200 shadow-md">
                                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $b->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}" title="{{ $b->is_active ? 'Aktif' : 'Nonaktif' }}"></span>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-rose-950 leading-snug font-headline">{{ $b->name }}</h3>
                                <p class="text-xs font-semibold text-rose-500 mt-0.5">{{ $b->specialization }}</p>
                                <div class="flex items-center gap-1.5 mt-1 text-xs text-amber-500 font-bold">
                                    <i class="fas fa-star text-xs"></i>
                                    <span>{{ number_format($b->rating ?? 5.0, 1) }}</span>
                                    <span class="text-gray-400 font-normal">({{ $b->rating_count ?? 0 }} Ulasan)</span>
                                </div>
                            </div>
                        </div>

                        {{-- Contact Details --}}
                        <div class="space-y-1.5 pt-2 border-t border-gray-100 text-xs text-rose-950">
                            @if($b->phone)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-rose-400 text-xs"></i>
                                    <span>{{ $b->phone }}</span>
                                </div>
                            @endif
                            @if($b->bio)
                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed italic">"{{ $b->bio }}"</p>
                            @endif
                        </div>

                        {{-- Total Bookings & Security Barcode Strip --}}
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div>
                                <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block">Total Penugasan:</span>
                                <span class="font-black text-rose-600 text-sm font-headline">
                                    {{ number_format($b->bookings_count ?? $b->total_bookings) }} Layanan
                                </span>
                            </div>

                            {{-- Security Barcode Visual Element --}}
                            <div class="text-right">
                                <div class="font-mono text-gray-400 text-xs tracking-tighter opacity-80 select-none">
                                    ||| | |||| | || |||
                                </div>
                                <span class="text-xs text-gray-400 font-mono block">VERIFIED STAFF</span>
                            </div>
                        </div>

                    </div>

                    {{-- ID Card Actions Footer --}}
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-4">
                        <a href="{{ route('admin.beauticians.show', $b->id) }}" 
                           class="flex-1 py-4 rounded-2xl bg-white border border-gray-200 text-rose-950 text-xs font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all flex items-center justify-center gap-4 shadow-sm">
                            <i class="fas fa-id-card text-xs"></i>
                            <span>Detail ID</span>
                        </a>

                        <a href="{{ route('admin.beauticians.edit', $b->id) }}" 
                           class="flex-1 py-4 rounded-2xl bg-white border border-gray-200 text-rose-950 text-xs font-bold hover:bg-rose-50 hover:text-rose-600 hover:border-rose-200 transition-all flex items-center justify-center gap-4 shadow-sm">
                            <i class="fas fa-pen text-xs"></i>
                            <span>Edit</span>
                        </a>

                        <form method="POST" action="{{ route('admin.beauticians.destroy', $b->id) }}" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus staf {{ $b->name }}?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-4 rounded-2xl bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition-all border border-rose-200" title="Hapus Staf">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>

                </div>

                @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center text-gray-400 border border-rose-100 space-y-4">
                    <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto text-2xl">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <h4 class="font-bold text-gray-900 text-base">Belum Ada Staf Beautician</h4>
                    <p class="text-xs text-gray-500 max-w-sm mx-auto">Daftarkan terapis / beautician baru untuk menerima penugasan reservasi pelanggan Yalia Beauty.</p>
                    <a href="{{ route('admin.beauticians.create') }}" class="inline-flex items-center gap-4 px-8 py-4 rounded-full bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] shadow-sm transition-all">
                        <i class="fas fa-plus"></i> Tambah Beautician Sekarang
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
