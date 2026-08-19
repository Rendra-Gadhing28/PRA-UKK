@php
    $isExpired = \Carbon\Carbon::parse($v->valid_until)->isPast();
@endphp

<div class="bg-white rounded-2xl border border-emerald-200 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row overflow-hidden relative group">
    
    {{-- LEFT TICKET CUTOUT CARD (Emerald Gradient for Claimed Vouchers) --}}
    <div class="w-full sm:w-2/5 p-4 bg-gradient-to-br from-emerald-500 via-teal-600 to-emerald-700 text-white flex flex-col justify-between items-center text-center relative shrink-0">
        
        {{-- Top Badge --}}
        <span class="text-[10px] font-bold uppercase tracking-widest bg-white/20 backdrop-blur-md text-white px-2.5 py-0.5 rounded-full border border-white/30">
            {{ $uv->is_used ? 'Sudah Terpakai' : 'Voucher Saya' }}
        </span>

        {{-- Big Value Display --}}
        <div class="my-3">
            @if($v->type === 'percentage')
                <div class="text-3xl font-black tracking-tight font-headline">
                    {{ (float)$v->value }}% <span class="text-sm font-semibold uppercase">OFF</span>
                </div>
                @if($v->max_discount)
                    <p class="text-[10px] text-emerald-100 font-medium">Maks. Rp {{ number_format($v->max_discount, 0, ',', '.') }}</p>
                @endif
            @else
                <div class="text-2xl font-black tracking-tight font-headline">
                    Rp {{ number_format($v->value, 0, ',', '.') }}
                </div>
                <p class="text-[10px] text-emerald-100 font-medium">Potongan Langsung</p>
            @endif
        </div>

        {{-- Promo Code Badge --}}
        <div class="w-full bg-white/20 backdrop-blur-md py-1.5 px-3 rounded-xl border border-white/30 flex items-center justify-center gap-1.5 cursor-pointer hover:bg-white/30 transition-all"
             title="Salin Kode Voucher"
             onclick="navigator.clipboard.writeText('{{ $v->code }}'); alert('Kode {{ $v->code }} berhasil disalin!')">
            <i class="fas fa-ticket text-xs text-emerald-100"></i>
            <span class="font-mono font-extrabold text-xs tracking-wider text-white">{{ $v->code }}</span>
        </div>

        {{-- Semi-circle ticket notches --}}
        <div class="hidden sm:block absolute -right-3 top-0 -translate-y-1/2 w-6 h-6 bg-[#fdf5f6] rounded-full z-10"></div>
        <div class="hidden sm:block absolute -right-3 bottom-0 translate-y-1/2 w-6 h-6 bg-[#fdf5f6] rounded-full z-10"></div>
    </div>

    {{-- RIGHT DETAILS CARD --}}
    <div class="w-full sm:w-3/5 p-4 bg-white flex flex-col justify-between space-y-3 relative">
        
        <div>
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" 
                         alt="Yalia Logo" 
                         class="w-7 h-7 object-cover rounded-full bg-emerald-50 p-0.5 border border-emerald-200 shrink-0 shadow-xs">
                    <div class="min-w-0">
                        <h3 class="font-bold text-gray-900 text-sm leading-snug truncate" title="{{ $v->name }}">
                            {{ $v->name }}
                        </h3>
                        <span class="text-[10px] text-gray-400 font-mono block">Diklaim: {{ $uv->created_at ? $uv->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>

                {{-- Usage Status --}}
                @if($uv->is_used)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 shrink-0">
                        Terpakai
                    </span>
                @elseif($isExpired)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200 shrink-0">
                        Expired
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 shrink-0 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                        Siap Pakai
                    </span>
                @endif
            </div>

            <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                {{ $v->description ?? 'Tidak ada deskripsi khusus.' }}
            </p>
        </div>

        {{-- Min Purchase & Expiry --}}
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs">
                <span class="text-gray-500 font-medium">Min. Transaksi:</span>
                <span class="font-bold text-gray-800">
                    {{ $v->min_purchase > 0 ? 'Rp '.number_format($v->min_purchase, 0, ',', '.') : 'Tanpa Min. Belanja' }}
                </span>
            </div>

            <div class="flex items-center justify-between text-[11px] text-gray-500 pt-0.5">
                <span class="flex items-center gap-1 font-medium">
                    <i class="far fa-calendar-alt text-emerald-500"></i>
                    Berlaku s/d {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}
                </span>
            </div>
        </div>

        {{-- Action Button --}}
        <div class="pt-2 border-t border-gray-100 flex items-center gap-2">
            @if(!$uv->is_used && !$isExpired)
                <a href="{{ route('user.bookings.create') }}" 
                   class="w-full py-2 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold text-center shadow-sm hover:shadow transition-all flex items-center justify-center gap-1.5">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Gunakan Saat Booking</span>
                </a>
            @else
                <button disabled class="w-full py-2 px-4 rounded-xl bg-gray-100 text-gray-400 text-xs font-bold cursor-not-allowed">
                    {{ $uv->is_used ? 'Sudah Digunakan pada Booking' : 'Tidak Dapat Digunakan (Kadaluarsa)' }}
                </button>
            @endif
        </div>

    </div>

</div>
