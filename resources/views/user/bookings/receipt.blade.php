@extends('layouts.app')

@section('title', 'Detail Reservasi #' . $booking->booking_code . ' — Yalia Beauty')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
    :root {
        --receipt-bg:     #fdf7f8;
        --receipt-card:   #ffffff;
        --receipt-border: rgba(209,137,152,0.22);
        --receipt-line:   rgba(209,137,152,0.35);
        --clr-brand:      #b01f44;
        --clr-brand-lt:   #fde8ed;
        --clr-label:      #7c5a65;
        --clr-value:      #1a0f14;
        --clr-muted:      #a07080;
    }
    .receipt-font      { font-family: 'Work Sans', sans-serif; }
    .receipt-serif     { font-family: 'Playfair Display', serif; }
    .receipt-mono      { font-family: 'Space Mono', monospace; }

    /* Fade + slide up */
    @keyframes r-enter {
        from { opacity:0; transform:translateY(28px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .r-enter { animation: r-enter .55s cubic-bezier(0.22,0.61,0.36,1) both; }

    /* Shimmer on photo drop zone hover */
    @keyframes shimmer {
        0%   { background-position: -200% center; }
        100% { background-position:  200% center; }
    }
    .drop-shimmer {
        background: linear-gradient(105deg, var(--receipt-card) 40%, #fde8ed 50%, var(--receipt-card) 60%);
        background-size: 200% auto;
    }
    .drop-shimmer:hover {
        animation: shimmer 1.6s cubic-bezier(0.7,0,0.25,1) infinite;
    }

    /* Dashed receipt divider */
    .r-dash { border-top: 1.5px dashed var(--receipt-line); }

    /* Stagger cards */
    .r-enter-1 { animation-delay: .06s; }
    .r-enter-2 { animation-delay: .14s; }
    .r-enter-3 { animation-delay: .22s; }
    .r-enter-4 { animation-delay: .30s; }
    .r-enter-5 { animation-delay: .38s; }

    @media print {
        @page {
            size: portrait;
            margin: 10mm;
        }
        body, html {
            background: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        /* Hide navbar, layout elements, photo upload card, forms, flash messages, action buttons */
        nav, header, footer, .no-print, form, [role="navigation"] {
            display: none !important;
        }
        .receipt-font {
            padding: 0 !important;
            background: #ffffff !important;
            min-height: auto !important;
        }
        .max-w-lg {
            max-width: 480px !important;
            margin: 0 auto !important;
        }
        .r-enter {
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $statusObj  = $booking->status;
    $statusVal  = is_object($statusObj) && isset($statusObj->value) ? $statusObj->value : (string)$statusObj;

    if ($booking->payment_status === 'paid' && $statusVal === 'pending') {
        $statusVal = 'confirmed';
        $statusObj = \App\Enums\BookingStatus::CONFIRMED;
    }

    $badgeLabel = is_object($statusObj) && method_exists($statusObj,'badgeLabel') ? $statusObj->badgeLabel() : ucfirst($statusVal);
    $badgeClass = is_object($statusObj) && method_exists($statusObj,'badgeClasses') ? $statusObj->badgeClasses() : 'bg-gray-100 text-gray-700';

    $tStart = $booking->time_start ? \Carbon\Carbon::parse($booking->time_start)->format('H:i') : '-';
    $tEnd   = $booking->time_end   ? \Carbon\Carbon::parse($booking->time_end)->format('H:i')   : '-';

    $totalDuration = $booking->treatments->sum('duration_minutes');

    $isCompleted = in_array($statusVal, ['completed']);
@endphp

<div class="receipt-font min-h-screen py-28 px-4 relative overflow-hidden"
     style="background: var(--receipt-bg);">

    {{-- Ambient --}}
    <div class="pointer-events-none absolute -top-40 -left-40 w-[480px] h-[480px] rounded-full blur-[120px]"
         style="background:rgba(244,84,114,.08)"></div>
    <div class="pointer-events-none absolute -bottom-40 -right-40 w-[360px] h-[360px] rounded-full blur-[100px]"
         style="background:rgba(176,31,68,.06)"></div>

    <div class="relative z-10 max-w-lg mx-auto space-y-4">

        {{-- ── Flash Messages ── --}}
        @if(session('success'))
        <div class="r-enter flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-semibold shadow-sm">
            <i class="fas fa-circle-check text-emerald-500"></i>
            {{ session('success') }}
        </div>
        @endif
        @error('photo_assign')
        <div class="r-enter flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold shadow-sm">
            <i class="fas fa-circle-exclamation text-red-400"></i>
            {{ $message }}
        </div>
        @enderror

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- CARD A: Brand Header                               --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="r-enter r-enter-1 rounded-3xl shadow-sm overflow-hidden border"
             style="background:var(--receipt-card);border-color:var(--receipt-border)">

            {{-- Top status bar --}}
            <div class="px-6 py-3 flex items-center justify-between gap-3
                @if($statusVal === 'completed') bg-emerald-600 text-white
                @elseif($statusVal === 'canceled') bg-red-500 text-white
                @elseif($statusVal === 'in_progress') bg-blue-600 text-white
                @elseif($statusVal === 'confirmed') bg-primary text-on-primary
                @else bg-amber-500 text-white
                @endif">
                <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest">
                    <i class="fas
                       @if($statusVal==='completed')   fa-circle-check
                       @elseif($statusVal==='canceled') fa-circle-xmark
                       @elseif($statusVal==='in_progress') fa-rotate fa-spin-pulse
                       @else fa-clock @endif text-xs"></i>
                    <span>{{ $badgeLabel }}</span>
                </div>
                <span class="receipt-mono text-[11px] font-bold opacity-80">#{{ $booking->booking_code }}</span>
            </div>

            {{-- Brand identity --}}
            <div class="px-6 py-6 text-center">
                <div class="w-14 h-14 mx-auto mb-3 rounded-2xl flex items-center justify-center shadow-sm"
                     style="background:var(--clr-brand-lt)">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty"
                         width="36" height="36" decoding="async"
                         class="w-9 h-9 object-contain">
                </div>
                <h1 class="receipt-serif text-xl font-black tracking-tight" style="color:var(--clr-value)">Yalia Beauty Salon</h1>
                <p class="text-[11px] mt-0.5" style="color:var(--clr-muted)">Luxury Treatment · Kecantikan Profesional</p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- CARD B: Info Pelanggan & Jadwal (receipt rows)     --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="r-enter r-enter-2 rounded-3xl shadow-sm border px-6 py-5 space-y-0"
             style="background:var(--receipt-card);border-color:var(--receipt-border)">

            <p class="text-[10px] font-bold uppercase tracking-widest mb-4" style="color:var(--clr-muted)">Informasi Reservasi</p>

            @php
                $rows = [
                    ['icon'=>'fa-user',           'label'=>'Pelanggan',          'value'=> $booking->user?->name ?? '-'],
                    ['icon'=>'fa-hashtag',         'label'=>'Kode Booking',       'value'=> '#' . $booking->booking_code, 'mono'=>true],
                    ['icon'=>'fa-calendar-day',    'label'=>'Tanggal',            'value'=> $booking->booking_date?->translatedFormat('l, d F Y') ?? '-'],
                    ['icon'=>'fa-clock',           'label'=>'Waktu',              'value'=> $tStart . ' – ' . $tEnd . ' WIB (' . $totalDuration . ' mnt)'],
                    ['icon'=>'fa-user-sparkles',   'label'=>'Beautician',         'value'=> $booking->beautician?->name ?? 'Auto Assign'],
                    ['icon'=>'fa-location-dot',    'label'=>'Tipe Kunjungan',     'value'=> $booking->booking_type === 'home' ? '🏠 Home Service' : '🏪 Ke Salon'],
                ];
            @endphp

            @foreach ($rows as $i => $row)
                <div class="flex items-start justify-between gap-4 py-3 {{ !$loop->last ? 'r-dash' : '' }}">
                    <div class="flex items-center gap-2.5 shrink-0">
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center text-[11px]"
                             style="background:var(--clr-brand-lt);color:var(--clr-brand)">
                            <i class="fas {{ $row['icon'] }}"></i>
                        </div>
                        <span class="text-[11px] font-semibold" style="color:var(--clr-label)">{{ $row['label'] }}</span>
                    </div>
                    <span class="text-[12px] font-bold text-right {{ ($row['mono'] ?? false) ? 'receipt-mono' : '' }}"
                          style="color:var(--clr-value)">{{ $row['value'] }}</span>
                </div>
            @endforeach

            @if($booking->booking_type === 'home' && $booking->home_address)
            <div class="mt-3 px-4 py-3 rounded-2xl text-xs" style="background:var(--clr-brand-lt);color:var(--clr-label)">
                <i class="fas fa-map-pin mr-1.5" style="color:var(--clr-brand)"></i>
                {{ $booking->home_address }}
            </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- CARD C: Treatment Items                            --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="r-enter r-enter-3 rounded-3xl shadow-sm border px-6 py-5"
             style="background:var(--receipt-card);border-color:var(--receipt-border)">

            <div class="flex items-center justify-between mb-4">
                <p class="text-[10px] font-bold uppercase tracking-widest" style="color:var(--clr-muted)">Detail Layanan</p>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                      style="background:var(--clr-brand-lt);color:var(--clr-brand)">
                    {{ $booking->treatments->count() }} treatment
                </span>
            </div>

            <div class="space-y-3">
                @forelse($booking->bookingTreatments as $item)
                    @php
                        $trObj = $item->Treatments ?? $item->treatment;
                        $trImg = $trObj?->image_url ?? ($trObj?->images ? asset('storage/'.$trObj->images) : asset('logo/yalia-logos-trnsprnt.svg'));
                    @endphp
                    <div class="flex items-center gap-3">
                        {{-- mini thumb --}}
                        <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border bg-rose-50/50"
                             style="border-color:var(--receipt-border)">
                            <img src="{{ $trImg }}"
                                 alt="{{ $trObj?->name }}"
                                 class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12px] font-bold truncate" style="color:var(--clr-value)">{{ $trObj?->name }}</p>
                            <p class="text-[10px]" style="color:var(--clr-muted)">× {{ $item->quantity }} &middot; {{ $trObj?->duration_minutes ?? 0 }} mnt</p>
                        </div>
                        <span class="receipt-mono text-[12px] font-bold shrink-0" style="color:var(--clr-brand)">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                @empty
                    @foreach($booking->treatments as $tr)
                    @php
                        $trImg = $tr->image_url ?? ($tr->images ? asset('storage/'.$tr->images) : asset('logo/yalia-logos-trnsprnt.svg'));
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border bg-rose-50/50" style="border-color:var(--receipt-border)">
                            <img src="{{ $trImg }}" alt="{{ $tr->name }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[12px] font-bold truncate" style="color:var(--clr-value)">{{ $tr->name }}</p>
                            <p class="text-[10px]" style="color:var(--clr-muted)">× 1 &middot; {{ $tr->duration_minutes ?? 0 }} mnt</p>
                        </div>
                        <span class="receipt-mono text-[12px] font-bold shrink-0" style="color:var(--clr-brand)">
                            Rp {{ number_format($tr->price, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                @endforelse
            </div>

            {{-- Total rows --}}
            <div class="mt-4 pt-4 r-dash space-y-2">
                <div class="flex justify-between text-[11px]" style="color:var(--clr-label)">
                    <span>Subtotal Layanan</span>
                    <span class="receipt-mono font-semibold" style="color:var(--clr-value)">
                        Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}
                    </span>
                </div>
                @if(($booking->discount_amount ?? 0) > 0)
                <div class="flex justify-between text-[11px] text-emerald-600">
                    <span>Diskon Voucher</span>
                    <span class="receipt-mono font-semibold">– Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if(($booking->transport_fee ?? 0) > 0)
                <div class="flex justify-between text-[11px]" style="color:var(--clr-label)">
                    <span>Ongkir Home Service</span>
                    <span class="receipt-mono font-semibold" style="color:var(--clr-value)">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="flex justify-between items-center pt-3 r-dash">
                    <span class="text-sm font-extrabold" style="color:var(--clr-value)">Total Lunas</span>
                    <span class="receipt-serif text-xl font-black" style="color:var(--clr-brand)">
                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Payment verified badge --}}
            <div class="mt-4 flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl"
                 style="background:var(--clr-brand-lt)">
                <i class="fas fa-shield-check text-sm" style="color:var(--clr-brand)"></i>
                <span class="text-[11px] font-bold" style="color:var(--clr-brand)">
                    Pembayaran Diterima via {{ strtoupper($booking->payment_method ?? 'QRIS') }}
                </span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- CARD D: Foto Hasil Treatment (photo_assign)        --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="no-print r-enter r-enter-4 rounded-3xl shadow-sm border px-6 py-5"
             style="background:var(--receipt-card);border-color:var(--receipt-border)">

            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest" style="color:var(--clr-muted)">Foto Hasil Treatment</p>
                    <p class="text-[11px] mt-0.5" style="color:var(--clr-label)">Dokumentasi after-service dari kunjunganmu</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                      style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0">Opsional</span>
            </div>

            @if($booking->photo_assign)
                {{-- Photo preview --}}
                <div class="relative rounded-2xl overflow-hidden border shadow-sm group" style="border-color:var(--receipt-border)">
                    <img src="{{ asset('storage/' . $booking->photo_assign) }}"
                         alt="Foto Hasil Treatment"
                         class="w-full max-h-72 object-cover transition-transform duration-500 ease-[cubic-bezier(0.22,0.61,0.36,1)] group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                    <div class="absolute bottom-3 left-3">
                        <span class="receipt-mono text-[10px] font-bold text-white/90 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-lg">
                            .webp · after-service
                        </span>
                    </div>
                </div>

                {{-- Replace photo --}}
                <form action="{{ route('user.bookings.photo-assign', $booking) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    <label class="flex items-center gap-2 cursor-pointer text-[11px] font-semibold" style="color:var(--clr-label)">
                        <input type="file" name="photo_assign" accept="image/*" class="hidden" id="photoInputReplace"
                               onchange="this.form.submit()">
                        <span class="px-4 py-2 rounded-xl border text-[11px] font-bold transition-all hover:shadow-sm active:scale-95"
                              style="border-color:var(--receipt-line);color:var(--clr-brand)"
                              onclick="document.getElementById('photoInputReplace').click()">
                            <i class="fas fa-rotate mr-1"></i>Ganti Foto
                        </span>
                        <span style="color:var(--clr-muted)">Maks 5 MB · JPEG / PNG / WebP</span>
                    </label>
                </form>

            @else
                {{-- Upload drop zone --}}
                <form action="{{ route('user.bookings.photo-assign', $booking) }}" method="POST" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    <input type="file" name="photo_assign" accept="image/*" class="hidden" id="photoInput"
                           onchange="document.getElementById('photoForm').submit()">
                    <button type="button"
                            onclick="document.getElementById('photoInput').click()"
                            class="drop-shimmer w-full py-10 rounded-2xl border-2 border-dashed text-center transition-all duration-300 hover:shadow-sm active:scale-[0.99] focus:outline-none"
                            style="border-color:var(--receipt-line)">
                        <div class="flex flex-col items-center gap-2">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                                 style="background:var(--clr-brand-lt);color:var(--clr-brand)">
                                <i class="fas fa-camera text-xl"></i>
                            </div>
                            <p class="text-sm font-bold" style="color:var(--clr-value)">Upload Foto Hasil Treatment</p>
                            <p class="text-[11px]" style="color:var(--clr-muted)">Klik untuk pilih foto · Otomatis dikonversi ke WebP</p>
                            <p class="text-[10px] receipt-mono px-3 py-1 rounded-lg" style="background:var(--clr-brand-lt);color:var(--clr-brand)">
                                JPEG · PNG · WebP · Max 5 MB
                            </p>
                        </div>
                    </button>
                </form>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- CARD E: Notes & Catatan Pembatalan                 --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @if($booking->notes || $booking->cancel_reason)
        <div class="r-enter r-enter-5 rounded-3xl shadow-sm border px-6 py-5 space-y-3"
             style="background:var(--receipt-card);border-color:var(--receipt-border)">
            @if($booking->notes)
            <div>
                <p class="text-[10px] font-bold uppercase tracking-widest mb-1.5" style="color:var(--clr-muted)">Catatan dari Pelanggan</p>
                <p class="text-sm" style="color:var(--clr-label)">{{ $booking->notes }}</p>
            </div>
            @endif
            @if($booking->cancel_reason)
            <div class="pt-3 r-dash">
                <p class="text-[10px] font-bold uppercase tracking-widest mb-1.5 text-red-400">Alasan Pembatalan</p>
                <p class="text-sm text-red-600">{{ $booking->cancel_reason }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- Action Buttons (no-print)                          --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="no-print r-enter r-enter-5 space-y-2.5 pb-4">
            <button onclick="window.print()"
                    class="w-full py-3.5 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm
                           transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:shadow-md active:scale-95"
                    style="background:var(--clr-brand);color:#fff">
                <i class="fas fa-print text-sm"></i>
                Cetak / Simpan Struk PDF
            </button>
            <a href="{{ route('user.bookings.index') }}"
               class="block text-center w-full py-3 rounded-2xl border font-bold text-sm
                      transition-all duration-200 hover:bg-rose-50 active:scale-95"
               style="border-color:var(--receipt-line);color:var(--clr-label)">
                ← Kembali ke Riwayat Reservasi
            </a>
        </div>

        {{-- Footer tagline --}}
        <div class="text-center pb-4">
            <p class="receipt-serif text-sm font-bold" style="color:var(--clr-brand)">Terima Kasih ✨</p>
            <p class="text-[11px] mt-0.5" style="color:var(--clr-muted)">Tunjukkan kode booking saat kedatangan di salon.</p>
        </div>

    </div>
</div>
@endsection
