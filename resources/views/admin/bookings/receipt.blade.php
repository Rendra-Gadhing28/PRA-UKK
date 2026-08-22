<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran #{{ $booking->booking_code }} — Yalia Beauty</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Work Sans', sans-serif; }
        .font-headline { font-family: 'Playfair Display', serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .receipt-box { border: none !important; shadow: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen py-8 flex flex-col items-center justify-center">

    {{-- Action Print Bar --}}
    <div class="no-print mb-6 flex items-center gap-4">
        <button onclick="window.print()" class="px-6 py-2.5 rounded-full bg-[#f45472] text-white font-bold text-sm shadow-md hover:bg-[#d93856] transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Struk Now
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 rounded-full bg-white text-gray-700 font-semibold text-sm border border-gray-200 hover:bg-gray-50 transition-all">
            Tutup Halaman
        </button>
    </div>

    {{-- Printable Receipt Container --}}
    <div class="receipt-box w-full max-w-md bg-white p-8 rounded-3xl shadow-xl border border-rose-100 text-gray-800">
        
        {{-- Header Logo & Salon Info --}}
        <div class="text-center pb-6 border-b border-dashed border-gray-200">
            <div class="w-14 h-14 mx-auto rounded-full bg-gradient-to-br from-[#f45472] to-[#ff8fa4] p-[2px] flex items-center justify-center shadow-md mb-2">
                <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty" class="w-full h-full object-cover rounded-full bg-white">
            </div>
            <h1 class="font-headline font-bold text-2xl text-gray-900 tracking-tight">YALIA BEAUTY</h1>
            <p class="text-[11px] text-gray-500 font-medium">Salon & Luxury Treatment Center</p>
            <p class="text-[10px] text-gray-400 mt-1">GHV9+F2 Candi, Kabupaten Boyolali, Jawa Tengah | WA: 0822-2702-3362</p>
        </div>

        {{-- Transaction Metadata --}}
        <div class="py-4 border-b border-dashed border-gray-200 text-xs space-y-1.5">
            <div class="flex justify-between">
                <span class="text-gray-500">No. Struk / Booking:</span>
                <span class="font-mono font-bold text-gray-900">#{{ $booking->booking_code }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal & Waktu:</span>
                <span class="font-medium text-gray-800">{{ $booking->booking_date ? $booking->booking_date->format('d/m/Y') : '-' }} {{ $booking->time_start ?? '' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Pelanggan:</span>
                <span class="font-bold text-gray-900">{{ $booking->user?->name ?? 'Guest' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Beautician:</span>
                <span class="font-semibold text-gray-800">{{ $booking->beautician?->name ?? 'Staff Salon' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tipe Kunjungan:</span>
                <span class="font-bold uppercase text-[#f45472]">{{ $booking->booking_type ?? 'Ke Salon' }}</span>
            </div>
        </div>

        {{-- Treatment Rincian Table --}}
        <div class="py-4 border-b border-dashed border-gray-200">
            <table class="w-full text-xs">
                <thead>
                    <tr class="text-gray-400 font-bold uppercase border-b border-gray-100 text-[10px]">
                        <th class="text-left pb-2">Treatment</th>
                        <th class="text-center pb-2">Qty</th>
                        <th class="text-right pb-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($booking->bookingTreatments as $item)
                    <tr>
                        <td class="py-2 font-bold text-gray-900">{{ $item->Treatments?->name }}</td>
                        <td class="py-2 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="py-2 text-right font-bold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    @foreach($booking->treatments as $tr)
                    <tr>
                        <td class="py-2 font-bold text-gray-900">{{ $tr->name }}</td>
                        <td class="py-2 text-center text-gray-600">1</td>
                        <td class="py-2 text-right font-bold text-gray-900">Rp {{ number_format($tr->price, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Financial Summary --}}
        <div class="py-4 border-b border-dashed border-gray-200 text-xs space-y-1.5">
            <div class="flex justify-between text-gray-600">
                <span>Subtotal:</span>
                <span class="font-semibold text-gray-900">Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}</span>
            </div>
            @if(($booking->discount_amount ?? 0) > 0)
            <div class="flex justify-between text-emerald-600">
                <span>Diskon Voucher:</span>
                <span class="font-semibold">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            @if(($booking->transport_fee ?? 0) > 0)
            <div class="flex justify-between text-gray-600">
                <span>Ongkir Home Service:</span>
                <span class="font-semibold">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm font-black text-gray-900 pt-2 border-t border-gray-100">
                <span>TOTAL AKHIR:</span>
                <span class="text-[#f45472]">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Payment Status Info --}}
        <div class="py-4 text-center text-xs space-y-1">
            <p class="text-gray-500">Metode Pembayaran: <strong class="uppercase text-gray-800">{{ $booking->payment_method ?? 'QRIS / Transfer' }}</strong></p>
            <p class="font-bold text-emerald-600 uppercase tracking-wider text-xs">
                Status: {{ $booking->payment_status === 'paid' ? 'LUNAS (PAID)' : 'BELUM DIVERIFIKASI' }}
            </p>
        </div>

        {{-- Footer Greeting --}}
        <div class="pt-4 text-center border-t border-dashed border-gray-200">
            <p class="text-xs font-headline font-bold text-gray-900">Terima Kasih Atas Kunjungan Anda! 🌸</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Glow Up Your Beauty with Yalia Beauty</p>
        </div>

    </div>

</body>
</html>
