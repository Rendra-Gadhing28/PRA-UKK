{{-- ── Booking History Cards ──────────────────────────────────── --}}

<style>
    @keyframes card-rise {
        from { opacity:0; transform:translateY(24px) scale(0.98); }
        to   { opacity:1; transform:translateY(0)    scale(1); }
    }
    .bk-card {
        animation: card-rise .46s cubic-bezier(0.22,0.61,0.36,1) both;
    }
    /* Subtle shimmer on photo placeholder */
    @keyframes ph-shimmer {
        0%   { background-position:-200% center; }
        100% { background-position: 200% center; }
    }
    .ph-shimmer {
        background: linear-gradient(100deg, #f4dde1 30%, #fff0f2 50%, #f4dde1 70%);
        background-size: 200% auto;
        animation: ph-shimmer 2s cubic-bezier(0.7,0,0.25,1) infinite;
    }
</style>

{{-- ══════ EMPTY STATE ══════ --}}
@if ($bookings->isEmpty())
<div class="bk-card flex flex-col items-center gap-5 py-20 text-center
            bg-surface-container-lowest rounded-3xl border border-outline-variant/30 shadow-sm">
    <div class="w-20 h-20 rounded-full bg-primary-fixed flex items-center justify-center text-primary shadow-inner">
        <i class="fas fa-calendar-xmark text-3xl"></i>
    </div>
    <div>
        <h3 class="text-lg font-extrabold text-text-heading" style="font-family:'Playfair Display',serif">
            @switch($tab)
                @case('past')      Belum Ada Riwayat Selesai     @break
                @case('cancelled') Tidak Ada yang Dibatalkan      @break
                @default           Belum Ada Jadwal Mendatang
            @endswitch
        </h3>
        <p class="text-sm text-on-surface-variant mt-1.5 max-w-xs mx-auto">
            Saatnya manjakan diri! Pilih treatment kecantikan dan buat reservasi sekarang.
        </p>
    </div>
    <a href="{{ route('user.treatments.index') }}"
       class="inline-flex items-center gap-2 px-7 py-3 rounded-full bg-primary text-on-primary text-xs font-bold shadow-md
              hover:bg-primary-container hover:shadow-lg active:scale-95
              transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]">
        <i class="fas fa-sparkles text-[10px]"></i>
        Eksplorasi Treatment
    </a>
</div>

