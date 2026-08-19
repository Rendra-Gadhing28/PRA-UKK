@php
    $isExpired = \Carbon\Carbon::parse($v->valid_until)->isPast();
    $isFullyUsed = $v->used_count >= $v->quota;
    $percentUsed = min(100, round(($v->used_count / max(1, $v->quota)) * 100));
    $isClaimed = in_array($v->id, $claimedVoucherIds ?? []);
    $userPoints = $user->total_points ?? 0;
    $hasEnoughPoints = $v->points_required > 0 ? ($userPoints >= $v->points_required) : true;
@endphp

<div class="bg-white rounded-2xl border border-rose-200 shadow-sm hover:shadow-md transition-all flex flex-col sm:flex-row overflow-hidden relative group">
    
    {{-- LEFT TICKET CUTOUT CARD (Shopee Ticket Style - Same as Admin Vouchers) --}}
    <div class="w-full sm:w-2/5 p-4 bg-gradient-to-br from-[#f45472] via-[#e64262] to-[#d93856] text-white flex flex-col justify-between items-center text-center relative shrink-0">
        
        {{-- Top Header / Event Badge --}}
        @if($v->is_event && $v->event_name)
            <span class="text-[10px] font-bold uppercase tracking-widest bg-amber-400 text-amber-950 px-2.5 py-0.5 rounded-full shadow-sm">
                {{ $v->event_name }}
            </span>
        @elseif($v->points_required > 0)
            <span class="text-[10px] font-bold uppercase tracking-widest bg-amber-300 text-amber-950 px-2.5 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                <i class="fas fa-coins"></i> {{ $v->points_required }} PTS
            </span>
        @else
            <span class="text-[10px] font-bold uppercase tracking-widest bg-white/20 backdrop-blur-md text-white px-2.5 py-0.5 rounded-full border border-white/30">
                {{ $v->type === 'percentage' ? 'Voucher Diskon' : 'Potongan Harga' }}
            </span>
        @endif

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
        
        {{-- Header: Logo & Title --}}
        <div>
            <div class="flex items-start justify-between gap-3 mb-2">
                <div class="flex items-center gap-2.5 min-w-0">
                    {{-- Logo Yalia Beauty Header --}}
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
                @if($isClaimed)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 shrink-0 flex items-center gap-1">
                        <i class="fas fa-check-circle"></i> Diklaim
                    </span>
                @elseif($isExpired)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200 shrink-0">
                        Expired
                    </span>
                @elseif($isFullyUsed)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200 shrink-0">
                        Habis
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200 shrink-0">
                        Tersedia
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

            {{-- Quota Usage Progress Bar --}}
            <div class="bg-rose-50/60 rounded-xl p-2 border border-rose-100/80 space-y-1.5">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-gray-600 font-semibold flex items-center gap-1 text-[11px]">
                        <i class="fas fa-chart-line text-[#f45472] text-[10px]"></i>
                        Sisa Kuota:
                    </span>
                    <span class="font-mono font-bold text-xs text-[#f45472]">
                        {{ max(0, $v->quota - $v->used_count) }} / {{ $v->quota }}
                    </span>
                </div>

                <div class="w-full bg-white rounded-full h-2 overflow-hidden border border-rose-200 shadow-inner">
                    <div class="bg-gradient-to-r from-[#f45472] via-[#ff7590] to-[#ff8fa4] h-2 rounded-full transition-all duration-500"
                         style="width: {{ $percentUsed }}%"></div>
                </div>
            </div>

            {{-- Expiry Date --}}
            <div class="flex items-center justify-between text-[11px] text-gray-500 pt-0.5">
                <span class="flex items-center gap-1 font-medium">
                    <i class="far fa-calendar-alt text-rose-400"></i>
                    Berlaku s/d {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}
                </span>
            </div>
        </div>

        {{-- ACTIONS FOOTER --}}
        <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2">
            @if($isClaimed)
                <span class="w-full py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold text-center border border-emerald-200 flex items-center justify-center gap-1.5">
                    <i class="fas fa-check-circle text-emerald-500"></i> Sudah Tersimpan di Akun
                </span>
            @elseif($isExpired)
                <button disabled class="w-full py-2 rounded-xl bg-gray-100 text-gray-400 text-xs font-bold cursor-not-allowed">
                    Kadaluarsa
                </button>
            @elseif($isFullyUsed)
                <button disabled class="w-full py-2 rounded-xl bg-gray-100 text-gray-400 text-xs font-bold cursor-not-allowed">
                    Kuota Habis
                </button>
            @else
                <form method="POST" action="{{ route('user.vouchers.claim', $v->id) }}" class="w-full">
                    @csrf
                    @if($v->points_required > 0)
                        @if($hasEnoughPoints)
                            <button type="submit" 
                                    class="w-full py-2 px-4 rounded-xl bg-amber-400 hover:bg-amber-500 text-amber-950 text-xs font-extrabold shadow-sm hover:shadow transition-all flex items-center justify-center gap-1.5">
                                <i class="fas fa-coins text-amber-900"></i>
                                <span>Tukar {{ $v->points_required }} PTS</span>
                            </button>
                        @else
                            <button type="button" disabled 
                                    class="w-full py-2 px-4 rounded-xl bg-gray-100 text-gray-400 text-xs font-bold cursor-not-allowed flex items-center justify-center gap-1.5"
                                    title="Poin Anda tidak mencukupi (Butuh {{ $v->points_required }} PTS)">
                                <i class="fas fa-lock text-gray-300"></i>
                                <span>Poin Tidak Cukup ({{ $v->points_required }} PTS)</span>
                            </button>
                        @endif
                    @elseif($v->is_event)
                        <button type="submit" 
                                class="w-full py-2 px-4 rounded-xl bg-gradient-to-r from-purple-600 to-rose-500 hover:from-purple-700 hover:to-rose-600 text-white text-xs font-extrabold shadow-sm hover:shadow transition-all flex items-center justify-center gap-1.5">
                            <i class="fas fa-gift text-amber-300"></i>
                            <span>Klaim Voucher Event 🎁</span>
                        </button>
                    @else
                        <button type="submit" 
                                class="w-full py-2 px-4 rounded-xl bg-[#f45472] hover:bg-[#d93856] text-white text-xs font-bold shadow-sm hover:shadow transition-all flex items-center justify-center gap-1.5">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Klaim Voucher Gratis</span>
                        </button>
                    @endif
                </form>
            @endif
        </div>

    </div>

</div>
