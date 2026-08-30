@extends('layouts.app')

@section('title', 'Pembayaran QRIS Struk - Yalia Beauty')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    .font-display { font-family: 'Playfair Display', serif; }
    .font-body { font-family: 'Work Sans', sans-serif; }
    .font-mono-code { font-family: ui-monospace, monospace; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    /* Receipt perforated border effect */
    .receipt-edge-top {
        background-image: radial-gradient(circle at 10px -5px, transparent 12px, #ffffff 13px);
        background-size: 20px 20px;
        background-repeat: repeat-x;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-transparent font-body py-28 px-4 relative flex items-center justify-center overflow-hidden"
     x-data="paymentPage({
        statusUrl: '{{ route('user.bookings.payment.status', $booking) }}',
        secondsRemaining: {{ (int) max(0, now()->diffInSeconds($booking->payment_expires_at, false)) }},
        redirectUrl: '{{ route('user.bookings.show', $booking) }}'
     })"
     x-init="init()">

    {{-- Background Ambient Blur Glows --}}
    <div class="absolute top-1/4 left-1/2 -translate-x-1/2 w-[500px] h-[500px] bg-[#f45472]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#f45472]/15 rounded-full blur-3xl pointer-events-none"></div>


    {{-- Notification Toast --}}
    <div x-show="toast.show" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-5"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-5"
         class="fixed top-24 left-1/2 -translate-x-1/2 z-[600] bg-emerald-600 text-white px-6 py-3.5 rounded-full shadow-2xl font-semibold text-sm flex items-center gap-2.5 border border-emerald-400">
        <i class="fa-solid fa-circle-check text-base text-white"></i>
        <span x-text="toast.message"></span>
    </div>

    {{-- Floating Receipt Modal Container --}}
    <div class="max-w-md w-full mx-auto relative z-10 my-auto">
        
        {{-- Floating Timer Card Header --}}
        <div class="bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white rounded-t-3xl p-5 text-center shadow-xl border-b border-white/10 relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            
            <p class="text-xs uppercase tracking-widest font-bold text-white/90 mb-1 flex items-center justify-center gap-1.5">
                <i class="fa-solid fa-stopwatch text-xs"></i>
                <span>Waktu Pembayaran QRIS</span>
            </p>
            <div class="font-mono-code text-4xl font-extrabold tracking-wider my-1 drop-shadow-sm" x-text="formattedTimer">
                15:00
            </div>
            <p class="text-xs text-white/90 font-medium">
                Selesaikan pembayaran dalam <span class="font-bold">15 menit</span> sebelum booking otomatis dibatalkan.
            </p>
        </div>

        {{-- Floating Struk Body --}}
        <div class="bg-white rounded-b-3xl shadow-[0_25px_60px_rgba(0,0,0,0.5)] p-6 md:p-8 border border-rose-100 relative">
            
            {{-- Struk Brand Header --}}
            <div class="text-center pb-5 border-b border-dashed border-gray-200">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] p-0.5 mx-auto mb-2 shadow-md">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Logo" width="48" height="48" style="width: 48px; height: 48px;" decoding="async" class="w-full h-full object-cover rounded-full bg-white">
                </div>
                <h1 class="font-display text-xl font-extrabold text-[#5b3a29]">YALIA BEAUTY SALON</h1>
                <p class="text-xs text-gray-500 uppercase tracking-widest font-semibold mt-0.5">Struk Reservasi & Tagihan Pembayaran</p>
                
                {{-- Copyable Booking Code Button --}}
                <button type="button" @click="copyBookingCode('{{ $booking->booking_code }}')"
                        class="mt-3 inline-flex items-center gap-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 px-3 py-1 rounded-full text-xs font-mono-code font-bold text-[#f45472] transition-colors cursor-pointer shadow-2xs group"
                        title="Klik untuk menyalin kode booking">
                    <span>Kode: {{ $booking->booking_code }}</span>
                    <i class="fa-solid fa-copy text-xs group-hover:scale-110 transition-transform"></i>
                </button>
            </div>

            {{-- Status Polling Badge --}}
            <div class="my-4 flex items-center justify-between bg-gray-50 rounded-2xl p-3 border border-gray-100 text-xs">
                <span class="text-gray-600 font-medium flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Status Polling:</span>
                </span>
                <span class="font-bold uppercase tracking-wider text-amber-600 flex items-center gap-1">
                    <span x-show="isChecking" class="flex items-center gap-1">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                        <span>Mengecek...</span>
                    </span>
                    <span x-show="!isChecking">Menunggu Transfer QRIS</span>
                </span>
            </div>

            {{-- QRIS Section --}}
            <div class="bg-gradient-to-b from-[#fff8f9] to-[#fff0f3] rounded-2xl p-5 text-center border border-rose-100 mb-6">
                <p class="text-xs font-bold text-[#5b3a29] uppercase tracking-wider mb-2">Scan QRIS Di Bawah Ini</p>
                
                <div class="bg-white p-3 rounded-2xl inline-block shadow-md border border-rose-200/80 mb-3">
                    @if($booking->qris_image_url)
                        <img src="{{ $booking->qris_image_url }}" alt="QRIS Code" width="192" height="192" loading="lazy" decoding="async" class="w-48 h-48 object-contain mx-auto rounded-lg">
                    @else
                        {{-- Fallback QR Code Image generator --}}
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($booking->qris_code ?? $booking->booking_code) }}" 
                             alt="QR Code Fallback" 
                             width="192"
                             height="192"
                             loading="lazy"
                             decoding="async"
                             class="w-48 h-48 object-contain mx-auto rounded-lg">
                    @endif
                </div>

                <p class="text-xs text-gray-600 leading-relaxed max-w-xs mx-auto">
                    Dapat di-scan dengan <strong>BCA, Mandiri, BRI, BNI, GoPay, OVO, ShopeePay, Dana, LinkAja</strong> dll.
                </p>
            </div>

            {{-- Struk Itemized Details --}}
            <div class="space-y-3 mb-6 font-mono-code text-xs">
                <div class="flex justify-between text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100 pb-2">
                    <span>Layanan</span>
                    <span>Subtotal</span>
                </div>

                @foreach($booking->treatments as $item)
                    <div class="flex justify-between text-gray-800 font-medium">
                        <span class="pr-2 font-sans font-semibold text-gray-900">{{ $item->name }} <span class="text-xs text-gray-500">x{{ $item->pivot->quantity }}</span></span>
                        <span class="shrink-0 font-bold">Rp {{ number_format($item->pivot->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach

                <div class="border-t border-dashed border-gray-200 pt-3 space-y-1.5">
                    <div class="flex justify-between text-gray-600 font-sans text-xs">
                        <span>Tipe Layanan:</span>
                        <span class="font-bold text-gray-800 uppercase">{{ $booking->booking_type === 'home' ? 'Home Service' : 'At Salon' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 font-sans text-xs">
                        <span>Skema Pembayaran:</span>
                        <span class="font-bold text-[#f45472] uppercase">{{ $booking->payment_type === 'cash' ? 'Cash (DP 35%)' : 'Cashless (Full 100%)' }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 font-sans text-xs">
                        <span>Jadwal:</span>
                        <span class="font-bold text-gray-800">{{ $booking->booking_date ? $booking->booking_date->format('d M Y') : '-' }} {{ $booking->time_start }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600 font-sans text-xs">
                        <span>Beautician:</span>
                        <span class="font-bold text-gray-800">{{ $booking->beautician?->name ?? 'Staff Salon' }}</span>
                    </div>
                    @if(($booking->transport_fee ?? 0) > 0)
                        <div class="flex justify-between text-gray-600 font-sans text-xs">
                            <span>Ongkir Transport:</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>

                @if($booking->payment_type === 'cash')
                    <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-3 my-2 space-y-1 font-sans text-xs">
                        <div class="flex justify-between text-gray-700">
                            <span>Total Seluruh Treatment:</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-amber-900 font-extrabold border-t border-amber-200/60 pt-1">
                            <span>Tagihan QRIS DP 35% (Sekarang):</span>
                            <span>Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-amber-800 font-semibold">
                            <span>Sisa Tunai di Salon (65%):</span>
                            <span>Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endif

                {{-- Total --}}
                <div class="border-t-2 border-gray-900 pt-3 flex justify-between items-center text-sm font-sans">
                    <span class="font-bold text-gray-900 uppercase">{{ $booking->payment_type === 'cash' ? 'Tagihan QRIS Sekarang (DP)' : 'Total Tagihan' }}</span>
                    <span class="font-display font-extrabold text-xl text-[#f45472]">Rp {{ number_format($booking->payment_type === 'cash' ? $booking->dp_amount : $booking->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Actions & Manual Check Button --}}
            <div class="space-y-3">
                <button type="button" 
                        @click="checkStatusNow()" 
                        :disabled="isChecking || isExpired"
                        class="w-full py-3.5 px-6 rounded-full bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-bold text-sm shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <i class="fa-solid fa-rotate text-xs" :class="isChecking ? 'fa-spin' : ''"></i>
                    <span x-show="!isChecking">Cek Pembayaran Sekarang</span>
                    <span x-show="isChecking">Mengecek Status...</span>
                </button>

                <a href="{{ route('user.bookings.index') }}" 
                   class="block text-center w-full py-3 px-6 rounded-full border border-gray-300 text-gray-700 font-semibold text-xs hover:bg-gray-50 transition">
                    Kembali ke Riwayat Booking
                </a>
            </div>

        </div>
    {{-- Payment Success & Points Reward Modal --}}
    <div x-show="showSuccessModal" x-cloak
         class="fixed inset-0 z-[700] flex items-center justify-center p-4 bg-black/60 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">
        <div class="bg-white rounded-3xl p-6 md:p-8 max-w-sm w-full text-center shadow-2xl border border-rose-100 relative overflow-hidden">
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg text-white" style="background: linear-gradient(135deg, #f45472, #b5294a); color: #ffffff !important;">
                <i class="fa-solid fa-circle-check text-4xl text-white"></i>
            </div>
            <h3 class="font-display text-2xl font-bold text-[#5b3a29] mb-1">Pembayaran Berhasil!</h3>
            <p class="text-sm text-gray-600 mb-5">Terima kasih telah melakukan reservasi di Yalia Beauty Salon.</p>

            <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 mb-6 text-center">
                <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider mb-1">Poin PTS Diperoleh</p>
                <div class="text-3xl font-extrabold text-[#f45472] font-mono-code" x-text="`+${earnedPoints} PTS`">
                    +0 PTS
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    Total Poin Kamu Sekarang: <span class="font-bold text-[#5b3a29]" x-text="`${userTotalPoints} PTS`"></span>
                </p>
            </div>

            <button @click="goToReceipt()"
                    class="w-full py-3.5 bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-bold rounded-xl shadow-lg hover:shadow-rose-300 hover:scale-[1.02] transition flex items-center justify-center gap-2">
                <span>Lihat Struk Reservasi</span>
                <i class="fa-solid fa-receipt text-xs"></i>
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
        secondsRemaining: config.secondsRemaining || 900, // 15 menit = 900 detik
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

            // Jalankan polling otomatis tiap 5 detik
            this.startPolling();
        },

        copyBookingCode(code) {
            if (!code) return;
            navigator.clipboard.writeText(code);
            this.toast = { show: true, message: `Kode booking ${code} berhasil disalin!` };
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
            // Polling otomatis setiap 5000 ms (5 detik)
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
                    this.toast = { show: true, message: 'Belum terdeteksi pembayaran. Silakan selesaikan via QRIS.' };
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
    }
}
</script>
@endpush
