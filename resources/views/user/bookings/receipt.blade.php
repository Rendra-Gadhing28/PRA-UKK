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
     style="background: transparent;">

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
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
                    <i class="fas
                       @if($statusVal==='completed')   fa-circle-check
                       @elseif($statusVal==='canceled') fa-circle-xmark
                       @elseif($statusVal==='in_progress') fa-rotate fa-spin-pulse
                       @else fa-clock @endif text-xs"></i>
                    <span>{{ $badgeLabel }}</span>
                </div>
                <span class="receipt-mono text-xs font-bold opacity-80">#{{ $booking->booking_code }}</span>
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
                <p class="text-xs mt-0.5" style="color:var(--clr-muted)">Luxury Treatment · Kecantikan Profesional</p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- CARD B: Info Pelanggan & Jadwal (receipt rows)     --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="r-enter r-enter-2 rounded-3xl shadow-sm border px-6 py-5 space-y-0"
             style="background:var(--receipt-card);border-color:var(--receipt-border)">

            <p class="text-xs font-bold uppercase tracking-widest mb-4" style="color:var(--clr-muted)">Informasi Reservasi</p>

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
                        <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs"
                             style="background:var(--clr-brand-lt);color:var(--clr-brand)">
                            <i class="fas {{ $row['icon'] }}"></i>
                        </div>
                        <span class="text-xs font-semibold" style="color:var(--clr-label)">{{ $row['label'] }}</span>
                    </div>
                    <span class="text-xs font-bold text-right {{ ($row['mono'] ?? false) ? 'receipt-mono' : '' }}"
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
                <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--clr-muted)">Detail Layanan</p>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                      style="background:var(--clr-brand-lt);color:var(--clr-brand)">
                    {{ $booking->treatments->count() }} treatment
                </span>
            </div>

            <div class="space-y-3">
                @forelse($booking->bookingTreatments as $item)
                    @php
                        $trObj = $item->Treatments ?? $item->treatment;
                        $trImg = $trObj?->image_url ?? \App\Support\ImageHelper::url($trObj?->images);
                    @endphp
                    <div class="flex flex-col gap-3 pb-3 {{ !$loop->last ? 'border-b border-[var(--receipt-line)] border-dashed' : '' }}">
                        <div class="flex items-center gap-3">
                            {{-- mini thumb --}}
                            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border bg-rose-50/50"
                                 style="border-color:var(--receipt-border)">
                                <img src="{{ $trImg }}"
                                     alt="{{ $trObj?->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold truncate" style="color:var(--clr-value)">{{ $trObj?->name }}</p>
                                <p class="text-xs" style="color:var(--clr-muted)">× {{ $item->quantity }} &middot; {{ $trObj?->duration_minutes ?? 0 }} mnt</p>
                            </div>
                            <span class="receipt-mono text-xs font-bold shrink-0" style="color:var(--clr-brand)">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($isCompleted)
                        <div class="flex justify-end no-print">
                            <a href="{{ route('user.treatments.review', ['booking' => $booking->id, 'treatment' => $trObj?->id]) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all hover:bg-rose-50 active:scale-95 border"
                               style="color:var(--clr-brand);border-color:var(--receipt-line)">
                                <i class="fas fa-star text-yellow-400"></i> Beri Ulasan
                            </a>
                        </div>
                        @endif
                    </div>
                @empty
                    @foreach($booking->treatments as $tr)
                    @php
                        $trImg = $tr->image_url ?? \App\Support\ImageHelper::url($tr->images);
                    @endphp
                    <div class="flex flex-col gap-3 pb-3 {{ !$loop->last ? 'border-b border-[var(--receipt-line)] border-dashed' : '' }}">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 border bg-rose-50/50" style="border-color:var(--receipt-border)">
                                <img src="{{ $trImg }}" alt="{{ $tr->name }}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-bold truncate" style="color:var(--clr-value)">{{ $tr->name }}</p>
                                <p class="text-xs" style="color:var(--clr-muted)">× 1 &middot; {{ $tr->duration_minutes ?? 0 }} mnt</p>
                            </div>
                            <span class="receipt-mono text-xs font-bold shrink-0" style="color:var(--clr-brand)">
                                Rp {{ number_format($tr->price, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($isCompleted)
                        <div class="flex justify-end no-print">
                            <a href="{{ route('user.treatments.review', ['booking' => $booking->id, 'treatment' => $tr->id]) }}" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all hover:bg-rose-50 active:scale-95 border"
                               style="color:var(--clr-brand);border-color:var(--receipt-line)">
                                <i class="fas fa-star text-yellow-400"></i> Beri Ulasan
                            </a>
                        </div>
                        @endif
                    </div>
                    @endforeach
                @endforelse
            </div>

            {{-- Total rows --}}
            <div class="mt-4 pt-4 r-dash space-y-2">
                <div class="flex justify-between text-xs" style="color:var(--clr-label)">
                    <span>Subtotal Layanan</span>
                    <span class="receipt-mono font-semibold" style="color:var(--clr-value)">
                        Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}
                    </span>
                </div>
                @if(($booking->discount_amount ?? 0) > 0)
                <div class="flex justify-between text-xs text-emerald-600">
                    <span>Potongan Diskon</span>
                    <span class="receipt-mono font-semibold">– Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if(($booking->transport_fee ?? 0) > 0)
                <div class="flex justify-between text-xs" style="color:var(--clr-label)">
                    <span>Ongkir Home Service</span>
                    <span class="receipt-mono font-semibold" style="color:var(--clr-value)">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($booking->payment_type === 'cash')
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3 my-2 space-y-1 text-xs">
                    <div class="flex justify-between text-amber-900 font-bold">
                        <span>DP 35% (Terbayar via QRIS):</span>
                        <span class="receipt-mono">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-amber-800 font-semibold">
                        <span>Sisa Pelunasan Tunai (65% di Salon):</span>
                        <span class="receipt-mono">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                <div class="flex justify-between items-center pt-3 r-dash">
                    <span class="text-sm font-extrabold" style="color:var(--clr-value)">
                        {{ $booking->payment_status === 'dp_paid' ? 'Total Treatment' : 'Total Lunas' }}
                    </span>
                    <span class="receipt-serif text-xl font-black" style="color:var(--clr-brand)">
                        Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Payment verified badge --}}
            <div class="mt-4 flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl"
                 style="background:var(--clr-brand-lt)">
                <i class="fas fa-shield-check text-sm" style="color:var(--clr-brand)"></i>
                <span class="text-xs font-bold" style="color:var(--clr-brand)">
                    @if($booking->payment_status === 'dp_paid')
                        DP 35% (Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}) Terbayar via {{ strtoupper($booking->payment_method ?? 'QRIS') }}
                    @else
                        Pembayaran Diterima via {{ strtoupper($booking->payment_method ?? 'QRIS') }}
                    @endif
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
                    <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--clr-muted)">Foto Hasil Treatment</p>
                    <p class="text-xs mt-0.5" style="color:var(--clr-label)">Dokumentasi after-service dari kunjunganmu</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Opsional</span>
            </div>

            @if($booking->photo_assign)
                {{-- Photo preview --}}
                <div class="relative rounded-2xl overflow-hidden border shadow-sm group" style="border-color:var(--receipt-border)">
                    <img src="{{ \App\Support\ImageHelper::url($booking->photo_assign) }}"
                         alt="Foto Hasil Treatment"
                         class="w-full max-h-72 object-cover transition-transform duration-500 ease-[cubic-bezier(0.22,0.61,0.36,1)] group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
                    <div class="absolute bottom-3 left-3">
                        <span class="receipt-mono text-xs font-bold text-white/90 bg-black/40 backdrop-blur-sm px-2.5 py-1 rounded-lg">
                            .webp · after-service
                        </span>
                    </div>
                </div>

                {{-- Replace photo --}}
                <form action="{{ route('user.bookings.photo-assign', $booking) }}" method="POST" enctype="multipart/form-data" class="mt-3">
                    @csrf
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold" style="color:var(--clr-label)">
                        <input type="file" name="photo_assign" accept="image/*" class="hidden" id="photoInputReplace"
                               onchange="this.form.submit()">
                        <span class="px-4 py-2 rounded-xl border text-xs font-bold transition-all hover:shadow-sm active:scale-95"
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
                            <p class="text-xs" style="color:var(--clr-muted)">Klik untuk pilih foto · Otomatis dikonversi ke WebP</p>
                            <p class="text-xs receipt-mono px-3 py-1 rounded-lg" style="background:var(--clr-brand-lt);color:var(--clr-brand)">
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
                <p class="text-xs font-bold uppercase tracking-widest mb-1.5" style="color:var(--clr-muted)">Catatan dari Pelanggan</p>
                <p class="text-sm" style="color:var(--clr-label)">{{ $booking->notes }}</p>
            </div>
            @endif
            @if($booking->cancel_reason)
            <div class="pt-3 r-dash">
                <p class="text-xs font-bold uppercase tracking-widest mb-1.5 text-red-400">Alasan Pembatalan</p>
                <p class="text-sm text-red-600">{{ $booking->cancel_reason }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- Action Buttons (no-print)                          --}}
        {{-- ══════════════════════════════════════════════════ --}}
         <div class="no-print r-enter r-enter-5 space-y-2.5 pb-4"
              x-data="{
                  openReschedule: {{ request()->boolean('reschedule') ? 'true' : 'false' }},
                  openCancel: false,
                  selectedDate: '{{ $booking->booking_date ? $booking->booking_date->format('Y-m-d') : date('Y-m-d') }}',
                  selectedTime: '{{ $booking->time_start ?? '09:00' }}',
                  duration: {{ $totalDuration ?? 60 }},
                  slots: [],
                  loadingSlots: false,
                  init() {
                      if (this.openReschedule) {
                          this.fetchSlots();
                      }
                  },
                  fetchSlots() {
                      this.loadingSlots = true;
                      fetch('{{ route('user.bookings.daily-slots') }}?booking_date=' + this.selectedDate + '&duration_minutes=' + this.duration)
                          .then(res => res.json())
                          .then(data => {
                              this.slots = data.slots || [];
                              this.loadingSlots = false;
                          })
                          .catch(() => { this.loadingSlots = false; });
                  }
              }">

            @if(in_array($statusVal, ['pending', 'confirmed']))
            <button type="button" @click="openReschedule = true; fetchSlots();"
                    class="w-full py-3.5 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm
                           transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] hover:shadow-md active:scale-95 border"
                    style="background:#fff;border-color:var(--clr-brand);color:var(--clr-brand)">
                <i class="fas fa-calendar-days text-sm"></i>
                Ganti Jadwal Reservasi
            </button>

            <button type="button" @click="openCancel = true"
                    class="w-full py-3.5 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm
                           transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] hover:shadow-md active:scale-95 border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100">
                <i class="fas fa-times-circle text-sm"></i>
                Batalkan Booking
            </button>
            @endif

            <button onclick="window.print()"
                    class="w-full py-3.5 rounded-2xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm
                           transition-all duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] hover:shadow-md active:scale-95"
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

            {{-- ── Modal Batalkan Booking ── --}}
            <div x-show="openCancel" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-rose-100 space-y-4 text-left relative"
                     @click.away="openCancel = false">
                    <div class="flex items-center justify-between border-b border-rose-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-rose-50 text-rose-600 font-bold">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">Batalkan Booking</h3>
                                <p class="text-xs text-gray-500">Mohon beritahu kami alasannya</p>
                            </div>
                        </div>
                        <button type="button" @click="openCancel = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        @if($booking->payment_status === 'dp_paid')
                            <div class="bg-rose-50 text-rose-700 text-xs p-3 rounded-xl border border-rose-100 font-medium">
                                <i class="fas fa-info-circle mr-1"></i> Pembayaran DP Anda akan hangus dan tidak dapat dikembalikan sesuai kebijakan salon.
                            </div>
                        @elseif($booking->payment_status === 'paid' || $booking->payment_status === 'fullpayment')
                            <div class="bg-amber-50 text-amber-700 text-xs p-3 rounded-xl border border-amber-100 font-medium">
                                <i class="fas fa-info-circle mr-1"></i> Dana pembayaran penuh Anda akan dikembalikan 100%. Tim kami akan menghubungi Anda untuk proses refund.
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Pembatalan</label>
                            <textarea name="reason" rows="3" required
                                      class="w-full text-sm rounded-2xl border-gray-200 focus:border-rose-400 focus:ring focus:ring-rose-200 focus:ring-opacity-50 transition-shadow bg-gray-50"
                                      placeholder="Contoh: Ada urusan mendadak, tidak jadi ke salon, dsb..."></textarea>
                        </div>
                        
                        <div class="pt-2 flex gap-3">
                            <button type="button" @click="openCancel = false"
                                    class="flex-1 py-3 rounded-2xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 py-3 rounded-2xl font-bold text-white transition-all hover:shadow-lg hover:shadow-rose-500/30 active:scale-95 text-sm"
                                    style="background:var(--clr-brand)">
                                Ya, Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ── Modal Reschedule ── --}}
            <div x-show="openReschedule" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-rose-100 space-y-4 text-left relative"
                     @click.away="openReschedule = false">
                    <div class="flex items-center justify-between border-b border-rose-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-rose-50 text-rose-600 font-bold">
                                <i class="fas fa-calendar-pen"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">Ganti Jadwal Reservasi</h3>
                                <p class="text-xs text-gray-500">Pilih tanggal dan jam baru untuk kunjunganmu</p>
                            </div>
                        </div>
                        <button type="button" @click="openReschedule = false" class="text-gray-400 hover:text-gray-600 p-1">
                            <i class="fas fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <form action="{{ route('user.bookings.reschedule', $booking) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="time_start" x-model="selectedTime">

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Tanggal Baru</label>
                            <input type="date" name="booking_date" x-model="selectedDate" @change="fetchSlots()"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full rounded-xl border-gray-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-gray-700">Pilih Jam Kedatangan Baru</label>
                                <span class="text-xs text-gray-400 font-semibold">Operasional 08:00 - 20:00</span>
                            </div>

                            <template x-if="loadingSlots">
                                <div class="py-6 text-center text-xs text-gray-400">
                                    <i class="fas fa-circle-notch fa-spin mr-1"></i> Memeriksa ketersediaan slot jam...
                                </div>
                            </template>

                            <template x-if="!loadingSlots">
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-1 scrollbar-thin">
                                    <template x-for="slot in slots" :key="slot.time">
                                        <button type="button"
                                                @click="if (slot.available) selectedTime = slot.time"
                                                :disabled="!slot.available"
                                                class="relative rounded-xl p-2 min-h-[58px] border-2 transition-all flex flex-col items-center justify-center gap-0.5 group text-center overflow-hidden w-full"
                                                :class="{
                                                    'border-[#f45472] bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-extrabold shadow-md scale-[1.02]': selectedTime === slot.time && slot.available,
                                                    'border-rose-200 bg-white text-[#5b3a29] hover:border-[#f45472] hover:bg-rose-50 shadow-xs': selectedTime !== slot.time && slot.available,
                                                    'border-rose-200/80 bg-rose-50/80 text-rose-800 opacity-60 cursor-not-allowed': !slot.available
                                                }">
                                            <div class="flex items-center justify-center gap-0.5 w-full px-1">
                                                <span class="text-xs font-extrabold truncate" x-text="slot.formatted_time"></span>
                                                <i x-show="selectedTime === slot.time && slot.available" class="fa-solid fa-check text-xs shrink-0"></i>
                                            </div>
                                            <span x-show="slot.available" class="text-xs uppercase font-bold tracking-wider truncate w-full" 
                                                  :class="selectedTime === slot.time ? 'text-white/90' : 'text-emerald-600'">Tersedia</span>
                                            <span x-show="!slot.available" class="w-full text-xs uppercase font-bold tracking-wider text-rose-800 bg-rose-200/70 px-1 py-0.5 rounded flex items-center justify-center gap-1 truncate">
                                                <i class="fa-solid fa-lock text-xs shrink-0"></i>
                                                <span class="truncate">TUTUP</span>
                                            </span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Perubahan (Opsional)</label>
                            <input type="text" name="reason" placeholder="Misal: Ada keperluan mendadak"
                                   class="w-full rounded-xl border-gray-200 text-sm focus:border-rose-500 focus:ring-rose-500">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-rose-100">
                            <button type="button" @click="openReschedule = false"
                                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:bg-gray-100">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="!selectedTime"
                                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-white shadow-md hover:shadow-lg active:scale-95 transition-all disabled:opacity-50"
                                    style="background:var(--clr-brand)">
                                Simpan Jadwal Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- Footer tagline --}}
        <div class="text-center pb-4">
            <p class="receipt-serif text-sm font-bold flex items-center justify-center gap-1.5" style="color:var(--clr-brand)">
                <span>Terima Kasih</span>
                <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
            </p>
            <p class="text-xs mt-0.5" style="color:var(--clr-muted)">Tunjukkan kode booking saat kedatangan di salon.</p>
        </div>

    </div>
</div>
@endsection
