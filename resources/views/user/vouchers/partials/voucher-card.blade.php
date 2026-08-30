@php
    $isExpired    = \Carbon\Carbon::parse($v->valid_until)->isPast();
    $isFullyUsed  = $v->used_count >= $v->quota;
    $percentUsed  = min(100, round(($v->used_count / max(1, $v->quota)) * 100));
    $isClaimed    = in_array($v->id, $claimedVoucherIds ?? []) || isset($userVoucher);
    $isUserUsed   = isset($userVoucher) ? (bool)$userVoucher->is_used : false;
    $userPoints   = $user->total_points ?? 0;
    $hasEnoughPoints = $v->points_required > 0 ? ($userPoints >= $v->points_required) : true;

    // Old Money / Luxury Tone right-panel accents:
    if ($isUserUsed || $isExpired || $isFullyUsed) {
        $panelStyle = 'background: linear-gradient(135deg, #4A4648 0%, #333032 100%); color: #F3F4F6;';
        $btnStyle   = 'color: #1F2937 !important; background-color: #FFFFFF !important;';
    } 
    elseif (!empty($v->is_event)) {
        // Luxury Rosewood / Royal Crimson (#7A1F35 to #9B2C46)
        $panelStyle = 'background: linear-gradient(135deg, #7A1F35 0%, #9B2C46 100%); color: #FFFFFF;';
        $btnStyle   = 'color: #7A1F35 !important; background-color: #FFFFFF !important;';
    } 
    elseif ($isClaimed) {
        // Deep Muted Forest Emerald Jade (#264E3D to #3B6E57)
        $panelStyle = 'background: linear-gradient(135deg, #264E3D 0%, #3B6E57 100%); color: #FFFFFF;';
        $btnStyle   = 'color: #1A382B !important; background-color: #FFFFFF !important;';
    } 
    elseif ($v->points_required > 0) {
        // Antique Champagne Gold / Bronze (#8C6D37 to #B89355)
        $panelStyle = 'background: linear-gradient(135deg, #8C6D37 0%, #B89355 100%); color: #FFFFFF;';
        $btnStyle   = 'color: #5C441D !important; background-color: #FFFFFF !important;';
    } 
    else {
        // Luxury Burgundy Rose (#7A1F35 to #9B2C46)
        $panelStyle = 'background: linear-gradient(135deg, #7A1F35 0%, #9B2C46 100%); color: #FFFFFF;';
        $btnStyle   = 'color: #7A1F35 !important; background-color: #FFFFFF !important;';
    }
@endphp

