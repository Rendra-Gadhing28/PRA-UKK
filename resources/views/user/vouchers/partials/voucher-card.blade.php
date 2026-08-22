@php
    $isExpired    = \Carbon\Carbon::parse($v->valid_until)->isPast();
    $isFullyUsed  = $v->used_count >= $v->quota;
    $percentUsed  = min(100, round(($v->used_count / max(1, $v->quota)) * 100));
    $isClaimed    = in_array($v->id, $claimedVoucherIds ?? []);
    $userPoints   = $user->total_points ?? 0;
    $hasEnoughPoints = $v->points_required > 0 ? ($userPoints >= $v->points_required) : true;

    // Right-panel accent using inline styles (avoids Tailwind JIT purge issue)
    if ($isExpired || $isFullyUsed) {
        $panelBg   = '#d1d5db'; // gray-300
        $panelText = '#4b5563'; // gray-600
    } elseif ($isClaimed) {
        $panelBg   = '#34d399'; // emerald-400
        $panelText = '#ffffff';
    } elseif (!empty($v->is_event)) {
        $panelBg   = '#8b5cf6'; // violet-500
        $panelText = '#ffffff';
    } elseif ($v->points_required > 0) {
        $panelBg   = '#fbbf24'; // amber-400
        $panelText = '#451a03'; // amber-950
    } else {
        $panelBg   = '#f45472'; // rose brand
        $panelText = '#ffffff';
    }
@endphp

