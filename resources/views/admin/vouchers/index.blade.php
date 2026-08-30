<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-3 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Vouchers & Diskon
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola voucher promo, diskon persen/nominal, kuota penggunaan, dan tanggal kadaluarsa</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.vouchers.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-xs font-bold shadow-md transition-all">
                    <i class="fa-solid fa-ticket text-xs"></i>
                    <span>Tambah Voucher Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 space-y-4">
                <form method="GET" action="{{ route('admin.vouchers.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                        
                        {{-- Filter Status --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-filter text-rose-500 text-xs"></i>
                                Status Voucher
                            </label>
                            <select name="status" class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
                                <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif & Berlaku</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Kadaluarsa</option>
                            </select>
                        </div>

                        {{-- Search Input --}}
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Kode / Nama Voucher
                            </label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Kode promo (contoh: WELCOME10) atau nama voucher..." value="{{ request('search') }}" 
                                       class="w-full pl-9 pr-4 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-rose-400">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-xs flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-sliders text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.vouchers.index') }}" class="py-2.5 px-3 rounded-xl bg-rose-100/70 text-rose-950 text-xs font-semibold hover:bg-rose-200 transition-all flex items-center justify-center" title="Reset Filter">
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

                        <a href="{{ route('admin.vouchers.index', array_filter(array_merge(request()->query(), ['status' => 'all']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'all' ? 'bg-rose-500 text-white border-rose-500 shadow-xs' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50' }}">
                            <i class="fa-solid fa-ticket text-xs"></i>
                            <span>Semua Voucher</span>
                        </a>

                        <a href="{{ route('admin.vouchers.index', array_filter(array_merge(request()->query(), ['status' => 'active']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'active' ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'bg-white text-emerald-950 border-gray-200 hover:bg-emerald-50' }}">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            <span>Aktif & Berlaku</span>
                        </a>

                        <a href="{{ route('admin.vouchers.index', array_filter(array_merge(request()->query(), ['status' => 'inactive']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'inactive' ? 'bg-amber-600 text-white border-amber-600 shadow-xs' : 'bg-white text-amber-950 border-gray-200 hover:bg-amber-50' }}">
                            <i class="fa-solid fa-circle-pause text-xs"></i>
                            <span>Nonaktif</span>
                        </a>

                        <a href="{{ route('admin.vouchers.index', array_filter(array_merge(request()->query(), ['status' => 'expired']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $stCurrent === 'expired' ? 'bg-gray-700 text-white border-gray-700 shadow-xs' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                            <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                            <span>Kadaluarsa</span>
                        </a>
                    </div>

                </form>
            </div>

            {{-- VOUCHER TICKETS GRID (USER VOUCHER CARD STYLE MATCH) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($vouchers as $v)
                    @php
                        $isExpired   = \Carbon\Carbon::parse($v->valid_until)->isPast();
                        $isFullyUsed = $v->used_count >= $v->quota;
                        $percentUsed = min(100, round(($v->used_count / max(1, $v->quota)) * 100));

                        // Gradient Accent Matching User Voucher Card
                        if ($isExpired || !$v->is_active || $isFullyUsed) {
                            $panelStyle = 'background: linear-gradient(135deg, #4A4648 0%, #333032 100%); color: #FFFFFF;';
                            $btnStyle   = 'color: #1F2937 !important; background-color: #FFFFFF !important;';
                        } elseif (!empty($v->is_event)) {
                            $panelStyle = 'background: linear-gradient(135deg, #7A1F35 0%, #9B2C46 100%); color: #FFFFFF;';
                            $btnStyle   = 'color: #7A1F35 !important; background-color: #FFFFFF !important;';
                        } elseif ($v->points_required > 0) {
                            $panelStyle = 'background: linear-gradient(135deg, #8C6D37 0%, #B89355 100%); color: #FFFFFF;';
                            $btnStyle   = 'color: #5C441D !important; background-color: #FFFFFF !important;';
                        } else {
                            $panelStyle = 'background: linear-gradient(135deg, #7A1F35 0%, #9B2C46 100%); color: #FFFFFF;';
                            $btnStyle   = 'color: #7A1F35 !important; background-color: #FFFFFF !important;';
                        }
                    @endphp

                    {{-- HORIZONTAL LUXURY TICKET CARD --}}
                    <div class="relative flex flex-col sm:flex-row rounded-2xl overflow-hidden transition-all duration-300 group hover:-translate-y-0.5 hover:shadow-md"
                         style="min-height:156px; background: rgba(255, 250, 252, 0.95); backdrop-filter: blur(16px); border: 1px solid rgba(220, 180, 190, 0.6); box-shadow: 0 4px 20px rgba(122, 31, 53, 0.05);">
                        
                        {{-- ===== LEFT PANEL – Frosted Glass Info Panel ===== --}}
                        <div class="flex-1 min-w-0 flex flex-col justify-between p-4 pr-4 relative overflow-hidden space-y-2">

                            {{-- Ambient Glows --}}
                            <div class="absolute -top-4 -left-4 w-20 h-20 rounded-full pointer-events-none bg-[#7A1F35]/5 blur-xl"></div>

                            {{-- Header: Logo & Status Badge --}}
                            <div class="flex items-center justify-between gap-2 relative z-10">
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}"
                                         alt="Yalia Logo"
                                         class="w-4 h-4 object-contain rounded-full bg-white p-0.5 border border-[#F4DDE1] shrink-0 shadow-2xs">
                                    <span class="text-xs font-bold tracking-widest uppercase text-[#7A4B56]">Yalia Beauty</span>
                                </div>

                                {{-- Status Badges --}}
                                @if($isExpired)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-300 shrink-0">
                                        Expired
                                    </span>
                                @elseif(!$v->is_active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-950 border border-amber-200 shrink-0">
                                        Nonaktif
                                    </span>
                                @elseif($isFullyUsed)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-950 border border-rose-200 shrink-0">
                                        Kuota Habis
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FFF0F3] text-[#7A1F35] border border-[#F4DDE1] shrink-0">
                                        ● Aktif
                                    </span>
                                @endif
                            </div>

                            {{-- Voucher Title & Type Badge --}}
                            <div class="relative z-10">
                                <h3 class="font-bold text-sm text-[#2B0F23] leading-snug truncate font-headline" title="{{ $v->name }}">
                                    {{ $v->name }}
                                </h3>
                                
                                <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                    <span class="text-xs font-mono text-gray-400">ID: #VOUCHER-{{ $v->id }}</span>
                                    
                                    @if($v->is_event && $v->event_name)
                                        <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider bg-[#FFF0F3] text-[#7A1F35] border border-[#F4DDE1] px-2 py-0.5 rounded-full">
                                            <i class="fa-solid fa-star text-xs text-[#7A1F35]"></i> {{ $v->event_name }}
                                        </span>
                                    @elseif($v->points_required > 0)
                                        <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-wider bg-[#FAF3E8] text-[#8C6D37] border border-[#E8D4B5] px-2 py-0.5 rounded-full">
                                            <i class="fa-solid fa-coins text-xs text-[#8C6D37]"></i> {{ $v->points_required }} PTS
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Min Purchase & Validity --}}
                            <div class="flex items-center gap-2 text-xs text-[#594043] font-medium relative z-10">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-bag-shopping text-xs opacity-70"></i>
                                    <span class="tabular-nums">{{ $v->min_purchase > 0 ? 'Min Rp '.number_format($v->min_purchase, 0, ',', '.') : 'Tanpa Min.' }}</span>
                                </span>
                                <span class="opacity-40">·</span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-regular fa-calendar-check text-xs opacity-70"></i>
                                    <span>s/d {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}</span>
                                </span>
                            </div>

                            {{-- Quota Bar --}}
                            <div class="relative z-10 space-y-1">
                                <div class="flex justify-between text-xs font-semibold text-[#7A4B56]">
                                    <span>Sisa Kuota:</span>
                                    <span class="font-mono font-bold text-[#2B0F23] tabular-nums">{{ max(0, $v->quota - $v->used_count) }}/{{ $v->quota }} ({{ $percentUsed }}%)</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full overflow-hidden bg-[#F4DDE1]">
                                    <div class="h-full rounded-full transition-all duration-500"
                                         style="width:{{ $percentUsed }}%; background: linear-gradient(to right, #7A1F35, #8C6D37);"></div>
                                </div>
                            </div>

                            {{-- Copy Code Pill Button with Alpine.js Toast Feedback --}}
                            <div x-data="{ copied: false }" 
                                 @click="navigator.clipboard.writeText('{{ $v->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                 class="self-start inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md cursor-pointer transition-all shadow-2xs relative z-10 border border-[#b01f44]/20 bg-[#fff0f5]/90 text-[#7A1F35] select-none hover:bg-rose-100"
                                 title="Salin kode voucher"
                                 aria-label="Salin kode {{ $v->code }}">
                                <i class="fa-solid" :class="copied ? 'fa-check text-emerald-600' : 'fa-ticket text-[#7A1F35]'"></i>
                                <span class="font-mono font-extrabold text-xs tracking-widest text-[#7A1F35]" x-text="copied ? 'Disalin!' : '{{ $v->code }}'"></span>
                            </div>

                        </div>

                        {{-- ===== NOTCH SEPARATOR ===== --}}
                        <div class="relative hidden sm:flex flex-col items-center justify-between shrink-0 pointer-events-none z-10" style="width:16px;">
                            <div class="w-4 h-4 rounded-full border border-pink-200/60 shrink-0" style="margin-top:-8px; background: #FFF8F8;"></div>
                            <div class="flex-1 w-0 border-l border-dashed border-[#F4DDE1]"></div>
                            <div class="w-4 h-4 rounded-full border border-pink-200/60 shrink-0" style="margin-bottom:-8px; background: #FFF8F8;"></div>
                        </div>

                        {{-- ===== RIGHT PANEL – Old Money Luxury Accent Panel (Admin Actions) ===== --}}
                        <div class="relative flex flex-col items-center justify-between rounded-b-2xl sm:rounded-r-2xl sm:rounded-bl-none shrink-0 overflow-hidden"
                             style="width:136px; padding:14px 10px; {{ $panelStyle }}">

                            {{-- Decorative Circles --}}
                            <div class="absolute -top-8 -right-8 w-20 h-20 rounded-full bg-white/10 pointer-events-none"></div>
                            <div class="absolute -bottom-6 -left-6 w-16 h-16 rounded-full bg-white/10 pointer-events-none"></div>

                            {{-- "VOUCHER" Eyebrow --}}
                            <span class="relative text-xs font-bold uppercase tracking-widest opacity-80">Voucher</span>

                            {{-- Big Value Display --}}
                            <div class="relative text-center my-1">
                                @if($v->type === 'percentage')
                                    <div class="text-3xl font-black leading-none font-headline tabular-nums">
                                        {{ (int)$v->value }}%
                                    </div>
                                    <div class="text-xs font-bold uppercase tracking-wide mt-0.5 opacity-90">Diskon</div>
                                    @if($v->max_discount)
                                        <div class="text-xs opacity-75 mt-0.5 tabular-nums">Maks Rp {{ number_format($v->max_discount, 0, ',', '.') }}</div>
                                    @endif
                                @else
                                    <div class="text-lg font-black leading-tight font-headline tabular-nums">
                                        Rp {{ number_format($v->value, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs font-bold uppercase tracking-wide mt-0.5 opacity-90">Potongan</div>
                                @endif
                            </div>

                            {{-- Subtext Brand Stamp --}}
                            <span class="relative text-xs font-bold opacity-75 tracking-wider font-mono">@yaliabeauty</span>

                            {{-- Admin Actions CTA Panel --}}
                            <div class="relative w-full mt-2 space-y-1.5">
                                
                                {{-- Instant Toggle Active Form --}}
                                <form method="POST" action="{{ route('admin.vouchers.toggle-active', $v->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="w-full text-center text-xs font-bold py-1.5 px-2 rounded-full shadow-xs hover:opacity-95 transition-all flex items-center justify-center gap-1 cursor-pointer"
                                            style="{{ $btnStyle }}"
                                            aria-label="{{ $v->is_active ? 'Nonaktifkan voucher ' . $v->code : 'Aktifkan voucher ' . $v->code }}">
                                        <i class="fa-solid {{ $v->is_active ? 'fa-toggle-on text-emerald-600' : 'fa-toggle-off text-rose-500' }} text-xs"></i>
                                        <span>{{ $v->is_active ? 'Aktif' : 'Off' }}</span>
                                    </button>
                                </form>

                                {{-- Edit & Delete Buttons --}}
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('admin.vouchers.edit', $v->id) }}" 
                                       class="flex-1 py-1 px-1.5 rounded-full bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition-all text-center border border-white/30 flex items-center justify-center gap-1"
                                       title="Edit Voucher"
                                       aria-label="Edit voucher {{ $v->code }}">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        <span>Edit</span>
                                    </a>

                                    <form method="POST" action="{{ route('admin.vouchers.destroy', $v->id) }}" 
                                          x-data="{ showConfirm: false }"
                                          @submit.prevent="showConfirm = true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1 px-2 rounded-full bg-rose-950/60 hover:bg-rose-900 text-white text-xs font-bold transition-all border border-white/30 flex items-center justify-center"
                                                title="Hapus Voucher"
                                                aria-label="Hapus voucher {{ $v->code }}">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>

                                        {{-- Alpine.js Confirmation Modal --}}
                                        <template x-if="showConfirm">
                                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                                 @click.self="showConfirm = false"
                                                 @keydown.escape.window="showConfirm = false">
                                                <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 border border-rose-100 text-left text-gray-900"
                                                     @click.stop>
                                                    <div class="flex items-center gap-3 mb-4">
                                                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-sm font-bold text-gray-900">Hapus Voucher Promo?</h4>
                                                            <p class="text-xs text-gray-500 mt-0.5 font-mono">{{ $v->code }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-600 mb-5 leading-relaxed">
                                                        Voucher <strong>{{ $v->name }}</strong> akan dihapus secara permanen.
                                                    </p>
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" @click="showConfirm = false"
                                                                class="flex-1 py-2.5 px-4 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-colors">
                                                            Batal
                                                        </button>
                                                        <button type="button" 
                                                                @click="$el.closest('form').removeEventListener('submit', arguments.callee); $el.closest('form').submit()"
                                                                class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-colors">
                                                            Ya, Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </form>
                                </div>
                            </div>

                        </div>

                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-rose-100 space-y-3">
                        <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto text-2xl">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <h4 class="font-bold text-gray-900 text-base">Belum Ada Voucher Promo</h4>
                        <p class="text-xs text-gray-500 max-w-sm mx-auto">Tambahkan kode voucher diskon baru untuk memberikan penawaran terbaik kepada pelanggan Yalia Beauty.</p>
                        <a href="{{ route('admin.vouchers.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] shadow-sm transition-all">
                            <i class="fa-solid fa-plus"></i> Tambah Voucher Sekarang
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            @if($vouchers->hasPages())
                <div class="bg-white rounded-3xl p-4 shadow-sm border border-rose-100">
                    {{ $vouchers->links() }}
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
