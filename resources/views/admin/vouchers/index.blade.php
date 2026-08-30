<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    <span class="w-3 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Vouchers & Diskon
                </h2>
                <p class="text-xs text-gray-500 mt-1">Kelola voucher promo, diskon persen/nominal, kuota penggunaan, dan tanggal kadaluarsa</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vouchers.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-xs font-bold shadow-md transition-all">
                    <i class="fas fa-plus text-xs"></i>
                    Tambah Voucher Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- PURE BLADE FILTER SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.vouchers.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                    
                    {{-- Filter Status --}}
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-filter text-rose-500 text-xs"></i>
                            Status Voucher
                        </label>
                        <select name="status" class="w-full px-3.5 py-2.5 text-xs font-medium rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
                            <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>🎟️ Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 Aktif & Berlaku</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>⚪ Nonaktif</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>🔴 Kadaluarsa</option>
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fas fa-magnifying-glass text-rose-500 text-xs"></i>
                            Cari Kode / Nama Voucher
                        </label>
                        <div class="relative">
                            <input type="text" name="search" placeholder="Kode promo (contoh: WELCOME10) atau nama voucher..." value="{{ request('search') }}" 
                                   class="w-full pl-9 pr-4 py-2.5 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-rose-400">
                                <i class="fas fa-magnifying-glass text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm flex items-center justify-center gap-1.5">
                            <i class="fas fa-sliders text-xs"></i>
                            <span>Filter</span>
                        </button>
                        <a href="{{ route('admin.vouchers.index') }}" class="py-2.5 px-3 rounded-xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-gray-200 transition-all flex items-center justify-center" title="Reset Filter">
                            <i class="fas fa-rotate-left text-xs"></i>
                        </a>
                    </div>

                </form>
            </div>

            {{-- VOUCHER TICKETS GRID (SHOPEE TICKET STYLE IN PURE BLADE) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($vouchers as $v)
                    @php
                        $isExpired = \Carbon\Carbon::parse($v->valid_until)->isPast();
                        $isFullyUsed = $v->used_count >= $v->quota;
                        $percentUsed = min(100, round(($v->used_count / max(1, $v->quota)) * 100));
                    @endphp

                    <div class="bg-white rounded-2xl border border-rose-200 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row overflow-hidden relative group">
                        
                        {{-- LEFT TICKET CUTOUT CARD (Shopee Orange/Pink Badge Style) --}}
                        <div class="w-full sm:w-2/5 p-4 bg-gradient-to-br from-[#f45472] via-[#e64262] to-[#d93856] text-white flex flex-col justify-between items-center text-center relative shrink-0">
                            
                            {{-- Top Header Badge --}}
                            <span class="text-[10px] font-bold uppercase tracking-widest bg-white/20 backdrop-blur-md text-white px-2.5 py-0.5 rounded-full border border-white/30">
                                {{ $v->type === 'percentage' ? 'Voucher Diskon' : 'Potongan Harga' }}
                            </span>

                            {{-- Big Value Display --}}
                            <div class="my-3">
                                @if($v->type === 'percentage')
                                    <div class="text-3xl font-black tracking-tight font-headline">
                                        {{ (float)$v->value }}% <span class="text-sm font-semibold uppercase">OFF</span>
                                    </div>
                                    @if($v->max_discount)
                                        <p class="text-[10px] text-rose-100 font-medium">Maks. Rp {{ number_format($v->max_discount, 0, ',', '.') }}</p>
                                    @endif
                                @else
                                    <div class="text-2xl font-black tracking-tight font-headline">
                                        Rp {{ number_format($v->value, 0, ',', '.') }}
                                    </div>
                                    <p class="text-[10px] text-rose-100 font-medium">Potongan Langsung</p>
                                @endif
                            </div>

                            {{-- Promo Code Badge --}}
                            <div class="w-full bg-white/15 backdrop-blur-md py-1.5 px-3 rounded-xl border border-white/30 flex items-center justify-center gap-1.5 cursor-pointer hover:bg-white/25 transition-all"
                                 title="Salin Kode Voucher"
                                 onclick="navigator.clipboard.writeText('{{ $v->code }}'); alert('Kode {{ $v->code }} berhasil disalin!')">
                                <i class="fas fa-ticket text-xs text-rose-100"></i>
                                <span class="font-mono font-extrabold text-xs tracking-wider text-white">{{ $v->code }}</span>
                            </div>

                            {{-- Semi-circle ticket notches (Shopee style cutouts) --}}
                            <div class="hidden sm:block absolute -right-3 top-0 -translate-y-1/2 w-6 h-6 bg-[#fdf5f6] rounded-full z-10"></div>
                            <div class="hidden sm:block absolute -right-3 bottom-0 translate-y-1/2 w-6 h-6 bg-[#fdf5f6] rounded-full z-10"></div>
                        </div>

                        {{-- RIGHT TICKET DETAILS CARD --}}
                        <div class="w-full sm:w-3/5 p-4 bg-white flex flex-col justify-between space-y-3 relative">
                            
                            {{-- Top Header: Title, Logo & Status --}}
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        {{-- Logo Yalia Beauty Atas Kanan/Header --}}
                                        <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" 
                                             alt="Yalia Logo" 
                                             class="w-7 h-7 object-cover rounded-full bg-rose-50 p-0.5 border border-rose-200 shrink-0 shadow-xs">
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-gray-900 text-sm leading-snug truncate" title="{{ $v->name }}">
                                                {{ $v->name }}
                                            </h3>
                                            <span class="text-[10px] text-gray-400 font-mono block">ID: #VOUCHER-{{ $v->id }}</span>
                                        </div>
                                    </div>
                                    
                                    {{-- Status Badge --}}
                                    @if($isExpired)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 shrink-0">
                                            Expired
                                        </span>
                                    @elseif(!$v->is_active)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200 shrink-0">
                                            Nonaktif
                                        </span>
                                    @elseif($isFullyUsed)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200 shrink-0">
                                            Habis
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 shrink-0">
                                            ● Aktif
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                                    {{ $v->description ?? 'Tidak ada deskripsi khusus.' }}
                                </p>
                            </div>

                            {{-- Min Purchase & Validity --}}
                            <div class="space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500 font-medium">Min. Transaksi:</span>
                                    <span class="font-bold text-gray-800">
                                        {{ $v->min_purchase > 0 ? 'Rp '.number_format($v->min_purchase, 0, ',', '.') : 'Tanpa Min. Belanja' }}
                                    </span>
                                </div>

                                {{-- Quota Usage Tracking (Misal: 46/50 Terpakai) --}}
                                <div class="bg-rose-50/60 rounded-xl p-2 border border-rose-100/80 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600 font-semibold flex items-center gap-1">
                                            <i class="fas fa-chart-line text-[#f45472] text-[10px]"></i>
                                            Track Kuota:
                                        </span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-mono font-black text-sm text-[#f45472]">
                                                {{ $v->used_count }}/{{ $v->quota }}
                                            </span>
                                            <span class="text-xs font-bold text-rose-600 bg-rose-100/80 px-1.5 py-0.5 rounded-md">
                                                {{ $percentUsed }}%
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Quota Usage Progress Bar --}}
                                    <div class="w-full bg-white rounded-full h-2 overflow-hidden border border-rose-200 shadow-inner">
                                        <div class="bg-gradient-to-r from-[#f45472] via-[#ff7590] to-[#ff8fa4] h-2 rounded-full transition-all duration-500"
                                             style="width: {{ $percentUsed }}%"></div>
                                    </div>

                                    <div class="flex items-center justify-between text-xs text-gray-500">
                                        <span>Terpakai: <strong class="text-gray-700">{{ $v->used_count }}</strong></span>
                                        <span>Sisa: <strong class="text-rose-600">{{ max(0, $v->quota - $v->used_count) }}</strong> kuota</span>
                                    </div>
                                </div>

                                {{-- Expiry Date --}}
                                <div class="flex items-center justify-between text-xs text-gray-500 pt-0.5">
                                    <span class="flex items-center gap-1 font-medium">
                                        <i class="far fa-calendar-alt text-rose-400"></i>
                                        Berlaku s/d {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            {{-- Actions Footer --}}
                            <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2">
                                {{-- Instant Toggle Active Form --}}
                                <form method="POST" action="{{ route('admin.vouchers.toggle-active', $v->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all {{ $v->is_active ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                                        {{ $v->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>

                                {{-- Edit & Delete --}}
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('admin.vouchers.edit', $v->id) }}" 
                                       class="px-2.5 py-1 rounded-lg bg-white border border-gray-200 hover:bg-rose-50 hover:text-rose-600 text-gray-800 text-xs font-bold transition-colors">
                                        Edit
                                    </a>

                                    <form method="POST" action="{{ route('admin.vouchers.destroy', $v->id) }}" 
                                          onsubmit="return confirm('Yakin ingin menghapus voucher {{ $v->code }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-rose-100">
                        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                            <i class="fas fa-ticket"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 text-base mb-1">Belum Ada Voucher Promo</h4>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto mb-4">Tambahkan kode voucher diskon baru untuk memberikan penawaran terbaik kepada pelanggan Yalia Beauty.</p>
                        <a href="{{ route('admin.vouchers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] shadow-sm transition-all">
                            <i class="fas fa-plus"></i> Tambah Voucher Sekarang
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            @if($vouchers->hasPages())
                <div class="pt-4">
                    {{ $vouchers->links() }}
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
