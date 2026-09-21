{{--
    resources/views/components/daily-reward-card.blade.php

    Komponen Kartu Daily Reward Yalia Beauty — Sanctuary of Soft Elegance.
    Menampilkan header kilau spa, day strip 7 hari interaktif, kartu pesan harian,
    penghitung reward poin, tombol klaim kapsul (pill CTA), dan tracker progres streak.

    Pemakaian:
    <x-daily-reward-card
        badge="Aura Glow-Up Memancar!"
        title="Day 1: Muka Bantal Eradication!"
        message="Selamat Ratu Beauty! Kamu berhasil mengklaim misi hari ini. Bonus poin PTS telah disimpan ke akunmu!"
        :reward="25"
        :days="[
            ['label' => 'Sen', 'status' => 'done'],
            ['label' => 'Sel', 'status' => 'done'],
            ['label' => 'Rab', 'status' => 'today'],
            ['label' => 'Kam', 'status' => 'locked'],
            ['label' => 'Jum', 'status' => 'locked'],
            ['label' => 'Sab', 'status' => 'locked'],
            ['label' => 'Min', 'status' => 'locked'],
        ]"
        cta-text="Siap Glow-Up Banget!"
        streak-label="Streak 7 hari untuk voucher diskon"
        :streak-days-left="4"
        :streak-progress="42"
    />
--}}

@props([
    'badge' => 'Aura Glow-Up Memancar!',
    'title' => 'Day 1: Misi Hari Ini!',
    'message' => '',
    'reward' => 25,
    'days' => [],
    'ctaText' => 'Klaim Hadiah Hari Ini',
    'streakLabel' => 'Streak 7 hari untuk voucher diskon',
    'streakDaysLeft' => 7,
    'streakProgress' => 0,
    'disabled' => false,
])

<div class="reward-card">

    {{-- Header: Soft blush gradient + Sparkle icon --}}
    <div class="reward-card__header">
        <span class="reward-card__spark reward-card__spark--1" aria-hidden="true">✦</span>
        <span class="reward-card__spark reward-card__spark--2" aria-hidden="true">✦</span>

        <div class="reward-card__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" width="26" height="26" fill="none">
                <path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z" fill="#ffffff"/>
            </svg>
        </div>
    </div>

    {{-- Body --}}
    <div class="reward-card__body">
        <span class="reward-card__badge">{{ $badge }}</span>

        <h3 class="reward-card__title" title="{{ $title }}">{{ $title }}</h3>

        {{-- Day Strip (7 Hari) --}}
        @if(count($days))
            <div class="reward-card__days" role="list" aria-label="Jadwal streak 7 hari">
                @foreach($days as $day)
                    @php
                        $dayStatus = $day['status'] ?? 'locked';
                    @endphp
                    <div class="reward-card__day reward-card__day--{{ $dayStatus }}" role="listitem">
                        <span class="reward-card__day-label">{{ $day['label'] ?? '' }}</span>
                        <span class="reward-card__day-mark" title="Hari {{ $day['label'] ?? '' }}: {{ $dayStatus === 'done' ? 'Selesai' : ($dayStatus === 'today' ? 'Hari Ini' : 'Terkunci') }}">
                            @if($dayStatus === 'done')
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M4 12l5 5L20 6"/>
                                </svg>
                            @elseif($dayStatus === 'today')
                                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 3l2 4 4.5.5-3.5 3 1 4.5-4-2.5-4 2.5 1-4.5-3.5-3 4.5-.5z"/>
                                </svg>
                            @else
                                <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Message / Witty text --}}
        @if($message)
            <p class="reward-card__message">{{ $message }}</p>
        @endif

        {{-- Reward Row --}}
        <div class="reward-card__reward">
            <span class="reward-card__reward-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </span>
            <span class="reward-card__reward-label">Hadiah:</span>
            <span class="reward-card__reward-value">+{{ $reward }} pts</span>
        </div>

        {{-- CTA Button --}}
        <button type="button"
                {{ $attributes->merge(['class' => 'reward-card__cta']) }}
                @disabled($disabled)>
            {{ $ctaText }}
        </button>
    </div>

    {{-- Streak Footer --}}
    <div class="reward-card__streak">
        <div class="reward-card__streak-icon" title="{{ $streakDaysLeft }} hari lagi">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
            </svg>
        </div>
        <div class="reward-card__streak-info">
            <span class="reward-card__streak-label">{{ $streakLabel }}</span>
            <span class="reward-card__streak-days">{{ $streakDaysLeft }} hari lagi untuk klaim bonus</span>
            <div class="reward-card__progress">
                <div class="reward-card__progress-bar" style="width: {{ max(5, min(100, (int) $streakProgress)) }}%"></div>
            </div>
        </div>
    </div>

</div>