{{-- ===== HORIZONTAL LUXURY TICKET CARD ===== --}}
<div class="relative flex flex-row rounded-2xl overflow-hidden transition-all duration-300 group hover:-translate-y-1 hover:shadow-xl"
     style="min-height:148px; background: rgba(255, 250, 252, 0.92); backdrop-filter: blur(16px); border: 1px solid rgba(220, 180, 190, 0.5); box-shadow: 0 10px 25px rgba(122, 31, 53, 0.07);">

    {{-- ===== LEFT PANEL – Frosted Glass Info Panel ===== --}}
    <div class="flex-1 min-w-0 flex flex-col justify-between p-4 pr-5 relative overflow-hidden">

        {{-- Decorative soft ambient glows --}}
        <div class="absolute -top-4 -left-4 w-20 h-20 rounded-full pointer-events-none bg-[#7A1F35]/5 blur-xl"></div>
        <div class="absolute bottom-2 right-2 w-16 h-16 rounded-full pointer-events-none bg-[#8C6D37]/5 blur-lg"></div>

        {{-- Brand header + status --}}
        <div class="flex items-center gap-1.5 mb-1.5 relative z-10">
            <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}"
                 alt="Yalia Logo"
                 class="w-4 h-4 object-contain rounded-full bg-white p-0.5 border border-[#F4DDE1] shrink-0 shadow-2xs">
            <span class="text-xs font-bold tracking-widest uppercase" style="color: #7A4B56 !important;">Yalia Beauty</span>

            @if($isUserUsed)
                <span class="ml-auto flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-300 shrink-0">
                    Terpakai
                </span>
            @elseif($isClaimed)
                <span class="ml-auto flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#EBF7F2] text-[#264E3D] border border-[#A8DEC9] shrink-0">
                    <i class="fas fa-check-circle text-xs"></i> Diklaim
                </span>
            @elseif($isExpired)
                <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-300 shrink-0">Expired</span>
            @elseif($isFullyUsed)
                <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500 border border-gray-300 shrink-0">Habis</span>
            @else
                <span class="ml-auto px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#FFF0F3] text-[#7A1F35] border border-[#F4DDE1] shrink-0">Tersedia</span>
            @endif
        </div>

        {{-- Voucher name + type badge --}}
        <div class="mb-1 relative z-10">
            <h3 class="font-black text-sm leading-tight truncate" style="color: #2B0F23 !important;" title="{{ $v->name }}">
                {{ $v->name }}
            </h3>
            @if($v->is_event && $v->event_name)
                <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-widest bg-[#FFF0F3] text-[#7A1F35] border border-[#F4DDE1] px-2 py-0.5 rounded-full mt-1">
                    <i class="fas fa-star text-xs text-[#7A1F35]"></i> {{ $v->event_name }}
                </span>
            @elseif($v->points_required > 0)
                <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-widest bg-[#FAF3E8] text-[#8C6D37] border border-[#E8D4B5] px-2 py-0.5 rounded-full mt-1">
                    <i class="fas fa-coins text-xs text-[#8C6D37]"></i> {{ $v->points_required }} PTS
                </span>
            @endif
        </div>

        {{-- Meta: min purchase + expiry in one compact line --}}
        <div class="flex items-center gap-2 text-xs mb-1.5 relative z-10 font-medium" style="color: #594043 !important;">
            <span class="flex items-center gap-1">
                <i class="fas fa-shopping-bag text-xs opacity-70"></i>
                {{ $v->min_purchase > 0 ? 'Min Rp '.number_format($v->min_purchase, 0, ',', '.') : 'Tanpa Min.' }}
            </span>
            <span class="opacity-40">·</span>
            <span class="flex items-center gap-1">
                <i class="far fa-calendar-alt text-xs opacity-70"></i>
                s/d {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}
            </span>
        </div>

        {{-- Quota bar --}}
        @if($isClaimed)
            <div class="w-full h-1.5 bg-[#E2F0EA] rounded-full overflow-hidden mb-1.5 relative z-10">
                <div class="h-full rounded-full bg-[#264E3D] w-full"></div>
            </div>
        @else
            <div class="mb-1.5 relative z-10">
                <div class="flex justify-between text-xs mb-0.5 font-semibold" style="color: #7A4B56 !important;">
                    <span>Sisa Kuota</span>
                    <span class="font-mono font-bold" style="color: #2B0F23 !important;">{{ max(0, $v->quota - $v->used_count) }}/{{ $v->quota }}</span>
                </div>
                <div class="w-full h-1.5 rounded-full overflow-hidden bg-[#F4DDE1]">
                    <div class="h-full rounded-full transition-all duration-500"
                         style="width:{{ $percentUsed }}%; background: linear-gradient(to right, #7A1F35, #8C6D37);"></div>
                </div>
            </div>
        @endif

        {{-- Voucher code copy --}}
        <button type="button"
                onclick="navigator.clipboard.writeText('{{ $v->code }}'); if (window.Alpine && window.Alpine.store('toast')) { window.Alpine.store('toast').show('Kode {{ $v->code }} disalin!'); } else { alert('Kode {{ $v->code }} berhasil disalin!'); }"
                title="Salin kode voucher"
                class="self-start inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md cursor-pointer transition-all shadow-2xs relative z-10"
                style="background: rgba(255, 240, 245, 0.9); border: 1px solid rgba(176, 31, 68, 0.2); color: #7A1F35 !important;">
            <i class="fas fa-ticket-alt text-xs" style="color: #7A1F35 !important;"></i>
            <span class="font-mono font-extrabold text-xs tracking-widest" style="color: #7A1F35 !important;">{{ $v->code }}</span>
            <i class="far fa-copy text-xs opacity-70"></i>
        </button>
    </div>

    {{-- ===== NOTCH SEPARATOR ===== --}}
    <div class="relative flex flex-col items-center justify-between shrink-0 pointer-events-none z-10" style="width:16px;">
        {{-- top notch --}}
        <div class="w-4 h-4 rounded-full border border-pink-200/60 shrink-0" style="margin-top:-8px; background: #FDE2ED;"></div>
        {{-- dashed divider --}}
        <div class="flex-1 w-0 border-l border-dashed border-[#F4DDE1]"></div>
        {{-- bottom notch --}}
        <div class="w-4 h-4 rounded-full border border-pink-200/60 shrink-0" style="margin-bottom:-8px; background: #F7C6DE;"></div>
    </div>

    {{-- ===== RIGHT PANEL – Old Money Luxury Accent Panel ===== --}}
    <div class="relative flex flex-col items-center justify-between rounded-r-2xl shrink-0 overflow-hidden"
         style="width:124px; padding:14px 10px; {{ $panelStyle }}">

        {{-- Decorative circles --}}
        <div class="absolute -top-8 -right-8 w-20 h-20 rounded-full bg-white/10 pointer-events-none"></div>
        <div class="absolute -bottom-6 -left-6 w-16 h-16 rounded-full bg-white/10 pointer-events-none"></div>

        {{-- "VOUCHER" eyebrow --}}
        <span class="relative text-xs font-bold uppercase tracking-widest opacity-80">Voucher</span>

        {{-- Big value --}}
        <div class="relative text-center my-1">
            @if($v->type === 'percentage')
                <div class="text-3xl font-black leading-none" style="font-family:'Playfair Display',serif;">
                    {{ (int)$v->value }}%
                </div>
                <div class="text-xs font-bold uppercase tracking-wide mt-0.5 opacity-90">Diskon</div>
                @if($v->max_discount)
                    <div class="text-xs opacity-75 mt-0.5">Maks Rp {{ number_format($v->max_discount,0,',','.') }}</div>
                @endif
            @else
                <div class="text-lg font-black leading-tight" style="font-family:'Playfair Display',serif;">
                    Rp {{ number_format($v->value,0,',','.') }}
                </div>
                <div class="text-xs font-bold uppercase tracking-wide mt-0.5 opacity-90">Potongan</div>
            @endif
        </div>

        {{-- Subtext --}}
        <span class="relative text-xs font-bold opacity-75 tracking-wider">@yaliabeauty</span>

        {{-- CTA / action --}}
        <div class="relative w-full mt-2">
            @if($isUserUsed)
                <span class="block w-full text-center text-xs font-bold py-1.5 px-2 rounded-full bg-black/25 text-white/80 border border-white/15">
                    Terpakai
                </span>
            @elseif($isClaimed)
                <a href="{{ route('user.bookings.create') }}"
                   class="flex items-center justify-center gap-1 w-full text-xs font-black py-1.5 px-2 rounded-full shadow-sm hover:opacity-95 transition-all text-center"
                   style="{{ $btnStyle }}">
                    <i class="fas fa-calendar-plus text-xs"></i> Gunakan
                </a>
            @elseif($isExpired)
                <span class="block w-full text-center text-xs font-bold py-1.5 px-2 rounded-full bg-black/25 text-white/80 border border-white/15">
                    Kadaluarsa
                </span>
            @elseif($isFullyUsed)
                <span class="block w-full text-center text-xs font-bold py-1.5 px-2 rounded-full bg-black/25 text-white/80 border border-white/15">
                    Kuota Habis
                </span>
            @else
                <form method="POST" action="{{ route('user.vouchers.claim', $v->id) }}">
                    @csrf
                    @if($v->points_required > 0)
                        @if($hasEnoughPoints)
                            <button type="submit"
                                    class="w-full text-center text-xs font-black py-1.5 px-2 rounded-full shadow-sm hover:opacity-95 transition-all cursor-pointer flex items-center justify-center gap-1"
                                    style="{{ $btnStyle }}">
                                <i class="fas fa-coins text-xs"></i> Tukar {{ $v->points_required }} PTS
                            </button>
                        @else
                            <button type="button" disabled
                                    class="w-full text-center text-xs font-bold py-1.5 px-2 rounded-full bg-black/25 text-white/60 cursor-not-allowed border border-white/15 flex items-center justify-center gap-1"
                                    title="Poin tidak cukup (Butuh {{ $v->points_required }} PTS)">
                                <i class="fas fa-lock text-xs"></i> PTS Kurang
                            </button>
                        @endif
                    @elseif($v->is_event)
                        <button type="submit"
                                class="w-full text-center text-xs font-black py-1.5 px-2 rounded-full shadow-sm hover:opacity-95 transition-all cursor-pointer flex items-center justify-center gap-1"
                                style="{{ $btnStyle }}">
                            <i class="fa-solid fa-gift text-xs"></i>
                            <span>Klaim</span>
                        </button>
                    @else
                        <button type="submit"
                                class="w-full text-center text-xs font-black py-1.5 px-2 rounded-full shadow-sm hover:opacity-95 transition-all cursor-pointer"
                                style="{{ $btnStyle }}">
                            Klaim Gratis
                        </button>
                    @endif
                </form>
            @endif
        </div>

    </div>

</div>