{{-- ══════ CARD LIST ══════ --}}
@else
<div class="grid grid-cols-1 gap-5">
    @foreach ($bookings as $i => $booking)
        @php
            /* ── Status resolution ── */
            $statusObj = is_string($booking->status)
                ? \App\Enums\BookingStatus::tryFrom($booking->status)
                : $booking->status;
            $statusVal = is_object($statusObj) && isset($statusObj->value)
                ? $statusObj->value
                : (string) $booking->status;

            if ($booking->payment_status === 'paid' && $statusVal === 'pending') {
                $statusVal = 'confirmed';
                $statusObj = \App\Enums\BookingStatus::CONFIRMED;
            }

            $badgeLabel  = is_object($statusObj) && method_exists($statusObj, 'badgeLabel')
                ? $statusObj->badgeLabel() : ucfirst($statusVal);

            /* ── Status badge colour (text only, no bg clash) ── */
            $statusColor = match($statusVal) {
                'completed'             => ['dot'=>'bg-emerald-500', 'text'=>'text-emerald-700', 'bg'=>'bg-emerald-50', 'border'=>'border-emerald-200'],
                'canceled', 'cancelled' => ['dot'=>'bg-red-400',     'text'=>'text-red-600',     'bg'=>'bg-red-50',     'border'=>'border-red-200'],
                'in_progress'           => ['dot'=>'bg-blue-500',    'text'=>'text-blue-700',    'bg'=>'bg-blue-50',    'border'=>'border-blue-200'],
                'confirmed'             => ['dot'=>'bg-primary',     'text'=>'text-primary',     'bg'=>'bg-primary-fixed','border'=>'border-primary-fixed-dim'],
                default                 => ['dot'=>'bg-amber-400',   'text'=>'text-amber-700',   'bg'=>'bg-amber-50',   'border'=>'border-amber-200'],
            };

            /* ── Payment badge ── */
            $payColor = match($booking->payment_status) {
                'paid'     => ['text'=>'text-emerald-700', 'bg'=>'bg-emerald-50',  'border'=>'border-emerald-200', 'icon'=>'fa-circle-check'],
                'refunded' => ['text'=>'text-indigo-700',  'bg'=>'bg-indigo-50',   'border'=>'border-indigo-200',  'icon'=>'fa-rotate-left'],
                'pending'  => ['text'=>'text-amber-700',   'bg'=>'bg-amber-50',    'border'=>'border-amber-200',   'icon'=>'fa-clock'],
                default    => ['text'=>'text-red-600',     'bg'=>'bg-red-50',      'border'=>'border-red-200',     'icon'=>'fa-circle-xmark'],
            };
            $payLabel = match($booking->payment_status) {
                'paid'    => 'Lunas',   'refunded' => 'Refunded',
                'pending' => 'Menunggu', default   => 'Belum Bayar',
            };

            /* ── Treatment & time ── */
            $firstTreatment = $booking->treatments->first();
            $treatmentNames = $booking->treatments->count() > 0
                ? $booking->treatments->pluck('name')->join(' · ')
                : ($booking->treatment?->name ?? 'Perawatan Yalia');
            $treatmentCount = $booking->treatments->count();
            $totalDuration  = $booking->treatments->sum('duration_minutes')
                ?: ($firstTreatment?->duration_minutes ?? 0);
            $tStart = $booking->time_start ? \Carbon\Carbon::parse($booking->time_start)->format('H:i') : '-';
            $tEnd   = $booking->time_end   ? \Carbon\Carbon::parse($booking->time_end)->format('H:i')   : '-';
            $dateStr = $booking->booking_date
                ? $booking->booking_date->translatedFormat('d M Y')
                : '-';
            $dayStr  = $booking->booking_date
                ? $booking->booking_date->translatedFormat('l')
                : '';

            /* ── Accent border per status ── */
            $accentBorder = match($statusVal) {
                'completed'             => 'border-l-emerald-400',
                'canceled','cancelled'  => 'border-l-red-400',
                'in_progress'           => 'border-l-blue-400',
                'confirmed'             => 'border-l-primary',
                default                 => 'border-l-amber-400',
            };

            $heroPhoto = \App\Support\ImageHelper::url($booking->photo_assign ?? $firstTreatment?->images, $firstTreatment?->image_url);

            $isPending   = $statusVal === 'pending' && $booking->payment_status !== 'paid';
            $isCompleted = $statusVal === 'completed';
            $isCancelled = in_array($statusVal, ['canceled','cancelled']);

            $delay = $i * 55;
        @endphp

        {{-- ══ Card ══ --}}
        <div class="bk-card group relative bg-white rounded-3xl overflow-hidden
                    border border-slate-200/80 border-l-4 {{ $accentBorder }}
                    shadow-[0_4px_20px_rgba(0,0,0,0.06)] hover:shadow-[0_12px_32px_rgba(0,0,0,0.12)] hover:-translate-y-0.5
                    transition-all duration-[380ms] ease-[cubic-bezier(0.22,0.61,0.36,1)]
                    flex flex-row items-stretch"
             style="animation-delay:{{ $delay }}ms">

            {{-- ── THUMBNAIL (kiri - rasio presisi & tidak terpotong) ── --}}
            <div class="relative shrink-0 w-32 sm:w-40 bg-slate-50 border-r border-slate-100 flex items-center justify-center overflow-hidden">
                <div class="absolute inset-0 flex items-center justify-center p-2">
                    @if($heroPhoto)
                        <img src="{{ $heroPhoto }}"
                             alt="{{ $treatmentNames }}"
                             width="160"
                             height="160"
                             decoding="async"
                             class="w-full h-full object-cover rounded-2xl transition-transform duration-500
                                    ease-[cubic-bezier(0.22,0.61,0.36,1)] group-hover:scale-[1.05]"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-transparent to-black/5 pointer-events-none rounded-2xl"></div>
                    @else
                        <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}"
                             alt="Yalia Beauty"
                             width="160"
                             height="160"
                             decoding="async"
                             class="w-full h-full object-contain p-2 transition-transform duration-500 group-hover:scale-105">
                    @endif
                </div>

                {{-- Photo-assign badge (top) --}}
                @if($booking->photo_assign)
                    <div class="absolute top-2 left-0 right-0 flex justify-center z-10">
                        <span class="flex items-center gap-1 px-2.5 py-0.5 rounded-full
                                     bg-black/60 backdrop-blur-md text-white text-[9px] font-bold shadow-sm">
                            <i class="fas fa-camera-retro text-[8px]"></i> Foto Hasil
                        </span>
                    </div>
                @endif

                {{-- Status dot (bottom) --}}
                <div class="absolute bottom-2 left-0 right-0 flex justify-center z-10">
                    <span class="flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-bold border
                                 {{ $statusColor['bg'] }} {{ $statusColor['text'] }} {{ $statusColor['border'] }}
                                 backdrop-blur-md shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusColor['dot'] }} shrink-0"></span>
                        {{ $badgeLabel }}
                    </span>
                </div>
            </div>

            {{-- ── CARD BODY ── --}}
            <div class="flex-1 min-w-0 px-5 py-4 space-y-3">

                {{-- ── Booking code + Treatment Title ── --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        {{-- booking code --}}
                        <p class="font-mono text-[10px] font-bold text-on-surface-variant/80 mb-1 tracking-tight">
                            #{{ $booking->booking_code }}
                        </p>
                        <h3 class="font-bold text-text-heading text-[14px] leading-snug line-clamp-2 group-hover:text-primary transition-colors duration-200"
                            style="font-family:'Playfair Display',serif">
                            {{ $treatmentNames }}
                        </h3>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            @if($booking->booking_type === 'home_service' || $booking->booking_type === 'home')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                             bg-secondary-fixed text-on-secondary-fixed-variant border border-secondary-fixed-dim">
                                    <i class="fas fa-house-chimney text-[8px]"></i> Home Service
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                             bg-tertiary-fixed text-on-tertiary-fixed border border-tertiary-fixed-dim">
                                    <i class="fas fa-store text-[8px]"></i> Ke Salon
                                </span>
                            @endif
                            @if($treatmentCount > 1)
                                <span class="text-[10px] text-on-surface-variant font-medium">
                                    +{{ $treatmentCount - 1 }} treatment lain
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Total price —  right --}}
                    <div class="text-right shrink-0">
                        <p class="text-[9px] font-bold text-on-surface-variant uppercase tracking-widest">Total</p>
                        <p class="text-base font-extrabold text-primary leading-tight">
                            {{ $booking->formatted_total }}
                        </p>
                    </div>
                </div>

                {{-- ── Info pills row ── --}}
                <div class="flex flex-wrap gap-2">
                    {{-- Tanggal --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/60 text-[11px] font-semibold text-on-surface-variant shadow-2xs">
                        <i class="fas fa-calendar-day text-primary text-[10px]"></i>
                        <span>{{ $dayStr }}, {{ $dateStr }}</span>
                    </div>
                    {{-- Jam --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/60 text-[11px] font-semibold text-on-surface-variant shadow-2xs">
                        <i class="fas fa-clock text-secondary text-[10px]"></i>
                        <span>{{ $tStart }} – {{ $tEnd }} <span class="text-[9px]">({{ $totalDuration }}mnt)</span></span>
                    </div>
                    {{-- Beautician --}}
                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/60 text-[11px] font-semibold text-on-surface-variant shadow-2xs">
                        <i class="fas fa-user-sparkles text-tertiary text-[10px]"></i>
                        <span>{{ $booking->beautician?->name ?? 'Auto Assign' }}</span>
                    </div>
                </div>

                {{-- ── Divider ── --}}
                <div class="border-t border-slate-100"></div>

                {{-- ── Payment status + Actions ── --}}
                <div class="flex items-center justify-between gap-3">
                    {{-- Payment pill --}}
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-[11px] font-bold
                                 {{ $payColor['bg'] }} {{ $payColor['text'] }} {{ $payColor['border'] }}">
                        <i class="fas {{ $payColor['icon'] }} text-[10px]"></i>
                        {{ $payLabel }}
                        @if($booking->payment_method)
                            <span class="opacity-60 font-normal">· {{ strtoupper($booking->payment_method) }}</span>
                        @endif
                    </span>

                    {{-- Action buttons --}}
                    <div class="flex items-center gap-2">
                        @if(in_array($statusVal, ['pending', 'confirmed']))
                            <a href="{{ route('user.bookings.show', ['booking' => $booking, 'reschedule' => 1]) }}"
                               class="px-3 py-2 rounded-xl text-[11px] font-bold border border-rose-200 text-rose-700 bg-rose-50 hover:bg-rose-100 transition-all"
                               title="Ubah Tanggal/Jam Reservasi">
                                <i class="fas fa-calendar-pen text-[10px] mr-1"></i>Ganti Jadwal
                            </a>
                        @endif

                        {{-- Detail --}}
                        <a href="{{ route('user.bookings.show', $booking) }}"
                           class="px-4 py-2 rounded-xl text-[11px] font-bold
                                  bg-surface-container text-on-surface
                                  hover:bg-surface-container-high active:scale-95
                                  transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)]">
                            Detail
                        </a>

                        @if($isPending)
                            <a href="{{ route('user.bookings.payment', $booking) }}"
                               class="px-4 py-2 rounded-xl text-[11px] font-bold
                                      bg-primary text-on-primary
                                      hover:bg-primary-container hover:shadow-md active:scale-95
                                      transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)]">
                                <i class="fas fa-bolt text-[9px] mr-0.5"></i>Bayar
                            </a>

                            <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST"
                                  onsubmit="return confirm('Batalkan reservasi ini?');" class="contents">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl border border-error/40 text-error
                                               hover:bg-error-container active:scale-95
                                               transition-all duration-200 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                                        title="Batalkan">
                                    <i class="fas fa-xmark text-[11px]"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

            </div>{{-- end card body --}}
        </div>{{-- end card --}}

    @endforeach
</div>

{{-- Pagination --}}
@if ($paginated ?? false)
    <div class="mt-8 flex justify-center">
        {{ $bookings->links() }}
    </div>
@endif

@endif
