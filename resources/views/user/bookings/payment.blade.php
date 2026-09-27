@extends('layouts.app')

@section('title', 'Pembayaran QRIS Reservasi #' . $booking->booking_code . ' — Yalia Beauty')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
    .payment-font  { font-family: 'Work Sans', sans-serif; }
    .payment-serif { font-family: 'Playfair Display', serif; }
    .payment-mono  { font-family: 'Space Mono', monospace; }
</style>
@endpush

@section('content')
<div class="payment-font min-h-screen pt-24 pb-20 px-4 sm:px-6 relative text-white bg-[#25181c]"
     x-data="paymentPage({
        statusUrl: '{{ route('user.bookings.payment.status', $booking) }}',
        secondsRemaining: {{ (int) max(0, now()->diffInSeconds($booking->payment_expires_at, false)) }},
        redirectUrl: '{{ route('user.bookings.show', $booking) }}'
     })"
     x-init="init()">

    {{-- Dark Ambient Glow Orbs from Root Palette --}}
    <div class="absolute top-10 left-1/2 -translate-x-1/2 w-full max-w-6xl h-96 bg-gradient-to-b from-[#b01f44]/25 via-[#9b4054]/15 to-transparent blur-3xl pointer-events-none -z-10" aria-hidden="true"></div>

    {{-- Toast Notification --}}
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="fixed top-24 left-1/2 -translate-x-1/2 z-[600] bg-[#059669] text-white px-5 py-3 rounded-full shadow-2xl text-xs sm:text-sm font-bold flex items-center gap-2 border border-emerald-300">
        <i class="fa-solid fa-circle-check text-sm text-emerald-100"></i>
        <span x-text="toast.message"></span>
    </div>

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- ── Header Breadcrumbs & Title ── --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('user.bookings.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#ffd2e1] hover:text-white transition-colors mb-2 px-3.5 py-1.5 rounded-full bg-[#331c23] border border-[#594043] hover:border-[#ffd2e1] w-fit">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Riwayat Booking</span>
                </a>
                <h1 class="payment-serif text-2xl sm:text-3xl font-extrabold text-white leading-tight drop-shadow-sm">
                    Selesaikan Pembayaran QRIS
                </h1>
                <p class="text-xs sm:text-sm text-[#ffd2e1]/90 mt-1 font-medium">
                    Pindai QRIS instan melalui mobile banking atau e-wallet pilihan Anda.
                </p>
            </div>

            {{-- Booking Code Chip --}}
            <div class="shrink-0 flex items-center gap-2">
                <button type="button" @click="copyBookingCode('{{ $booking->booking_code }}')"
                        class="px-4 py-2.5 rounded-full bg-[#331c23] border border-[#594043] shadow-lg hover:border-[#f59e0b] text-xs font-bold text-[#f59e0b] transition-all flex items-center gap-2 cursor-pointer active:scale-95"
                        title="Salin Kode Booking">
                    <span class="payment-mono tracking-wide font-extrabold">#{{ $booking->booking_code }}</span>
                    <i class="fa-regular fa-copy text-xs text-[#ffd2e1]"></i>
                </button>
            </div>
        </div>

        {{-- ── 2-COLUMN LUXURY SPLIT CHECKOUT TERMINAL ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- ════ SISI KIRI (7 Kolom): LIVE QRIS SCANNER & TIMER ════ --}}
            <div class="lg:col-span-7 space-y-5">

                {{-- Timer Card --}}
                <div class="bg-gradient-to-r from-[#1f0d11] via-[#380e22] to-[#500a1d] text-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-[#785341] relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-44 h-44 bg-[#f59e0b]/15 rounded-full blur-2xl pointer-events-none"></div>
                    <div class="flex items-center justify-between gap-4 relative z-10">
                        <div class="space-y-1">
                            <span class="text-xs font-bold uppercase tracking-widest text-[#ffd2e1] flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-xs text-[#f59e0b]"></i>
                                <span>Batas Waktu Pembayaran</span>
                            </span>
                            <p class="text-xs text-white/90">Selesaikan sebelum sesi berakhir:</p>
                        </div>

                        <div class="text-right">
                            <div class="payment-mono text-3xl sm:text-4xl font-extrabold tracking-wider text-[#f59e0b] drop-shadow-md" x-text="formattedTimer">
                                15:00
                            </div>
                        </div>
                    </div>

                    {{-- Countdown Progress Bar --}}
                    <div class="w-full bg-black/60 rounded-full h-2.5 mt-4 overflow-hidden border border-white/20 relative z-10">
                        <div class="h-full bg-gradient-to-r from-[#f59e0b] via-amber-300 to-yellow-200 rounded-full transition-all duration-1000 shadow-md"
                             :style="`width: ${Math.min(100, Math.max(0, (secondsRemaining / 900) * 100))}%;`"></div>
                    </div>
                </div>

                {{-- QRIS Interactive Frame Card --}}
                <div class="bg-[#1a0f13] rounded-3xl p-6 sm:p-7 border-2 border-[#594043] shadow-2xl text-center space-y-5">
                    
                    <div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#331c23] border border-[#785341] text-xs font-black text-[#ffd2e1] uppercase tracking-wider mb-2 shadow-sm">
                            <i class="fa-solid fa-qrcode text-xs text-[#f59e0b]"></i>
                            <span>QRIS National Standard</span>
                        </div>
                        <h2 class="payment-serif text-xl font-bold text-white">Pindai Kode QRIS</h2>
                        <p class="text-xs text-[#ffd2e1]/90 mt-1">Buka aplikasi mobile banking atau e-wallet Anda untuk memindai.</p>
                    </div>

                    {{-- QR Code Image Frame (Clean High-Contrast White Background for Scanners) --}}
                    <div class="relative inline-block p-4 sm:p-5 bg-white rounded-2xl border-4 border-[#331c23] shadow-2xl ring-4 ring-[#b01f44]/20">
                        @php
                            $qrSource = $booking->qris_image_url ?: 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($booking->qris_code ?? $booking->booking_code);
                        @endphp
                        <img src="{{ $qrSource }}"
                             alt="QRIS Yalia Beauty"
                             width="220"
                             height="220"
                             class="w-52 h-52 sm:w-60 sm:h-60 object-contain rounded-xl mx-auto">

                        {{-- Expired Overlay --}}
                        <div x-show="isExpired" x-cloak
                             class="absolute inset-0 bg-[#25181c]/95 backdrop-blur-sm rounded-2xl flex flex-col items-center justify-center p-4 text-center z-20 border border-[#b01f44]">
                            <i class="fa-solid fa-circle-xmark text-4xl text-[#b01f44] mb-2"></i>
                            <h3 class="font-bold text-white text-sm">Waktu Pembayaran Habis</h3>
                            <p class="text-xs text-[#ffd2e1] mt-1">Reservasi otomatis dibatalkan. Silakan lakukan pemesanan ulang.</p>
                            <a href="{{ route('user.treatments.index') }}" class="mt-3 px-5 py-2 rounded-full bg-[#b01f44] hover:bg-[#8f1735] text-white text-xs font-bold shadow-lg">
                                Pesan Ulang
                            </a>
                        </div>
                    </div>

                    {{-- Supported Payment Logos & Badges (High Contrast on Dark) --}}
                    <div class="pt-3 border-t border-[#331c23]">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#ffd2e1]/80 block mb-3">Mendukung Semua Bank & E-Wallet</span>
                        <div class="flex items-center justify-center gap-2 flex-wrap">
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-blue-300">BCA</span>
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-[#f59e0b]">Mandiri</span>
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-sky-300">BRI</span>
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-teal-300">BNI</span>
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-emerald-300">GoPay</span>
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-purple-300">OVO</span>
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-orange-300">ShopeePay</span>
                            <span class="px-3 py-1 rounded-lg bg-[#331c23] border border-[#594043] text-xs font-bold text-cyan-300">DANA</span>
                        </div>
                    </div>

                    {{-- Live Status Polling & Action --}}
                    <div class="pt-3.5 border-t border-[#331c23] flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#ffd2e1]">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#f59e0b] animate-ping"></span>
                            <span x-show="isChecking">Mengecek transaksi ke payment gateway...</span>
                            <span x-show="!isChecking">Menunggu konfirmasi pembayaran...</span>
                        </div>

                        <button type="button"
                                @click="checkStatusNow()"
                                :disabled="isChecking || isExpired"
                                class="w-full sm:w-auto px-5 py-2.5 rounded-full bg-[#b01f44] hover:bg-[#8f1735] text-white shadow-lg shadow-[#b01f44]/30 transition-all text-xs font-bold active:scale-95 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-arrows-rotate text-xs" :class="isChecking ? 'fa-spin' : ''"></i>
                            <span>Cek Status Sekarang</span>
                        </button>
                    </div>

                </div>

            </div>

            {{-- ════ SISI KANAN (5 Kolom): RINGKASAN LAYANAN & PROTEKSI ════ --}}
            <div class="lg:col-span-5 space-y-5">

                {{-- Summary Card --}}
                <div class="bg-[#1a0f13] rounded-3xl p-5 sm:p-6 border-2 border-[#594043] shadow-2xl space-y-4">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-[#331c23]">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#ffd2e1]">Ringkasan Pesanan</span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $booking->booking_type === 'home' ? 'bg-purple-900/70 text-purple-200 border border-purple-400' : 'bg-[#500a1d] text-[#ffd2e1] border border-[#b01f44]' }}">
                            <i class="fa-solid {{ $booking->booking_type === 'home' ? 'fa-house-chimney' : 'fa-spa' }} text-xs mr-1"></i>
                            {{ $booking->booking_type === 'home' ? 'Home Service' : 'Ke Salon' }}
                        </span>
                    </div>

                    {{-- Treatment Items --}}
                    <div class="space-y-2.5">
                        @foreach($booking->treatments as $tr)
                            <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-[#25181c] border border-[#331c23] hover:border-[#594043] transition-all text-xs">
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-bold text-white truncate">{{ $tr->name }}</h4>
                                    <p class="text-[#ffd2e1]/80 mt-0.5">{{ $tr->duration_minutes }} menit · x{{ $tr->pivot->quantity }} unit</p>
                                </div>
                                <span class="payment-mono font-bold text-[#f59e0b] shrink-0">
                                    Rp {{ number_format($tr->pivot->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    {{-- Schedule & Beautician Info Box --}}
                    <div class="p-4 rounded-2xl bg-[#331c23] border border-[#594043] space-y-2 text-xs">
                        <div class="flex items-center justify-between text-[#ffd2e1]">
                            <span class="font-medium flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-[#f59e0b] text-xs"></i>
                                <span>Jadwal:</span>
                            </span>
                            <span class="font-bold text-white">{{ $booking->booking_date ? $booking->booking_date->translatedFormat('d M Y') : '-' }} · {{ $booking->time_start }} WIB</span>
                        </div>
                        <div class="flex items-center justify-between text-[#ffd2e1]">
                            <span class="font-medium flex items-center gap-1.5">
                                <i class="fa-solid fa-user-tie text-[#f59e0b] text-xs"></i>
                                <span>Terapis:</span>
                            </span>
                            <span class="font-bold text-white">{{ $booking->beautician?->name ?? 'Auto Assign' }}</span>
                        </div>
                        @if($booking->booking_type === 'home' && $booking->home_address)
                            <div class="flex items-start justify-between text-[#ffd2e1] pt-2 border-t border-[#594043]">
                                <span class="font-medium shrink-0 flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-[#f59e0b] text-xs"></i>
                                    <span>Alamat:</span>
                                </span>
                                <span class="font-bold text-white text-right truncate max-w-[180px]">{{ $booking->home_address }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Financial Breakdown --}}
                    <div class="pt-2 border-t border-[#331c23] space-y-2 text-xs">
                        <div class="flex justify-between text-[#ffd2e1] font-medium">
                            <span>Subtotal Layanan:</span>
                            <span class="payment-mono font-bold text-white">Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}</span>
                        </div>

                        @if(($booking->discount_amount ?? 0) > 0)
                            <div class="flex justify-between text-emerald-400 font-semibold">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-tag text-xs"></i>
                                    <span>Potongan Diskon:</span>
                                </span>
                                <span class="payment-mono font-bold">– Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if(($booking->transport_fee ?? 0) > 0)
                            <div class="flex justify-between text-[#ffd2e1] font-medium">
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-motorcycle text-xs text-[#f59e0b]"></i>
                                    <span>Ongkir Transport:</span>
                                </span>
                                <span class="payment-mono font-bold text-white">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        @if($booking->payment_type === 'cash')
                            <div class="p-3.5 rounded-2xl bg-[#451a03]/60 border border-[#f59e0b]/60 space-y-1.5 my-2">
                                <div class="flex justify-between text-amber-200 font-bold">
                                    <span>Tagihan DP 35% (Sekarang via QRIS):</span>
                                    <span class="payment-mono text-[#f59e0b]">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-amber-300/80 font-semibold text-xs">
                                    <span>Sisa Pelunasan Tunai (65% di Salon):</span>
                                    <span class="payment-mono">Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- Total Due Amount Container (Ultra High Contrast with Root Colors) --}}
                        <div class="pt-3 border-t border-[#331c23] p-4 rounded-2xl bg-gradient-to-r from-[#380e22] to-[#500a1d] border-2 border-[#f59e0b]/50 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-[#ffd2e1] block">
                                    {{ $booking->payment_type === 'cash' ? 'Total Tagihan QRIS (DP)' : 'Total Pembayaran Lunas' }}
                                </span>
                                <span class="text-xs text-emerald-400 font-bold flex items-center gap-1 mt-0.5">
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                    <span>Bebas Biaya Admin</span>
                                </span>
                            </div>
                            <span class="payment-serif text-2xl font-black text-[#f59e0b] drop-shadow-sm">
                                Rp {{ number_format($booking->payment_type === 'cash' ? $booking->dp_amount : $booking->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                </div>

                {{-- Trust & Safety Badges --}}
                <div class="bg-[#1a0f13] rounded-3xl p-4.5 border-2 border-[#594043] shadow-xl space-y-2.5">
                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-emerald-950/40 border border-emerald-500/40">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 shadow-md">
                            <i class="fa-solid fa-shield-halved text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs text-white">Pembayaran Terenkripsi & Aman</h4>
                            <p class="text-xs text-emerald-200/80">Diproses secara otomatis dan terverifikasi secara instan.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 p-3 rounded-2xl bg-purple-950/40 border border-purple-500/40">
                        <div class="w-8 h-8 rounded-xl bg-purple-500 text-white flex items-center justify-center shrink-0 shadow-md">
                            <i class="fa-solid fa-hand-holding-dollar text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-bold text-xs text-white">Garansi 100% Refund</h4>
                            <p class="text-xs text-purple-200/80">Pengembalian dana penuh jika salon membatalkan jadwal Anda.</p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- ══ MODAL PEMBAYARAN BERHASIL ══ --}}
    <div x-show="showSuccessModal" x-cloak
         class="fixed inset-0 z-[700] flex items-center justify-center p-4 bg-black/80 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-[#1a0f13] rounded-3xl p-6 sm:p-8 max-w-sm w-full text-center shadow-2xl border-2 border-[#b01f44] relative overflow-hidden space-y-4 text-white">
            
            <div class="w-16 h-16 rounded-2xl bg-emerald-500 text-white flex items-center justify-center mx-auto text-3xl shadow-xl border border-emerald-300">
                <i class="fa-solid fa-circle-check"></i>
            </div>

            <div>
                <h3 class="payment-serif text-2xl font-bold text-white">Pembayaran Berhasil!</h3>
                <p class="text-xs text-[#ffd2e1]/80 mt-1">Reservasi Anda telah terkonfirmasi dan siap dilayani.</p>
            </div>

            <div class="bg-[#331c23] border border-[#594043] rounded-2xl p-4 text-center space-y-1 shadow-inner">
                <span class="text-xs font-extrabold text-[#ffd2e1] uppercase tracking-widest block">Poin Loyalty Diperoleh</span>
                <div class="text-2xl font-black text-[#f59e0b] payment-mono" x-text="`+${earnedPoints} PTS`">
                    +0 PTS
                </div>
                <p class="text-xs text-[#ffd2e1]/80">
                    Total Poin Anda: <strong class="text-white" x-text="`${userTotalPoints} PTS`"></strong>
                </p>
            </div>

            <button type="button" @click="goToReceipt()"
                    class="w-full py-3.5 bg-[#b01f44] hover:bg-[#8f1735] text-white font-bold rounded-full shadow-lg shadow-[#b01f44]/40 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95 text-xs">
                <span>Lihat Pass Reservasi</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
<script>
window.paymentPage = function paymentPage(config) {
    return {
        statusUrl: config.statusUrl,
        secondsRemaining: config.secondsRemaining || 900,
        redirectUrl: config.redirectUrl,
        isChecking: false,
        isExpired: false,
        timerInterval: null,
        pollInterval: null,
        toast: { show: false, message: '' },
        showSuccessModal: false,
        earnedPoints: 0,
        userTotalPoints: 0,
        targetRedirectUrl: '',

        init() {
            if (this.secondsRemaining <= 0) {
                this.isExpired = true;
            } else {
                this.startTimer();
            }

            this.startPolling();
        },

        copyBookingCode(code) {
            if (!code) return;
            navigator.clipboard.writeText(code);
            this.toast = { show: true, message: `Kode booking #${code} berhasil disalin!` };
            setTimeout(() => { this.toast.show = false; }, 2500);
        },

        get formattedTimer() {
            if (this.secondsRemaining <= 0) return '00:00';
            const totalSec = Math.max(0, Math.floor(this.secondsRemaining));
            const h = Math.floor(totalSec / 3600);
            const m = Math.floor((totalSec % 3600) / 60);
            const s = totalSec % 60;
            const pad = (n) => String(n).padStart(2, '0');
            if (h > 0) {
                return `${pad(h)}:${pad(m)}:${pad(s)}`;
            }
            return `${pad(m)}:${pad(s)}`;
        },

        startTimer() {
            this.timerInterval = setInterval(() => {
                if (this.secondsRemaining > 0) {
                    this.secondsRemaining--;
                } else {
                    this.secondsRemaining = 0;
                    this.isExpired = true;
                    clearInterval(this.timerInterval);
                }
            }, 1000);
        },

        startPolling() {
            this.pollInterval = setInterval(() => {
                if (!this.isExpired && !this.isChecking) {
                    this.checkStatusNow(true);
                }
            }, 5000);
        },

        async checkStatusNow(isAuto = false) {
            if (this.isChecking) return;
            this.isChecking = true;

            try {
                const res = await fetch(this.statusUrl, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.expired) {
                    this.isExpired = true;
                    this.secondsRemaining = 0;
                    clearInterval(this.timerInterval);
                    clearInterval(this.pollInterval);
                }

                if (data.payment_status === 'paid' || data.status === 'confirmed' || data.status === 'completed') {
                    clearInterval(this.timerInterval);
                    clearInterval(this.pollInterval);

                    this.earnedPoints = data.earned_points || 0;
                    this.userTotalPoints = data.user_total_points || 0;
                    this.targetRedirectUrl = data.redirect_url || this.redirectUrl;
                    this.showSuccessModal = true;

                    if (typeof confetti === 'function') {
                        confetti({
                            particleCount: 120,
                            spread: 80,
                            origin: { y: 0.6 }
                        });
                    }
                } else if (!isAuto) {
                    this.toast = { show: true, message: 'Belum terdeteksi pembayaran. Silakan selesaikan scan QRIS.' };
                    setTimeout(() => { this.toast.show = false; }, 3000);
                }
            } catch (e) {
                console.error('Polling error:', e);
            } finally {
                this.isChecking = false;
            }
        },

        goToReceipt() {
            window.location.href = this.targetRedirectUrl || this.redirectUrl;
        }
    };
};
</script>
@endpush
