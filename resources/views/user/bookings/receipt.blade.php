@extends('layouts.app')

@section('title', 'Struk Bukti Pembayaran - Yalia Beauty')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
<style>
    .font-display { font-family: 'Playfair Display', serif; }
    .font-body { font-family: 'Work Sans', sans-serif; }
    .font-mono-code { font-family: 'Space Mono', monospace; }
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .receipt-card { shadow: none !important; border: none !important; }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#fff0f3] via-[#fff5f7] to-[#ffe4e8] font-body py-28 px-4 flex items-center justify-center relative overflow-hidden">

    {{-- Background Soft Pink Decorative Elements --}}
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-[#f45472]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-[#f45472]/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full mx-auto relative z-10">

        {{-- Top Success Status Bar --}}
        <div class="no-print bg-emerald-600 text-white rounded-t-3xl p-4 text-center shadow-lg flex items-center justify-center gap-2 text-xs font-bold uppercase tracking-wider">
            <svg class="w-5 h-5 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Pembayaran Lunas & Reservasi Dikonfirmasi</span>
        </div>

        {{-- Printable Struk Card Container --}}
        <div class="receipt-card bg-white rounded-b-3xl shadow-[0_20px_50px_rgba(244,84,114,0.12)] p-6 md:p-8 border border-rose-100/80 relative">

            {{-- Brand Header --}}
            <div class="text-center pb-5 border-b border-dashed border-rose-200">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#f45472] to-[#ff8fa4] p-[2px] mx-auto mb-2 shadow-md">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty Logo" class="w-full h-full object-cover rounded-full bg-white">
                </div>
                <h1 class="font-display text-2xl font-black text-gray-900 tracking-tight">YALIA BEAUTY</h1>
                <p class="text-[11px] text-gray-500 font-medium">Salon & Luxury Treatment Center</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Jl. Beauty Salon No. 88 | WA: 0812-3456-7890</p>
                
                <div class="mt-3 inline-block bg-rose-50 border border-rose-200 px-3 py-1 rounded-full text-xs font-mono-code font-bold text-[#f45472]">
                    Kode Booking: #{{ $booking->booking_code }}
                </div>
            </div>

            {{-- Metadata Info --}}
            <div class="py-4 border-b border-dashed border-rose-200 text-xs space-y-2 font-mono-code">
                <div class="flex justify-between text-gray-600">
                    <span class="font-sans text-gray-500">Pelanggan:</span>
                    <span class="font-bold text-gray-900 font-sans">{{ $booking->user?->name ?? 'Guest Customer' }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span class="font-sans text-gray-500">Tipe Kunjungan:</span>
                    <span class="font-bold text-rose-600 uppercase font-sans">{{ $booking->booking_type === 'home' ? 'Home Service' : 'Ke Salon' }}</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span class="font-sans text-gray-500">Jadwal Perawatan:</span>
                    <span class="font-bold text-gray-900 font-sans">{{ $booking->booking_date ? $booking->booking_date->format('d M Y') : '-' }} ({{ $booking->time_start }})</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span class="font-sans text-gray-500">Beautician:</span>
                    <span class="font-bold text-gray-900 font-sans">{{ $booking->beautician?->name ?? 'Staff Salon' }}</span>
                </div>
            </div>

            {{-- Treatment Itemized Table --}}
            <div class="py-4 border-b border-dashed border-rose-200">
                <div class="flex justify-between text-gray-400 font-bold uppercase border-b border-gray-100 text-[10px] pb-2 mb-2 font-mono-code">
                    <span>Layanan Treatment</span>
                    <span>Subtotal</span>
                </div>
                <div class="space-y-2 text-xs">
                    @forelse($booking->bookingTreatments as $item)
                        <div class="flex justify-between text-gray-800">
                            <span class="font-bold text-gray-900">{{ $item->Treatments?->name }} <span class="text-xs font-normal text-gray-500">x{{ $item->quantity }}</span></span>
                            <span class="font-bold font-mono-code">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        @foreach($booking->treatments as $tr)
                            <div class="flex justify-between text-gray-800">
                                <span class="font-bold text-gray-900">{{ $tr->name }} <span class="text-xs font-normal text-gray-500">x1</span></span>
                                <span class="font-bold font-mono-code">Rp {{ number_format($tr->price, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    @endforelse
                </div>
            </div>

            {{-- Summary Totals --}}
            <div class="py-4 border-b border-dashed border-rose-200 text-xs space-y-1.5 font-mono-code">
                <div class="flex justify-between text-gray-600 font-sans">
                    <span>Subtotal Layanan:</span>
                    <span class="font-semibold text-gray-900">Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}</span>
                </div>
                @if(($booking->discount_amount ?? 0) > 0)
                <div class="flex justify-between text-emerald-600 font-sans">
                    <span>Diskon Voucher:</span>
                    <span class="font-semibold">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                @if(($booking->transport_fee ?? 0) > 0)
                <div class="flex justify-between text-gray-600 font-sans">
                    <span>Ongkir Home Service:</span>
                    <span class="font-semibold">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-sm font-black text-gray-900 pt-2 border-t border-gray-100 font-sans">
                    <span>TOTAL LUNAS</span>
                    <span class="text-[#f45472] font-display font-extrabold text-lg">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Payment Verified Status --}}
            <div class="py-4 text-center">
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    PEMBAYARAN DITERIMA VIA {{ strtoupper($booking->payment_method ?? 'QRIS / Midtrans') }}
                </span>
            </div>

            {{-- Greeting Footer --}}
            <div class="pt-3 text-center border-t border-dashed border-rose-200">
                <p class="text-xs font-display font-bold text-gray-900">Terima Kasih Atas Kepercayaan Anda! 🌸</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Tunjukkan struk ini saat kedatangan di salon.</p>
            </div>

            {{-- Printable Action Buttons --}}
            <div class="no-print mt-6 space-y-2.5">
                <button onclick="window.print()" 
                        class="w-full py-3.5 px-6 rounded-full bg-[#f45472] text-white font-bold text-xs shadow-md hover:bg-[#d93856] hover:shadow-lg transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak / Simpan Struk PDF
                </button>

                <a href="{{ route('user.bookings.index') }}" 
                   class="block text-center w-full py-3 px-6 rounded-full border border-rose-200 text-gray-700 font-bold text-xs hover:bg-rose-50 transition-all">
                    Kembali ke Riwayat Booking Saya
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