{{-- ===== HORIZONTAL TICKET CARD ===== --}}
<div class="relative flex flex-row rounded-[20px] overflow-hidden
            border border-rose-100
            shadow-[0_6px_28px_rgba(176,31,68,0.08)]
            hover:shadow-[0_14px_46px_rgba(176,31,68,0.14)] hover:-translate-y-0.5
            transition-all duration-300 group"
     style="min-height:148px; background: linear-gradient(135deg, #ffd6e0 0%, #ffe4ec 50%, #ffc9d8 100%);">

    {{-- ===== LEFT PANEL – soft pink info panel ===== --}}
    <div class="flex-1 min-w-0 flex flex-col justify-between p-4 pr-5 relative overflow-hidden">

        {{-- Decorative soft blobs --}}
        <div class="absolute -top-4 -left-4 w-16 h-16 rounded-full pointer-events-none" style="background:rgba(244,84,114,0.08); filter:blur(12px);"></div>
        <div class="absolute bottom-2 right-2 w-12 h-12 rounded-full pointer-events-none" style="background:rgba(255,143,164,0.12); filter:blur(10px);"></div>

        {{-- Brand header + status --}}
        <div class="flex items-center gap-1.5 mb-1.5">
            <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}"
                 alt="Yalia Logo"
                 class="w-4 h-4 object-contain rounded-full bg-rose-50 p-0.5 border border-rose-200 shrink-0">
            <span class="text-[8px] font-semibold text-rose-400 tracking-widest uppercase">Yalia Beauty</span>

            @if($isClaimed)
                <span class="ml-auto flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[8px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200 shrink-0">
                    <i class="fas fa-check-circle text-[8px]"></i> Diklaim
                </span>
            @elseif($isExpired)
                <span class="ml-auto px-1.5 py-0.5 rounded-full text-[8px] font-bold bg-white/60 text-gray-400 border border-gray-200 shrink-0">Expired</span>
            @elseif($isFullyUsed)
                <span class="ml-auto px-1.5 py-0.5 rounded-full text-[8px] font-bold bg-white/60 text-rose-400 border border-rose-200 shrink-0">Habis</span>
            @else
                <span class="ml-auto px-1.5 py-0.5 rounded-full text-[8px] font-bold bg-white/60 text-rose-500 border border-rose-200 shrink-0">Tersedia</span>
            @endif
        </div>

        {{-- Voucher name + type badge --}}
        <div class="mb-1">
            <h3 class="font-black text-gray-900 text-sm leading-tight truncate" title="{{ $v->name }}">
                {{ $v->name }}
            </h3>
            @if($v->is_event && $v->event_name)
                <span class="inline-flex items-center gap-1 text-[8px] font-bold uppercase tracking-widest bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full mt-0.5">
                    <i class="fas fa-star text-[7px]"></i> {{ $v->event_name }}
                </span>
            @elseif($v->points_required > 0)
                <span class="inline-flex items-center gap-1 text-[8px] font-bold uppercase tracking-widest bg-amber-50 text-amber-600 px-1.5 py-0.5 rounded-full mt-0.5">
                    <i class="fas fa-coins text-[7px]"></i> {{ $v->points_required }} PTS
                </span>
            @endif
        </div>

        {{-- Meta: min purchase + expiry in one compact line --}}
        <div class="flex items-center gap-2 text-[9px] text-rose-400/80 mb-1.5">
            <span class="flex items-center gap-0.5">
                <i class="fas fa-shopping-bag text-[8px]"></i>
                {{ $v->min_purchase > 0 ? 'Min Rp '.number_format($v->min_purchase, 0, ',', '.') : 'Tanpa Min.' }}
            </span>
            <span class="text-rose-200">·</span>
            <span class="flex items-center gap-0.5">
                <i class="far fa-calendar-alt text-[8px]"></i>
                s/d {{ \Carbon\Carbon::parse($v->valid_until)->format('d M Y') }}
            </span>
        </div>

        {{-- Quota bar --}}
        @if($isClaimed)
            <div class="w-full h-1 bg-emerald-100 rounded-full overflow-hidden mb-1.5">
                <div class="h-1 rounded-full bg-gradient-to-r from-emerald-400 to-teal-400 w-full"></div>
            </div>
        @else
            <div class="mb-1.5">
                <div class="flex justify-between text-[8px] text-rose-400/70 mb-0.5">
                    <span>Sisa Kuota</span>
                    <span class="font-mono font-bold">{{ max(0, $v->quota - $v->used_count) }}/{{ $v->quota }}</span>
                </div>
                <div class="w-full h-1 rounded-full overflow-hidden" style="background:rgba(244,84,114,0.12);">
                    <div class="h-1 rounded-full bg-gradient-to-r from-[#f45472] to-[#ff8fa4] transition-all duration-500"
                         style="width:{{ $percentUsed }}%"></div>
                </div>
            </div>
        @endif

        {{-- Voucher code copy --}}
        <button type="button"
                onclick="navigator.clipboard.writeText('{{ $v->code }}'); alert('Kode {{ $v->code }} berhasil disalin!')"
                title="Salin kode voucher"
                class="self-start inline-flex items-center gap-1 px-2 py-0.5 rounded-md cursor-pointer transition-colors"
                style="background:rgba(255,255,255,0.6); border:1px dashed rgba(244,84,114,0.4);">
            <i class="fas fa-ticket-alt text-[#f45472] text-[8px]"></i>
            <span class="font-mono font-extrabold text-[9px] tracking-widest text-[#f45472]">{{ $v->code }}</span>
            <i class="far fa-copy text-rose-300 text-[8px]"></i>
        </button>
    </div>

    {{-- ===== NOTCH SEPARATOR ===== --}}
    <div class="relative flex flex-col items-center justify-between shrink-0 pointer-events-none" style="width:20px;">
        {{-- top notch --}}
        <div class="w-5 h-5 rounded-full border border-rose-100 shrink-0" style="background:#fdf5f6; margin-top:-10px;"></div>
        {{-- dashed divider --}}
        <div class="flex-1 w-0 border-l-2 border-dashed border-rose-100"></div>
        {{-- bottom notch --}}
        <div class="w-5 h-5 rounded-full border border-rose-100 shrink-0" style="background:#fdf5f6; margin-bottom:-10px;"></div>
    </div>

    {{-- ===== RIGHT PANEL – solid accent value panel ===== --}}
    <div class="relative flex flex-col items-center justify-between rounded-r-[20px] shrink-0 overflow-hidden"
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
            @if($isClaimed)
                <span class="flex items-center justify-center gap-1 w-full text-[9px] font-bold py-1.5 px-2 rounded-full
                             bg-white/25 border border-white/30 backdrop-blur-sm">
                    <i class="fas fa-check text-[8px]"></i> Tersimpan
                </span>
            @elseif($isExpired)
                <span class="block w-full text-center text-[9px] font-bold py-1.5 px-2 rounded-full bg-black/10 opacity-60">
                    Kadaluarsa
                </span>
            @elseif($isFullyUsed)
                <span class="block w-full text-center text-[9px] font-bold py-1.5 px-2 rounded-full bg-black/10 opacity-60">
                    Kuota Habis
                </span>
            @else
                <form method="POST" action="{{ route('user.vouchers.claim', $v->id) }}">
                    @csrf
                    @if($v->points_required > 0)
                        @if($hasEnoughPoints)
                            <button type="submit"
                                    class="w-full text-center text-[9px] font-extrabold py-1.5 px-2 rounded-full
                                           bg-white/25 hover:bg-white/40 border border-white/40 backdrop-blur-sm
                                           transition-all cursor-pointer">
                                <i class="fas fa-coins text-[8px]"></i> Tukar {{ $v->points_required }} PTS
                            </button>
                        @else
                            <button type="button" disabled
                                    class="w-full text-center text-[9px] font-bold py-1.5 px-2 rounded-full
                                           bg-black/10 opacity-50 cursor-not-allowed"
                                    title="Poin tidak cukup (Butuh {{ $v->points_required }} PTS)">
                                <i class="fas fa-lock text-[8px]"></i> PTS Kurang
                            </button>
                        @endif
                    @elseif($v->is_event)
                        <button type="submit"
                                class="w-full text-center text-[9px] font-extrabold py-1.5 px-2 rounded-full
                                       bg-white/25 hover:bg-white/40 border border-white/40 backdrop-blur-sm
                                       transition-all cursor-pointer">
                            Klaim 🎁
                        </button>
                    @else
                        <button type="submit"
                                class="w-full text-center text-[9px] font-extrabold py-1.5 px-2 rounded-full
                                       bg-white/25 hover:bg-white/40 border border-white/40 backdrop-blur-sm
                                       transition-all cursor-pointer">
                            Klaim Gratis
                        </button>
                    @endif
                </form>
            @endif
        </div>

    </div>

</div>
