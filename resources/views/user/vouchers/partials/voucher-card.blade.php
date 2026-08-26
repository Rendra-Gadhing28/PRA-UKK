@php
    $isExpired    = \Carbon\Carbon::parse($v->valid_until)->isPast();
    $isFullyUsed  = $v->used_count >= $v->quota;
    $percentUsed  = min(100, round(($v->used_count / max(1, $v->quota)) * 100));
    $isClaimed    = in_array($v->id, $claimedVoucherIds ?? []) || isset($userVoucher);
    $isUserUsed   = isset($userVoucher) ? (bool)$userVoucher->is_used : false;
    $userPoints   = $user->total_points ?? 0;
    $hasEnoughPoints = $v->points_required > 0 ? ($userPoints >= $v->points_required) : true;

    // Right-panel accent colors priority:
    // 1. Grey ONLY when used or expired
    if ($isUserUsed || $isExpired) {
        $panelBg   = '#4b5563'; // gray-600 (abu-abu saat terpakai/expired)
        $panelText = '#e5e7eb'; // gray-200
    } 
    // 2. Event Special Vouchers -> ALWAYS PINK (#f45472 warna utama web ini)
    elseif (!empty($v->is_event)) {
        $panelBg   = '#f45472'; // brand pink warna utama
        $panelText = '#ffffff';
    } 
    // 3. Regular claimed vouchers -> GREEN (#059669)
    elseif ($isClaimed) {
        $panelBg   = '#059669'; // emerald-600 (HIJAU saat tersedia & diklaim)
        $panelText = '#ffffff';
    } 
    // 4. Grey for unclaimed public vouchers out of quota
    elseif ($isFullyUsed) {
        $panelBg   = '#4b5563'; // gray-600
        $panelText = '#e5e7eb';
    } 
    // 5. Point Redemption Vouchers -> Amber
    elseif ($v->points_required > 0) {
        $panelBg   = '#d97706'; // amber-600
        $panelText = '#ffffff';
    } 
    // 6. Regular free promo vouchers
    else {
        $panelBg   = '#b01f44'; // primary color from tailwind config
        $panelText = '#ffffff';
    }
@endphp

