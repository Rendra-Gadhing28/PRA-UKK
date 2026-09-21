{{--
    resources/views/components/beautician-profile-card.blade.php

    Komponen Profil Card Beautician Yalia Beauty.
    - Card serba rounded besar (--card-radius: 28px) berlatar putih bersih dengan soft shadow.
    - Foto potret elegan (--img-radius: 22px) dengan hover scale halus & monogram placeholder.
    - Floating pill tag spesialisasi / status ketersediaan di atas foto.
    - Lencana terverifikasi (Verified badge) di samping nama.
    - Tipografi editorial Playfair Display (nama) & Work Sans (deskripsi & stats).
    - Baris stats: Jumlah klien / layanan selesai + Skor rating bintang.
    - Tombol aksi Booking kapsul (pill-shaped) di pojok kanan bawah.

    Pemakaian:
    1. Menggunakan Model Beauticians:
       <x-beautician-profile-card :beautician="$beautician" />

    2. Menggunakan Props Kustom:
       <x-beautician-profile-card
           :image="asset('images/beautician/sarah.jpg')"
           name="Sarah Swift"
           :verified="true"
           profession="Spesialis Facial & Lash"
           description="Terapis bersertifikat yang fokus pada hasil alami dan tahan lama."
           :clients="312"
           :rating="4.9"
           :booking-url="route('user.bookings.create', ['beautician_id' => 1])"
       />
--}}

@props([
    'beautician' => null,
    'image' => null,
    'name' => null,
    'verified' => true,
    'profession' => 'Beautician Spesialis',
    'description' => null,
    'clients' => null,
    'rating' => null,
    'works' => null,
    'bookingUrl' => null,
    'bookingLabel' => 'Booking',
    'badge' => null,
    'isAvailable' => true,
])

@php
    // Sinkronisasi data bila diberikan instance Model Beauticians
    $resolvedName = $name ?: ($beautician?->name ?? 'Nama Beautician');
    $resolvedImage = $image ?: ($beautician?->photo_url ?? null);
    $resolvedBio = $description ?: ($beautician?->bio ?? $profession);
    $resolvedClients = $clients ?? ($beautician?->total_bookings ?? 0);
    $resolvedVerified = $beautician !== null ? (bool) ($beautician->is_active ?? true) : (bool) $verified;
    $resolvedId = $beautician?->id ?? null;
    $resolvedBookingUrl = $bookingUrl ?: ($resolvedId ? route('user.bookings.create', ['beautician_id' => $resolvedId]) : route('user.bookings.create'));
    $resolvedBadge = $badge ?: ($profession !== 'Beautician Spesialis' ? $profession : null);

    // Hitung inisial nama untuk monogram placeholder bila foto kosong
    $nameParts = array_filter(explode(' ', trim($resolvedName)));
    $initials = '';
    if (!empty($nameParts)) {
        $first = mb_substr($nameParts[0], 0, 1);
        $second = isset($nameParts[1]) ? mb_substr($nameParts[1], 0, 1) : '';
        $initials = strtoupper($first . $second);
    }
@endphp

<div class="beauty-card">

    {{-- Area Media / Foto Potret --}}
    <div class="beauty-card__media">
        {{-- Floating Badge Spesialisasi --}}
        @if($resolvedBadge)
            <span class="beauty-card__badge-specialty">{{ $resolvedBadge }}</span>
        @endif

        {{-- Floating Status Ketersediaan --}}
        @if($isAvailable)
            <span class="beauty-card__badge-status" title="Terapis Aktif & Siap Menerima Reservasi">
                <span class="beauty-card__status-dot is-pulse" aria-hidden="true"></span>
                <span>Tersedia</span>
            </span>
        @endif

        @if($resolvedImage)
            <img src="{{ $resolvedImage }}"
                 alt="Foto profil {{ $resolvedName }}"
                 class="beauty-card__img"
                 loading="lazy"
                 decoding="async">
        @else
            <div class="beauty-card__img-placeholder">
                <span class="beauty-card__placeholder-initials">{{ $initials ?: 'YB' }}</span>
                <span class="beauty-card__placeholder-sub">Yalia Beautician</span>
            </div>
        @endif
    </div>

    {{-- Header: Nama + Badge Terverifikasi --}}
    <div class="beauty-card__header">
        <h3 class="beauty-card__name" title="{{ $resolvedName }}">{{ $resolvedName }}</h3>

        @if($resolvedVerified)
            <span class="beauty-card__verified" aria-label="Beautician Resmi Terverifikasi" title="Terapis Resmi Terverifikasi">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" aria-hidden="true">
                    <path d="M12 2l2.2 1.3 2.5-.4 1.2 2.2 2.3 1.1-.3 2.6 1.6 2-1.6 2 .3 2.6-2.3 1.1-1.2 2.2-2.5-.4L12 22l-2.2-1.3-2.5.4-1.2-2.2-2.3-1.1.3-2.6L2.5 13l1.6-2-.3-2.6 2.3-1.1 1.2-2.2 2.5.4L12 2Z" fill="#14A879"/>
                    <path d="M8.5 12.2l2.2 2.2 4.3-4.6" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
        @endif
    </div>

    {{-- Deskripsi / Keahlian (Line Clamped 2 Baris) --}}
    <p class="beauty-card__desc" title="{{ $resolvedBio }}">{{ $resolvedBio }}</p>

    {{-- Footer: Baris Stats + Tombol Aksi Booking --}}
    <div class="beauty-card__footer">
        <div class="beauty-card__stats">
            {{-- Stat 1: Total Klien / Reservasi Selesai --}}
            <span class="beauty-card__stat" title="{{ number_format((int) $resolvedClients) }} reservasi selesai">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M5.5 21a6.5 6.5 0 0 1 13 0"/>
                </svg>
                <span>{{ $resolvedClients > 0 ? number_format((int) $resolvedClients) : '150+' }}</span>
            </span>

            {{-- Stat 2: Rating Ulasan Bintang atau Jumlah Karya --}}
            @if($rating !== null)
                <span class="beauty-card__stat beauty-card__stat--rating" title="Rating kepuasan pelanggan {{ number_format((float) $rating, 1) }}">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span>{{ number_format((float) $rating, 1) }}</span>
                </span>
            @elseif($works !== null)
                <span class="beauty-card__stat" title="{{ $works }} portofolio hasil karya">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="4" y="4" width="12" height="14" rx="2"/>
                        <path d="M8 20h12V8"/>
                    </svg>
                    <span>{{ $works }}</span>
                </span>
            @else
                {{-- Default Salon Rating --}}
                <span class="beauty-card__stat beauty-card__stat--rating" title="Rating kepuasan pelanggan 4.9">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                    </svg>
                    <span>4.9</span>
                </span>
            @endif
        </div>

        {{-- Tombol Pill Booking --}}
        <a href="{{ $resolvedBookingUrl }}"
           class="beauty-card__booking"
           aria-label="Reservasi janji temu dengan {{ $resolvedName }}">
            <span>{{ $bookingLabel }}</span>
            <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M5 12h14M13 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

</div>
