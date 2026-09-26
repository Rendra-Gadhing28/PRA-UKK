@extends('layouts.app')

@section('title', 'Detail Reservasi #' . $booking->booking_code . ' — Yalia Beauty')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
    .pass-font  { font-family: 'Work Sans', sans-serif; }
    .pass-serif { font-family: 'Playfair Display', serif; }
    .pass-mono  { font-family: 'Space Mono', monospace; }

    @keyframes pass-enter {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .pass-enter {
        animation: pass-enter .4s cubic-bezier(0.22, 0.61, 0.36, 1) both;
    }

    @media print {
        @page {
            size: portrait;
            margin: 8mm;
        }
        body, html {
            background: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        nav, header, footer, .no-print, form, [role="navigation"], .ambient-bg {
            display: none !important;
        }
        .pass-font {
            padding: 0 !important;
            background: #ffffff !important;
            min-height: auto !important;
        }
        .pass-container {
            max-width: 100% !important;
            margin: 0 auto !important;
            box-shadow: none !important;
            border: 1px solid #e0bec1 !important;
        }
        .pass-enter {
            animation: none !important;
            transform: none !important;
            opacity: 1 !important;
        }
    }
</style>
@endpush

@section('content')
@php
    $statusObj = $booking->status;
    $statusVal = is_object($statusObj) && isset($statusObj->value) ? $statusObj->value : (string)$statusObj;

    if ($booking->payment_status === 'paid' && $statusVal === 'pending') {
        $statusVal = 'confirmed';
        $statusObj = \App\Enums\BookingStatus::CONFIRMED;
    }

    $badgeLabel = is_object($statusObj) && method_exists($statusObj, 'badgeLabel') ? $statusObj->badgeLabel() : ucfirst($statusVal);

    $isPending   = $statusVal === 'pending' && $booking->payment_status !== 'paid';
    $isConfirmed = in_array($statusVal, ['confirmed']);
    $isInProgress= in_array($statusVal, ['in_progress']);
    $isCompleted = in_array($statusVal, ['completed']);
    $isCancelled = in_array($statusVal, ['canceled', 'cancelled']);

    $statusMeta = match($statusVal) {
        'completed'             => ['dot' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'fa-circle-check', 'label' => 'Selesai Dilayani'],
        'canceled', 'cancelled' => ['dot' => 'bg-rose-500',    'text' => 'text-rose-700',    'bg' => 'bg-rose-50',    'border' => 'border-rose-200',    'icon' => 'fa-circle-xmark', 'label' => 'Reservasi Dibatalkan'],
        'in_progress'           => ['dot' => 'bg-blue-500',    'text' => 'text-blue-700',    'bg' => 'bg-blue-50',    'border' => 'border-blue-200',    'icon' => 'fa-wand-magic-sparkles', 'label' => 'Sedang Berlangsung'],
        'confirmed'             => ['dot' => 'bg-primary',     'text' => 'text-primary',     'bg' => 'bg-[#FFF0F2]',  'border' => 'border-[#F4DDE1]',   'icon' => 'fa-calendar-check', 'label' => 'Jadwal Terkonfirmasi'],
        default                 => ['dot' => 'bg-amber-500',   'text' => 'text-amber-800',   'bg' => 'bg-amber-50',   'border' => 'border-amber-200',   'icon' => 'fa-clock-rotate-left', 'label' => 'Menunggu Konfirmasi'],
    };

    $payMeta = match($booking->payment_status) {
        'paid'     => ['text' => 'text-emerald-700', 'bg' => 'bg-emerald-50',  'border' => 'border-emerald-200', 'icon' => 'fa-circle-check', 'label' => 'Lunas'],
        'dp_paid'  => ['text' => 'text-blue-700',    'bg' => 'bg-blue-50',     'border' => 'border-blue-200',    'icon' => 'fa-receipt',      'label' => 'DP Terbayar'],
        'refunded' => ['text' => 'text-indigo-700',  'bg' => 'bg-indigo-50',   'border' => 'border-indigo-200',  'icon' => 'fa-rotate-left',  'label' => 'Refunded'],
        'pending'  => ['text' => 'text-amber-800',   'bg' => 'bg-amber-50',    'border' => 'border-amber-200',   'icon' => 'fa-clock',        'label' => 'Menunggu Pembayaran'],
        default    => ['text' => 'text-rose-700',    'bg' => 'bg-rose-50',     'border' => 'border-rose-200',    'icon' => 'fa-circle-xmark', 'label' => 'Belum Bayar'],
    };

    $tStart = $booking->time_start ? \Carbon\Carbon::parse($booking->time_start)->format('H:i') : '-';
    $tEnd   = $booking->time_end   ? \Carbon\Carbon::parse($booking->time_end)->format('H:i')   : '-';
    $totalDuration = $booking->treatments->sum('duration_minutes');
    $dateStr = $booking->booking_date ? $booking->booking_date->translatedFormat('l, d F Y') : '-';

    // Step index (1: Pending, 2: Confirmed, 3: In Progress, 4: Completed)
    $stepIdx = match($statusVal) {
        'completed' => 4,
        'in_progress' => 3,
        'confirmed' => 2,
        default => 1,
    };
@endphp

<div class="pass-font min-h-screen pt-28 pb-20 px-4 sm:px-6 relative text-[#2B0F23]">

    <div class="max-w-3xl mx-auto space-y-6 pass-enter">

        {{-- ─── Flash Messages ─── --}}
        @if(session('success'))
            <div class="no-print flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="no-print flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif
        @error('photo_assign')
            <div class="no-print flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                <span>{{ $message }}</span>
            </div>
        @enderror

        {{-- ═════════════════════════════════════════════════════════════ --}}
        {{-- LUXURY APPOINTMENT PASS (Main Card)                           --}}
        {{-- ═════════════════════════════════════════════════════════════ --}}
        <div class="pass-container bg-white rounded-3xl border border-[#F4DDE1] shadow-md overflow-hidden">

            {{-- 1. Pass Header Bar --}}
            <div class="bg-gradient-to-r from-[#2B0F23] via-[#5C1439] to-[#7A1F52] text-white p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 shadow-md border-2 border-[#F4B942]/50 bg-white flex items-center justify-center p-0.5">
                        <img src="{{ asset('logo/yalia-logos.svg') }}" alt="Yalia Beauty" width="48" height="48" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#FDE2ED] block">Luxury Appointment Pass</span>
                        <h1 class="pass-serif text-xl sm:text-2xl font-bold tracking-tight text-white">Yalia Beauty Salon</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:self-center">
                    <span class="pass-mono text-xs sm:text-sm font-bold bg-white/15 px-3 py-1.5 rounded-xl border border-white/20 tracking-wider">
                        #{{ $booking->booking_code }}
                    </span>
                </div>
            </div>

            {{-- 2. Status & Quick QR Check-in Banner --}}
            <div class="p-5 sm:p-6 bg-[#FFF8FA] border-b border-[#F4DDE1] flex flex-col md:flex-row md:items-center justify-between gap-6">
                {{-- Status & Overview --}}
                <div class="space-y-2 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusMeta['bg'] }} {{ $statusMeta['text'] }} {{ $statusMeta['border'] }}">
                            <span class="w-2 h-2 rounded-full {{ $statusMeta['dot'] }}"></span>
                            <i class="fa-solid {{ $statusMeta['icon'] }} text-xs"></i>
                            <span>{{ $statusMeta['label'] }}</span>
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $payMeta['bg'] }} {{ $payMeta['text'] }} {{ $payMeta['border'] }}">
                            <i class="fa-solid {{ $payMeta['icon'] }} text-xs"></i>
                            <span>{{ $payMeta['label'] }}</span>
                        </span>

                        @if($booking->booking_type === 'home' || $booking->booking_type === 'home_service')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                <i class="fa-solid fa-house-chimney text-xs"></i>
                                <span>Home Service</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                <i class="fa-solid fa-store text-xs"></i>
                                <span>Ke Salon</span>
                            </span>
                        @endif
                    </div>

                    <h2 class="pass-serif text-lg sm:text-xl font-bold text-[#2B0F23]">
                        Reservasi Atas Nama <span class="text-primary">{{ $booking->user?->name ?? 'Pelanggan' }}</span>
                    </h2>
                    <p class="text-xs text-[#5C1439]/75">
                        Tunjukkan kode booking atau scan QR Code di samping saat tiba di lokasi perawatan.
                    </p>
                </div>

                {{-- QR Code Card (Clean, Sharp & Fast) --}}
                <div class="shrink-0 flex items-center gap-3 p-3 bg-white rounded-2xl border border-[#F4DDE1] shadow-sm self-start md:self-auto">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($booking->booking_code) }}"
                         alt="QR Booking"
                         width="80"
                         height="80"
                         class="w-20 h-20 rounded-lg object-contain">
                    <div class="space-y-0.5 pr-2">
                        <span class="text-xs font-extrabold uppercase tracking-widest text-[#5C1439]/60 block">Check-in QR</span>
                        <span class="pass-mono text-xs font-black text-[#2B0F23] block">#{{ $booking->booking_code }}</span>
                        <span class="text-xs text-emerald-700 font-bold block">Siap di-scan</span>
                    </div>
                </div>
            </div>

            {{-- 3. Visual Timeline Stepper (Unless Cancelled) --}}
            @if(!$isCancelled)
            <div class="p-5 sm:p-6 border-b border-[#F4DDE1] bg-white">
                <p class="text-xs font-bold uppercase tracking-widest text-[#5C1439]/60 mb-4">Tahapan Layanan</p>

                <div class="grid grid-cols-4 gap-2 relative">
                    @php
                        $steps = [
                            ['num' => 1, 'label' => 'Diajukan',    'icon' => 'fa-file-invoice'],
                            ['num' => 2, 'label' => 'Terkonfirmasi','icon' => 'fa-calendar-check'],
                            ['num' => 3, 'label' => 'Perawatan',   'icon' => 'fa-wand-magic-sparkles'],
                            ['num' => 4, 'label' => 'Selesai',     'icon' => 'fa-circle-check'],
                        ];
                    @endphp

                    @foreach ($steps as $st)
                        @php
                            $isDone = $stepIdx >= $st['num'];
                            $isCurrent = $stepIdx === $st['num'];
                        @endphp
                        <div class="flex flex-col items-center text-center gap-1.5 relative z-10">
                            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-xs sm:text-sm font-bold transition-all
                                        {{ $isDone ? 'bg-primary text-white shadow-sm' : 'bg-[#FFF0F2] text-[#8D7072] border border-[#F4DDE1]' }}">
                                <i class="fa-solid {{ $st['icon'] }}"></i>
                            </div>
                            <span class="text-xs font-bold {{ $isCurrent ? 'text-primary' : ($isDone ? 'text-[#2B0F23]' : 'text-[#8D7072]') }}">
                                {{ $st['label'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- 4. Detail Jadwal, Terapis & Lokasi (Grid Box) --}}
            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5 bg-white border-b border-[#F4DDE1]">
                {{-- Tanggal & Waktu --}}
                <div class="p-3.5 rounded-2xl bg-[#FFF8FA] border border-[#F4DDE1] flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <i class="fa-regular fa-calendar text-sm"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#5C1439]/60 block">Waktu Reservasi</span>
                        <p class="text-xs font-bold text-[#2B0F23] mt-0.5">{{ $dateStr }}</p>
                        <p class="text-xs text-primary font-semibold">{{ $tStart }} – {{ $tEnd }} WIB <span class="text-[#5C1439]/60 font-normal">({{ $totalDuration }} mnt)</span></p>
                    </div>
                </div>

                {{-- Beautician Specialist --}}
                <div class="p-3.5 rounded-2xl bg-[#FFF8FA] border border-[#F4DDE1] flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-user-nurse text-sm"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#5C1439]/60 block">Spesialis Terapis</span>
                        <p class="text-xs font-bold text-[#2B0F23] mt-0.5">{{ $booking->beautician?->name ?? 'Auto Assign (Terapis Salon)' }}</p>
                        <p class="text-xs text-[#5C1439]/70">Pelayanan Profesional Yalia</p>
                    </div>
                </div>

                {{-- Tipe & Lokasi --}}
                <div class="p-3.5 rounded-2xl bg-[#FFF8FA] border border-[#F4DDE1] flex items-start gap-3 sm:col-span-2 md:col-span-1">
                    <div class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-location-dot text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#5C1439]/60 block">Lokasi Kunjungan</span>
                        @if($booking->booking_type === 'home')
                            <p class="text-xs font-bold text-[#2B0F23] mt-0.5 truncate">{{ $booking->home_address ?? 'Alamat Pelanggan' }}</p>
                            <p class="text-xs text-purple-700 font-semibold">Layanan Home Service</p>
                        @else
                            <p class="text-xs font-bold text-[#2B0F23] mt-0.5">Salon Yalia Beauty</p>
                            <p class="text-xs text-amber-800 font-semibold">Datang Langsung ke Tempat</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 5. Treatment Items & Price Breakdown --}}
            <div class="p-5 sm:p-6 bg-white space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-[#F4DDE1]">
                    <span class="text-xs font-bold uppercase tracking-widest text-[#5C1439]/60">Daftar Layanan</span>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-[#FFF0F2] text-primary border border-[#F4DDE1]">
                        {{ $booking->treatments->count() }} Treatment
                    </span>
                </div>

                {{-- Treatment List --}}
                <div class="divide-y divide-[#F4DDE1]">
                    @forelse($booking->bookingTreatments as $item)
                        @php
                            $trObj = $item->Treatments ?? $item->treatment;
                            $trImg = $trObj?->image_url ?? \App\Support\ImageHelper::url($trObj?->images);
                        @endphp
                        <div class="py-3.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-12 h-12 rounded-xl bg-[#FFF0F2] border border-[#F4DDE1] overflow-hidden shrink-0">
                                    @if($trImg)
                                        <img src="{{ $trImg }}" alt="{{ $trObj?->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-primary">
                                            <i class="fa-solid fa-spa text-base"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h4 class="pass-serif text-sm font-bold text-[#2B0F23] truncate">{{ $trObj?->name ?? 'Perawatan Yalia' }}</h4>
                                    <p class="text-xs text-[#5C1439]/70">× {{ $item->quantity }} unit · {{ $trObj?->duration_minutes ?? 0 }} menit</p>
                                </div>
                            </div>

                            <div class="text-right shrink-0 flex items-center gap-3">
                                <span class="pass-mono text-sm font-bold text-primary">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>

                                @if($isCompleted)
                                    @if($booking->review)
                                        <span class="no-print inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                                            <span>{{ number_format($booking->review->rating, 1) }}</span>
                                        </span>
                                    @else
                                        <a href="{{ route('user.treatments.review', ['booking' => $booking->id, 'treatment' => $trObj?->id]) }}"
                                           class="no-print inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border border-amber-300 bg-amber-50 text-amber-900 hover:bg-amber-100 transition-all shadow-xs">
                                            <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                                            <span>Beri Ulasan (+15 PTS)</span>
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        @foreach($booking->treatments as $tr)
                            @php
                                $trImg = $tr->image_url ?? \App\Support\ImageHelper::url($tr->images);
                            @endphp
                            <div class="py-3.5 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <div class="w-12 h-12 rounded-xl bg-[#FFF0F2] border border-[#F4DDE1] overflow-hidden shrink-0">
                                        @if($trImg)
                                            <img src="{{ $trImg }}" alt="{{ $tr->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-primary">
                                                <i class="fa-solid fa-spa text-base"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="pass-serif text-sm font-bold text-[#2B0F23] truncate">{{ $tr->name }}</h4>
                                        <p class="text-xs text-[#5C1439]/70">× 1 unit · {{ $tr->duration_minutes ?? 0 }} menit</p>
                                    </div>
                                </div>

                                <div class="text-right shrink-0 flex items-center gap-3">
                                    <span class="pass-mono text-sm font-bold text-primary">
                                        Rp {{ number_format($tr->price, 0, ',', '.') }}
                                    </span>

                                    @if($isCompleted)
                                        <a href="{{ route('user.treatments.review', ['booking' => $booking->id, 'treatment' => $tr->id]) }}"
                                           class="no-print inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-bold border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 transition-all shadow-xs">
                                            <i class="fa-solid fa-star text-amber-500 text-xs"></i>
                                            <span>Beri Ulasan</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforelse
                </div>

                {{-- Financial Summary Box --}}
                <div class="pt-4 border-t border-[#F4DDE1] space-y-2">
                    <div class="flex justify-between text-xs text-[#5C1439]/80 font-medium">
                        <span>Subtotal Layanan</span>
                        <span class="pass-mono font-bold text-[#2B0F23]">Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}</span>
                    </div>

                    @if(($booking->discount_amount ?? 0) > 0)
                        <div class="flex justify-between text-xs text-emerald-700 font-semibold">
                            <span>Potongan Diskon Voucher</span>
                            <span class="pass-mono font-bold">– Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if(($booking->transport_fee ?? 0) > 0)
                        <div class="flex justify-between text-xs text-[#5C1439]/80 font-medium">
                            <span>Biaya Transport Home Service</span>
                            <span class="pass-mono font-bold text-[#2B0F23]">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    @if($booking->payment_type === 'cash')
                        <div class="p-3 bg-amber-50/80 border border-amber-200 rounded-2xl space-y-1 text-xs my-2">
                            <div class="flex justify-between text-amber-900 font-bold">
                                <span>DP 35% (Terbayar via QRIS):</span>
                                <span class="pass-mono">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-amber-800 font-semibold">
                                <span>Sisa Pelunasan Tunai (65% di Salon):</span>
                                <span class="pass-mono">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endif

                    <div class="pt-3 border-t border-[#F4DDE1] flex justify-between items-center">
                        <div>
                            <span class="text-sm font-extrabold text-[#2B0F23] block">
                                {{ $booking->payment_status === 'dp_paid' ? 'Total Nilai Treatment' : 'Total Pembayaran' }}
                            </span>
                            <span class="text-xs text-[#5C1439]/70 font-medium">Metode: {{ strtoupper($booking->payment_method ?? 'QRIS') }}</span>
                        </div>
                        <span class="pass-serif text-xl sm:text-2xl font-black text-primary">
                            Rp {{ number_format($booking->total_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- 6. Customer Review Summary (If Already Reviewed) --}}
            @if($booking->review)
                <div class="p-5 sm:p-6 bg-[#FFF8FA] border-t border-[#F4DDE1] space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#5C1439]/60">Ulasan & Penilaian Anda</span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-check text-xs"></i> +15 PTS Diklaim
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 rounded-2xl bg-white border border-[#F4DDE1] space-y-1">
                            <span class="text-xs font-bold text-[#5C1439]/70">Rating Treatment</span>
                            <div class="flex items-center gap-1 text-amber-400 text-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $booking->review->rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                @endfor
                                <span class="font-bold text-xs text-[#2B0F23] ml-1">{{ $booking->review->rating }}.0</span>
                            </div>
                            @if($booking->review->comment)
                                <p class="text-xs text-[#2B0F23] italic mt-1 font-medium">"{{ $booking->review->comment }}"</p>
                            @endif
                        </div>

                        @if($booking->beautician && $booking->review->beautician_rating)
                            <div class="p-3.5 rounded-2xl bg-white border border-[#F4DDE1] space-y-1">
                                <span class="text-xs font-bold text-[#5C1439]/70">Rating Terapis ({{ $booking->beautician->name }})</span>
                                <div class="flex items-center gap-1 text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $booking->review->beautician_rating ? 'text-amber-400' : 'text-gray-200' }}"></i>
                                    @endfor
                                    <span class="font-bold text-xs text-[#2B0F23] ml-1">{{ $booking->review->beautician_rating }}.0</span>
                                </div>
                                @php
                                    $tags = is_string($booking->review->beautician_tags) ? json_decode($booking->review->beautician_tags, true) : $booking->review->beautician_tags;
                                @endphp
                                @if(!empty($tags) && is_array($tags))
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($tags as $tg)
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-[#FFF0F2] text-primary border border-[#F4DDE1]">
                                                {{ $tg }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- 7. Documentation / Photo Assign --}}
            <div class="no-print p-5 sm:p-6 bg-white border-t border-[#F4DDE1]">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-widest text-[#5C1439]/60">Dokumentasi Hasil Perawatan</span>
                        <p class="text-xs text-[#5C1439]/75 mt-0.5">Foto hasil perawatan after-service dari kunjungan Anda.</p>
                    </div>
                    @if($booking->photo_assign)
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">Terlampir</span>
                    @endif
                </div>

                @if($booking->photo_assign)
                    <div class="space-y-3">
                        <div class="relative rounded-2xl overflow-hidden border border-[#F4DDE1] max-h-72 group">
                            <img src="{{ \App\Support\ImageHelper::url($booking->photo_assign) }}" alt="Foto Hasil Treatment" class="w-full h-full object-cover">
                        </div>
                        <form action="{{ route('user.bookings.photo-assign', $booking) }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
                            @csrf
                            <input type="file" name="photo_assign" accept="image/*" class="hidden" id="photoInputReplace" onchange="this.form.submit()">
                            <button type="button" onclick="document.getElementById('photoInputReplace').click()" class="px-4 py-2 rounded-full border border-primary text-primary bg-white hover:bg-[#FFF0F2] text-xs font-bold active:scale-95 transition-all cursor-pointer">
                                <i class="fa-solid fa-camera-rotate mr-1"></i> Ganti Foto
                            </button>
                            <span class="text-xs text-[#5C1439]/60">Maksimal 5 MB (JPEG, PNG, WebP)</span>
                        </form>
                    </div>
                @else
                    <form action="{{ route('user.bookings.photo-assign', $booking) }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        <input type="file" name="photo_assign" accept="image/*" class="hidden" id="photoInput" onchange="document.getElementById('photoForm').submit()">
                        <button type="button" onclick="document.getElementById('photoInput').click()" class="w-full py-6 rounded-2xl border-2 border-dashed border-[#F4DDE1] hover:border-primary/50 bg-white hover:bg-[#FFF0F2]/50 text-center transition-all flex flex-col items-center justify-center gap-2 cursor-pointer">
                            <div class="w-10 h-10 rounded-xl bg-[#FFF0F2] text-primary flex items-center justify-center">
                                <i class="fa-solid fa-cloud-arrow-up text-lg"></i>
                            </div>
                            <span class="text-xs font-bold text-[#2B0F23]">Unggah Foto Hasil Treatment</span>
                            <span class="text-xs text-[#5C1439]/60">Klik untuk memilih file · Maks 5 MB</span>
                        </button>
                    </form>
                @endif
            </div>

            {{-- 7. Customer Notes / Cancel Reason (If Any) --}}
            @if($booking->notes || $booking->cancel_reason)
                <div class="p-5 sm:p-6 bg-white border-t border-[#F4DDE1] space-y-2">
                    @if($booking->notes)
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#5C1439]/60">Catatan Khusus:</span>
                            <p class="text-xs text-[#2B0F23] mt-0.5 font-medium">{{ $booking->notes }}</p>
                        </div>
                    @endif
                    @if($booking->cancel_reason)
                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-200">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-700">Alasan Pembatalan:</span>
                            <p class="text-xs text-rose-900 mt-0.5 font-medium">{{ $booking->cancel_reason }}</p>
                        </div>
                    @endif
                </div>
            @endif

        </div>{{-- End Pass Container --}}

        {{-- ═════════════════════════════════════════════════════════════ --}}
        {{-- ACTION TOOLBAR (Ergonomic & Clean)                            --}}
        {{-- ═════════════════════════════════════════════════════════════ --}}
        <div class="no-print space-y-3"
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

            {{-- Main Actions --}}
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <button type="button" onclick="window.print()"
                        class="w-full sm:flex-1 py-3.5 px-6 rounded-full font-bold text-xs sm:text-sm bg-primary hover:bg-primary-container text-white shadow-sm hover:shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-print text-sm"></i>
                    <span>Cetak / Simpan Pass PDF</span>
                </button>

                @if($isPending)
                    <a href="{{ route('user.bookings.payment', $booking) }}"
                       class="w-full sm:flex-1 py-3.5 px-6 rounded-full font-black text-xs sm:text-sm bg-gradient-to-r from-primary via-[#C82D53] to-secondary text-white shadow-sm hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-bolt text-sm"></i>
                        <span>Bayar Sekarang</span>
                    </a>
                @endif
            </div>

            {{-- Secondary Actions (Reschedule & Cancel) --}}
            @if(in_array($statusVal, ['pending', 'confirmed']))
                <div class="flex items-center gap-3">
                    <button type="button" @click="openReschedule = true; fetchSlots();"
                            class="flex-1 py-2.5 px-4 rounded-full font-bold text-xs border border-primary/40 bg-[#FFF0F2] text-primary hover:bg-[#FFE5EC] active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-regular fa-calendar-days text-xs"></i>
                        <span>Ganti Jadwal</span>
                    </button>

                    <button type="button" @click="openCancel = true"
                            class="flex-1 py-2.5 px-4 rounded-full font-bold text-xs border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 active:scale-95 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-xmark text-xs"></i>
                        <span>Batalkan Reservasi</span>
                    </button>
                </div>
            @endif

            {{-- Back Link --}}
            <div class="text-center pt-2">
                <a href="{{ route('user.bookings.index') }}"
                   class="inline-flex items-center gap-2 text-xs font-bold text-[#5C1439]/80 hover:text-primary transition-colors py-2 px-4 rounded-full hover:bg-[#FFF0F2]">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Riwayat Reservasi</span>
                </a>
            </div>

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
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#2B0F23] text-base">Batalkan Booking</h3>
                                <p class="text-xs text-[#5C1439]/70">Mohon beritahu kami alasannya</p>
                            </div>
                        </div>
                        <button type="button" @click="openCancel = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 rounded-full w-8 h-8 flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        @if($booking->payment_status === 'dp_paid')
                            <div class="bg-rose-50 text-rose-700 text-xs p-3 rounded-xl border border-rose-100 font-medium">
                                <i class="fa-solid fa-circle-info mr-1"></i> Pembayaran DP Anda akan hangus sesuai kebijakan salon.
                            </div>
                        @elseif($booking->payment_status === 'paid' || $booking->payment_status === 'fullpayment')
                            <div class="bg-amber-50 text-amber-800 text-xs p-3 rounded-xl border border-amber-100 font-medium">
                                <i class="fa-solid fa-circle-info mr-1"></i> Dana pembayaran penuh Anda akan dikembalikan 100% oleh tim admin.
                            </div>
                        @endif

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Alasan Pembatalan</label>
                            <textarea name="reason" rows="3" required
                                      class="w-full text-sm rounded-2xl border-gray-200 focus:border-primary focus:ring focus:ring-primary/20 transition-all bg-gray-50"
                                      placeholder="Contoh: Ada urusan mendadak, jadwal bentrok..."></textarea>
                        </div>
                        
                        <div class="pt-2 flex gap-3">
                            <button type="button" @click="openCancel = false"
                                    class="flex-1 py-2.5 rounded-full font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors text-xs">
                                Batal
                            </button>
                            <button type="submit"
                                    class="flex-1 py-2.5 rounded-full font-bold text-white bg-rose-600 hover:bg-rose-700 transition-all shadow-sm active:scale-95 text-xs cursor-pointer">
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
                <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl border border-[#F4DDE1] space-y-4 text-left relative"
                     @click.away="openReschedule = false">
                    <div class="flex items-center justify-between border-b border-[#F4DDE1] pb-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-[#FFF0F2] text-primary font-bold">
                                <i class="fa-regular fa-calendar-days text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-[#2B0F23] text-base">Ganti Jadwal Reservasi</h3>
                                <p class="text-xs text-[#5C1439]/70">Pilih tanggal dan jam baru</p>
                            </div>
                        </div>
                        <button type="button" @click="openReschedule = false" class="text-gray-400 hover:text-gray-600 p-1">
                            <i class="fa-solid fa-xmark text-lg"></i>
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
                                   class="w-full rounded-xl border-gray-200 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-xs font-bold text-gray-700">Pilih Jam Baru</label>
                                <span class="text-xs text-gray-400 font-semibold">09:00 - 18:00</span>
                            </div>

                            <template x-if="loadingSlots">
                                <div class="py-6 text-center text-xs text-gray-400">
                                    <i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Memeriksa ketersediaan slot...
                                </div>
                            </template>

                            <template x-if="!loadingSlots">
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-1">
                                    <template x-for="slot in slots" :key="slot.time">
                                        <button type="button"
                                                @click="if (slot.available) selectedTime = slot.time"
                                                :disabled="!slot.available"
                                                class="relative rounded-xl p-2 min-h-[54px] border transition-all flex flex-col items-center justify-center gap-0.5 text-center overflow-hidden w-full cursor-pointer"
                                                :class="{
                                                    'border-primary bg-primary text-white font-bold shadow-sm': selectedTime === slot.time && slot.available,
                                                    'border-[#F4DDE1] bg-white text-[#2B0F23] hover:border-primary hover:bg-[#FFF0F2]': selectedTime !== slot.time && slot.available,
                                                    'border-gray-200 bg-gray-50 text-gray-400 opacity-60 cursor-not-allowed': !slot.available
                                                }">
                                            <span class="text-xs font-bold" x-text="slot.formatted_time"></span>
                                            <span x-show="slot.available" class="text-xs font-bold" :class="selectedTime === slot.time ? 'text-white' : 'text-emerald-700'">Tersedia</span>
                                            <span x-show="!slot.available" class="text-xs font-bold text-gray-400">Penuh</span>
                                        </button>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Alasan Perubahan (Opsional)</label>
                            <input type="text" name="reason" placeholder="Misal: Ada keperluan mendadak..."
                                   class="w-full rounded-xl border-gray-200 text-sm focus:border-primary focus:ring-primary">
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-[#F4DDE1]">
                            <button type="button" @click="openReschedule = false"
                                    class="px-4 py-2 rounded-full text-xs font-bold text-gray-600 hover:bg-gray-100">
                                Batal
                            </button>
                            <button type="submit"
                                    :disabled="!selectedTime"
                                    class="px-5 py-2 rounded-full text-xs font-bold text-white bg-primary hover:bg-primary-container shadow-sm active:scale-95 transition-all disabled:opacity-50 cursor-pointer">
                                Simpan Jadwal Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- End Action Toolbar --}}

    </div>
</div>
@endsection