{{-- ===== HORIZONTAL TICKET CARD ===== --}}
<div class="relative flex flex-row rounded-2xl overflow-hidden
            border border-outline-variant/30
            bg-inverse-surface
            shadow-md hover:shadow-xl hover:-translate-y-0.5
            transition-all duration-300 group"
     style="min-height:148px;">

    {{-- ===== LEFT PANEL – Dark luxury info panel (Tailwind config background & complementary text) ===== --}}
    <div class="flex-1 min-w-0 flex flex-col justify-between p-4 pr-5 relative overflow-hidden">

        {{-- Decorative soft ambient glows --}}
        <div class="absolute -top-4 -left-4 w-20 h-20 rounded-full pointer-events-none bg-primary/10 blur-xl"></div>
        <div class="absolute bottom-2 right-2 w-16 h-16 rounded-full pointer-events-none bg-tertiary-fixed-dim/10 blur-lg"></div>

        {{-- Brand header + status --}}
        <div class="flex items-center gap-1.5 mb-1.5 relative z-10">
            <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}"
                 alt="Yalia Logo"
                 class="w-4 h-4 object-contain rounded-full bg-on-surface p-0.5 border border-outline-variant/40 shrink-0">
            <span class="text-[8px] font-semibold text-outline-variant tracking-widest uppercase">Yalia Beauty</span>

            @if($isUserUsed)
                <span class="ml-auto flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold bg-slate-800 text-slate-400 border border-slate-700 shrink-0">
                    Terpakai
                </span>
            @elseif($isClaimed)
                <span class="ml-auto flex items-center gap-1 px-2 py-0.5 rounded-full text-[8px] font-bold bg-emerald-950/90 text-emerald-300 border border-emerald-800/80 shrink-0">
                    <i class="fas fa-check-circle text-[8px]"></i> Diklaim
                </span>
            @elseif($isExpired)
                <span class="ml-auto px-2 py-0.5 rounded-full text-[8px] font-bold bg-on-surface text-outline-variant border border-outline-variant/30 shrink-0">Expired</span>
            @elseif($isFullyUsed)
                <span class="ml-auto px-2 py-0.5 rounded-full text-[8px] font-bold bg-primary-fixed-dim/20 text-primary-fixed-dim border border-primary-fixed-dim/40 shrink-0">Habis</span>
            @else
                <span class="ml-auto px-2 py-0.5 rounded-full text-[8px] font-bold bg-accent-clear/15 text-accent-clear border border-accent-clear/30 shrink-0">Tersedia</span>
            @endif
        </div>

        {{-- Voucher name + type badge --}}
        <div class="mb-1 relative z-10">
            <h3 class="font-black text-inverse-on-surface text-sm leading-tight truncate" title="{{ $v->name }}">
                {{ $v->name }}
            </h3>
            @if($v->is_event && $v->event_name)
                <span class="inline-flex items-center gap-1 text-[8px] font-bold uppercase tracking-widest bg-rose-950 text-rose-300 border border-rose-800/60 px-2 py-0.5 rounded-full mt-1">
                    <i class="fas fa-star text-[7px]"></i> {{ $v->event_name }}
                </span>
            @elseif($v->points_required > 0)
                <span class="inline-flex items-center gap-1 text-[8px] font-bold uppercase tracking-widest bg-amber-950 text-amber-300 border border-amber-800/60 px-2 py-0.5 rounded-full mt-1">
                    <i class="fas fa-coins text-[7px]"></i> {{ $v->points_required }} PTS
                </span>
            @endif
        </div>

        {{-- Meta: min purchase + expiry in one compact line --}}
        <div class="flex items-center gap-2 text-[9px] text-outline-variant mb-1.5 relative z-10">
            <span class="flex items-center gap-1">
                <i class="fas fa-shopping-bag text-[8px] text-outline-variant/70"></i>
                {{ $v->min_purchase > 0 ? 'Min Rp '.number_format($v->min_purchase, 0, ',', '.') : 'Tanpa Min.' }}
            </span>
            <span class="text-outline-variant/50">·</span>
            <span class="flex items-center gap-1">
                <i class="far fa-calendar-alt text-[8px] text-outline-variant/70"></i>
                s/d {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}
            </span>
        </div>

        {{-- Quota bar --}}
        @if($isClaimed)
            <div class="w-full h-1 bg-emerald-950 rounded-full overflow-hidden mb-1.5 relative z-10">
                <div class="h-1 rounded-full bg-gradient-to-r from-emerald-500 to-teal-400 w-full"></div>
            </div>
        @else
            <div class="mb-1.5 relative z-10">
                <div class="flex justify-between text-[8px] text-outline-variant mb-0.5">
                    <span>Sisa Kuota</span>
                    <span class="font-mono font-bold text-inverse-on-surface">{{ max(0, $v->quota - $v->used_count) }}/{{ $v->quota }}</span>
                </div>
                <div class="w-full h-1 rounded-full overflow-hidden bg-on-surface">
                    <div class="h-1 rounded-full bg-gradient-to-r from-primary via-amber-500 to-emerald-400 transition-all duration-500"
                         style="width:{{ $percentUsed }}%"></div>
                </div>
            </div>
        @endif

        {{-- Voucher code copy --}}
        <button type="button"
                onclick="navigator.clipboard.writeText('{{ $v->code }}'); alert('Kode {{ $v->code }} berhasil disalin!')"
                title="Salin kode voucher"
                class="self-start inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md cursor-pointer transition-colors bg-on-surface/80 hover:bg-on-surface border border-outline-variant/30 relative z-10">
            <i class="fas fa-ticket-alt text-primary-fixed-dim text-[9px]"></i>
            <span class="font-mono font-extrabold text-[9px] tracking-widest text-accent-clear">{{ $v->code }}</span>
            <i class="far fa-copy text-outline-variant text-[8px]"></i>
        </button>
    </div>

    {{-- ===== NOTCH SEPARATOR ===== --}}
    <div class="relative flex flex-col items-center justify-between shrink-0 pointer-events-none z-10" style="width:20px;">
        {{-- top notch --}}
        <div class="w-5 h-5 rounded-full border border-outline-variant/30 shrink-0 bg-gray-100" style="margin-top:-10px;"></div>
        {{-- dashed divider --}}
        <div class="flex-1 w-0 border-l-2 border-dashed border-outline-variant/30"></div>
        {{-- bottom notch --}}
        <div class="w-5 h-5 rounded-full border border-outline-variant/30 shrink-0 bg-gray-100" style="margin-bottom:-10px;"></div>
    </div>

    {{-- ===== RIGHT PANEL – solid accent value panel ===== --}}
    <div class="relative flex flex-col items-center justify-between rounded-r-2xl shrink-0 overflow-hidden"
         style="width:118px; padding:14px 10px; background-color:{{ $panelBg }}; color:{{ $panelText }};">

        {{-- Decorative circles --}}
        <div class="absolute -top-8 -right-8 w-20 h-20 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute -bottom-6 -left-6 w-16 h-16 rounded-full bg-white/10 pointer-events-none"></div>

        {{-- "VOUCHER" eyebrow --}}
        <span class="relative text-[9px] font-bold uppercase tracking-widest opacity-75">Voucher</span>

        {{-- Big value --}}
        <div class="relative text-center my-1">
            @if($v->type === 'percentage')
                <div class="font-black leading-none" style="font-size:36px; font-family:'Playfair Display',serif; line-height:1;">
                    {{ (int)$v->value }}%
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wide mt-0.5 opacity-80">Diskon</div>
                @if($v->max_discount)
                    <div class="text-[9px] opacity-60 mt-0.5">Maks Rp {{ number_format($v->max_discount,0,',','.') }}</div>
                @endif
            @else
                <div class="font-black leading-tight" style="font-size:18px; font-family:'Playfair Display',serif;">
                    Rp {{ number_format($v->value,0,',','.') }}
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wide mt-0.5 opacity-80">Potongan</div>
            @endif
        </div>

        {{-- Subtext --}}
        <span class="relative text-[9px] font-bold opacity-60">@yaliabeauty</span>

        {{-- CTA / action --}}
        <div class="relative w-full mt-2">
            @if($isUserUsed)
                <span class="block w-full text-center text-[9px] font-bold py-1.5 px-2 rounded-full bg-black/30 text-white/70 border border-white/10">
                    Terpakai
                </span>
            @elseif($isClaimed)
                <a href="{{ route('user.bookings.create') }}"
                   class="flex items-center justify-center gap-1 w-full text-[9px] font-black py-1.5 px-2 rounded-full
                          bg-white shadow-sm hover:bg-slate-100 transition-all text-center"
                   style="color: #0f172a !important; background-color: #ffffff !important;">
                    <i class="fas fa-calendar-plus text-[8px]" style="color: #0f172a !important;"></i> Gunakan
                </a>
            @elseif($isExpired)
                <span class="block w-full text-center text-[9px] font-bold py-1.5 px-2 rounded-full bg-black/30 text-white/90 border border-white/20">
                    Kadaluarsa
                </span>
            @elseif($isFullyUsed)
                <span class="block w-full text-center text-[9px] font-bold py-1.5 px-2 rounded-full bg-black/30 text-white/90 border border-white/20">
                    Kuota Habis
                </span>
            @else
                <form method="POST" action="{{ route('user.vouchers.claim', $v->id) }}">
                    @csrf
                    @if($v->points_required > 0)
                        @if($hasEnoughPoints)
                            <button type="submit"
                                    class="w-full text-center text-[9px] font-black py-1.5 px-2 rounded-full
                                           bg-white shadow-sm hover:bg-slate-100 transition-all cursor-pointer"
                                    style="color: #0f172a !important; background-color: #ffffff !important;">
                                <i class="fas fa-coins text-[8px]" style="color: #d97706 !important;"></i> Tukar {{ $v->points_required }} PTS
                            </button>
                        @else
                            <button type="button" disabled
                                    class="w-full text-center text-[9px] font-bold py-1.5 px-2 rounded-full
                                           bg-black/30 text-white/60 cursor-not-allowed border border-white/10"
                                    title="Poin tidak cukup (Butuh {{ $v->points_required }} PTS)">
                                <i class="fas fa-lock text-[8px]"></i> PTS Kurang
                            </button>
                        @endif
                    @elseif($v->is_event)
                        <button type="submit"
                                class="w-full text-center text-[9px] font-black py-1.5 px-2 rounded-full
                                       bg-white shadow-sm hover:bg-slate-100 transition-all cursor-pointer"
                                style="color: #0f172a !important; background-color: #ffffff !important;">
                            Klaim 🎁
                        </button>
                    @else
                        <button type="submit"
                                class="w-full text-center text-[9px] font-black py-1.5 px-2 rounded-full
                                       bg-white shadow-sm hover:bg-slate-100 transition-all cursor-pointer"
                                style="color: #0f172a !important; background-color: #ffffff !important;">
                            Klaim Gratis
                        </button>
                    @endif
                </form>
            @endif
        </div>

    </div>

</div>
