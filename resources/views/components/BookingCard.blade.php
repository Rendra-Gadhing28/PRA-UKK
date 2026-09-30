{{-- Satu kartu booking reusable component (Compact & Simple) --}}
@php
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

    $badgeLabel = is_object($statusObj) && method_exists($statusObj, 'badgeLabel')
        ? $statusObj->badgeLabel()
        : ucfirst($statusVal);

    $statusColor = match($statusVal) {
        'completed'             => ['dot'=>'bg-emerald-500', 'text'=>'text-emerald-700', 'bg'=>'bg-emerald-50', 'border'=>'border-emerald-200'],
        'canceled', 'cancelled' => ['dot'=>'bg-rose-500',    'text'=>'text-rose-700',    'bg'=>'bg-rose-50',    'border'=>'border-rose-200'],
        'in_progress'           => ['dot'=>'bg-blue-500',    'text'=>'text-blue-700',    'bg'=>'bg-blue-50',    'border'=>'border-blue-200'],
        'confirmed'             => ['dot'=>'bg-primary',     'text'=>'text-primary',     'bg'=>'bg-[#FFF0F2]',  'border'=>'border-[#F4DDE1]'],
        default                 => ['dot'=>'bg-amber-500',   'text'=>'text-amber-800',   'bg'=>'bg-amber-50',   'border'=>'border-amber-200'],
    };

    $firstTr = $booking->treatments->first();
    $treatmentNames = $booking->treatments->count() > 0
        ? $booking->treatments->pluck('name')->join(' · ')
        : ($booking->treatment?->name ?? 'Perawatan Yalia');
    $heroPhoto = \App\Support\ImageHelper::url($booking->photo_assign ?? $firstTr?->images, $firstTr?->image_url);

    $tStart = $booking->time_start ? \Carbon\Carbon::parse($booking->time_start)->format('H:i') : '-';
    $tEnd   = $booking->time_end   ? \Carbon\Carbon::parse($booking->time_end)->format('H:i')   : '-';
    $dateStr = $booking->booking_date ? $booking->booking_date->translatedFormat('d M Y') : '-';
    $dayStr  = $booking->booking_date ? $booking->booking_date->translatedFormat('l') : '';
@endphp

<div class="group relative bg-white rounded-2xl p-3 sm:p-4 border border-[#F4DDE1] shadow-sm hover:shadow-md hover:border-primary/40 transition-all duration-300 ease-out flex flex-col sm:flex-row sm:items-center justify-between gap-3 sm:gap-4 {{ $statusVal === 'canceled' || $statusVal === 'cancelled' ? 'opacity-75' : '' }}" style="font-family:'Work Sans',sans-serif">
    {{-- Left Side: Thumbnail + Info Details --}}
    <div class="flex items-center gap-3 sm:gap-3.5 min-w-0 flex-1">
        {{-- Compact Square Thumbnail --}}
        <div class="relative shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-[#FFF0F2] border border-[#F4DDE1] overflow-hidden">
            @if($heroPhoto)
                <img src="{{ $heroPhoto }}" alt="{{ $treatmentNames }}" width="96" height="96" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" loading="lazy">
            @else
                <div class="w-full h-full flex items-center justify-center p-2 bg-white">
                    <img src="{{ asset('logo/yalia-logos.svg') }}" alt="Yalia Beauty" width="48" height="48" class="w-12 h-12 object-contain">
                </div>
            @endif
        </div>

        {{-- Center Details --}}
        <div class="min-w-0 flex-1 space-y-1">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="font-mono text-xs font-bold text-primary bg-[#FFF0F2] px-2 py-0.5 rounded-md border border-[#F4DDE1]">
                    #{{ $booking->booking_code }}
                </span>

                @if($booking->booking_type === 'home_service' || $booking->booking_type === 'home')
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        <i class="fa-solid fa-house-chimney text-xs"></i>
                        <span>Home</span>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                        <i class="fa-solid fa-store text-xs"></i>
                        <span>Salon</span>
                    </span>
                @endif

                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-bold border {{ $statusColor['bg'] }} {{ $statusColor['text'] }} {{ $statusColor['border'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusColor['dot'] }} shrink-0"></span>
                    <span>{{ $badgeLabel }}</span>
                </span>
            </div>

            <h3 class="font-bold text-[#2B0F23] text-sm sm:text-base leading-snug truncate [text-wrap:balance] group-hover:text-primary transition-colors" style="font-family:'Playfair Display',serif">
                {{ $treatmentNames }}
            </h3>

            <div class="flex items-center gap-2 text-xs text-[#5C1439]/70 flex-wrap">
                <span class="inline-flex items-center gap-1 font-medium">
                    <i class="fa-regular fa-calendar text-primary text-xs shrink-0"></i>
                    <span class="tabular-nums">{{ $dayStr }}, {{ $dateStr }}</span>
                </span>
                <span class="opacity-40">·</span>
                <span class="inline-flex items-center gap-1 font-medium">
                    <i class="fa-regular fa-clock text-primary text-xs shrink-0"></i>
                    <span class="tabular-nums">{{ $tStart }} – {{ $tEnd }}</span>
                </span>
                <span class="opacity-40">·</span>
                <span class="inline-flex items-center gap-1 font-medium truncate max-w-[140px]">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary text-xs shrink-0"></i>
                    <span>{{ $booking->beautician?->name ?? 'Auto' }}</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Right Side: Price + Actions --}}
    <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#F4DDE1]/60">
        <div class="text-left sm:text-right">
            <span class="text-xs font-extrabold text-[#5C1439]/60 uppercase tracking-wider block sm:leading-none">Total</span>
            <span class="text-sm sm:text-base font-black text-primary font-mono leading-tight tabular-nums">
                {{ $booking->formatted_total }}
            </span>
        </div>

        <div class="flex items-center gap-1.5 flex-wrap">
            <a href="{{ route('user.bookings.show', $booking) }}" class="px-3 py-1 rounded-full text-xs font-bold bg-[#FFF6FA] border border-[#F4DDE1] text-[#2B0F23] hover:bg-[#FFF0F2] hover:text-primary active:scale-95 transition-all flex items-center gap-1">
                <i class="fa-regular fa-eye text-xs"></i>
                <span>Detail</span>
            </a>

            @if(in_array($statusVal, ['pending', 'confirmed']))
                <form method="POST" action="{{ route('user.bookings.cancel', $booking) }}" onsubmit="return confirm('Batalkan reservasi ini?');" class="inline-flex m-0 p-0">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-2.5 py-1 rounded-full border border-rose-200 text-rose-600 hover:bg-rose-50 active:scale-95 transition-all text-xs font-bold flex items-center gap-0.5 cursor-pointer" title="Batalkan">
                        <i class="fa-solid fa-xmark text-xs"></i>
                        <span>Batal</span>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
