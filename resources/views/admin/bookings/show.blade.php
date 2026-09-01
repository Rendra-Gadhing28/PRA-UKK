<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.bookings.index') }}" class="p-2.5 rounded-full bg-white border border-rose-200 text-rose-950 hover:text-rose-600 hover:bg-rose-50 transition-all shadow-2xs">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                        Reservasi #{{ $booking->booking_code }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">Dibuat pada {{ $booking->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.bookings.receipt', $booking->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold shadow-xs transition-all">
                    <i class="fa-solid fa-receipt text-xs"></i>
                    <span>Cetak Struk Pembayaran</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- TOP INFO CARDS GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Customer Info Card --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-600 flex items-center gap-1.5">
                                <i class="fa-solid fa-user text-xs"></i>
                                Informasi Pemesan
                            </span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100">
                                Customer
                            </span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-[#f45472] font-bold flex items-center justify-center text-sm shrink-0 border border-rose-200">
                                    {{ strtoupper(substr($booking->user->name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900 leading-tight">{{ $booking->user->name ?? 'Guest' }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $booking->user->email ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="pt-3 border-t border-rose-50 space-y-2 text-xs text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-phone text-rose-400 w-4"></i>
                                    <span class="font-medium">{{ $booking->user->phone ?? 'Tidak ada No HP' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <i class="fa-solid fa-location-dot text-rose-400 w-4 mt-0.5"></i>
                                    <span class="leading-snug font-medium">{{ $booking->address ?: 'Perawatan di Studio Salon' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Beautician Info & Assignment Card --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-600 flex items-center gap-1.5">
                                <i class="fa-solid fa-user-sparkles text-xs"></i>
                                Beautician Terapis
                            </span>
                            @if($booking->beautician)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-600 border border-purple-100">
                                    Ditugaskan
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                    Belum Ditugaskan
                                </span>
                            @endif
                        </div>

                        @if($booking->beautician)
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ $booking->beautician->photo_url ?? '' }}" alt="{{ $booking->beautician->name }}" class="w-12 h-12 rounded-2xl object-cover border border-rose-100">
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900">{{ $booking->beautician->name }}</h4>
                                    <p class="text-xs text-purple-600 font-medium mt-0.5">{{ $booking->beautician->specialization ?? 'Beautician' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200/80 text-xs text-amber-900 mb-3 flex items-start gap-2">
                                <i class="fa-solid fa-circle-info text-amber-600 text-sm shrink-0 mt-0.5"></i>
                                <span>Belum ada beautician yang ditugaskan untuk reservasi ini.</span>
                            </div>
                        @endif
                    </div>

                    {{-- Form Change Beautician --}}
                    <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="mt-2 pt-3 border-t border-rose-50">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $booking->status }}">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5 flex items-center gap-1">
                            <i class="fa-solid fa-user-gear text-rose-500 text-xs"></i>
                            Tugaskan / Ganti Beautician
                        </label>
                        <div class="flex gap-2">
                            <select name="beautician_id" class="flex-1 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] py-2 px-3">
                                <option value="">-- Pilih Beautician --</option>
                                @foreach($beauticians as $b)
                                    @php
                                        $bId = is_object($b) ? $b->id : (is_array($b) ? ($b['id'] ?? '') : $b);
                                        $bName = is_object($b) ? $b->name : (is_array($b) ? ($b['name'] ?? '') : $b);
                                    @endphp
                                    <option value="{{ $bId }}" {{ $booking->beautician_id == $bId ? 'selected' : '' }}>{{ $bName }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-[#f45472] text-white text-xs font-bold rounded-xl hover:bg-[#d93856] transition-colors shadow-xs">Simpan</button>
                        </div>
                    </form>
                </div>

                {{-- Status & Quick Action Card --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600 flex items-center gap-1.5">
                                <i class="fa-solid fa-clock text-xs"></i>
                                Status & Jadwal
                            </span>
                            @php
                                $statusBg = match($booking->status) {
                                    'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'in_progress' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    'canceled' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                                };
                            @endphp
                            @php $stVal = is_object($booking->status) ? $booking->status->value : (string)$booking->status; @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $statusBg }}">
                                {{ is_object($booking->status) && method_exists($booking->status, 'badgeLabel') ? $booking->status->badgeLabel() : ucfirst($stVal) }}
                            </span>
                        </div>

                        <div class="space-y-2 text-xs mb-4">
                            <div class="flex justify-between py-1 border-b border-rose-50">
                                <span class="text-gray-500">Tanggal Booking:</span>
                                <span class="font-bold text-gray-900">{{ $booking->booking_date ? $booking->booking_date->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-rose-50">
                                <span class="text-gray-500">Jam Layanan:</span>
                                <span class="font-bold text-gray-900">{{ $booking->time_start ?? '' }} - {{ $booking->time_end ?? '' }}</span>
                            </div>
                            @if($stVal === 'canceled' || $stVal === 'cancelled')
                                <div class="py-1 text-rose-600 font-semibold">
                                    <span>Alasan Batal:</span> {{ $booking->cancel_reason ?: 'Tidak diisi' }}
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!in_array($stVal, ['completed', 'canceled', 'cancelled'], true))
                        {{-- Form Change Status --}}
                        <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="mt-2 pt-3 border-t border-rose-50" x-data="{ selectedStatus: '{{ $stVal }}' }">
                            @csrf
                            @method('PATCH')
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5 flex items-center gap-1">
                                <i class="fa-solid fa-list-check text-rose-500 text-xs"></i>
                                Ubah Status Reservasi
                            </label>
                            <div class="space-y-2">
                                <select name="status" x-model="selectedStatus" class="w-full text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] py-2 px-3">
                                    <option value="pending" {{ $stVal === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                                    <option value="confirmed" {{ $stVal === 'confirmed' ? 'selected' : '' }}>Confirmed (Terkonfirmasi)</option>
                                    <option value="in_progress" {{ $stVal === 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Berlangsung)</option>
                                    <option value="completed">Completed (Selesai)</option>
                                    <option value="canceled">Canceled (Batalkan)</option>
                                </select>

                                <div x-show="selectedStatus === 'canceled'" x-cloak class="mt-2">
                                    <input type="text" name="cancel_reason" placeholder="Alasan pembatalan..." class="w-full text-xs rounded-xl border-rose-200 py-2 px-3">
                                </div>

                                <button type="submit" class="w-full py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-all shadow-xs flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-arrows-rotate text-xs"></i>
                                    <span>Update Status Reservasi</span>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="mt-3 pt-3 border-t border-rose-50 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 border border-gray-200">
                                <i class="fa-solid fa-lock text-xs"></i>
                                Status {{ $stVal === 'completed' ? 'Selesai' : 'Dibatalkan' }} (tidak dapat diubah)
                            </span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- ITEMIZED TREATMENTS TABLE --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4 font-headline flex items-center gap-2">
                    <i class="fa-solid fa-spa text-rose-500 text-base"></i>
                    Rincian Treatment & Biaya
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-rose-950 border-b border-rose-100">
                                <th class="py-3 px-4">Treatment</th>
                                <th class="py-3 px-4">Durasi</th>
                                <th class="py-3 px-4 text-right">Harga Satuan</th>
                                <th class="py-3 px-4 text-center">Jumlah</th>
                                <th class="py-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50 text-sm">
                            @forelse($booking->bookingTreatments as $item)
                            <tr>
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $item->Treatments?->name ?? 'Treatment Item' }}
                                </td>
                                <td class="py-3.5 px-4 text-xs text-gray-500">
                                    <i class="fa-regular fa-clock text-gray-400 text-xs mr-1"></i>
                                    {{ $item->Treatments?->duration_minutes ?? 60 }} Menit
                                </td>
                                <td class="py-3.5 px-4 text-right text-gray-700 tabular-nums">
                                    Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-gray-900 tabular-nums">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            @foreach($booking->treatments as $tr)
                            <tr>
                                <td class="py-3.5 px-4 font-bold text-gray-900">{{ $tr->name }}</td>
                                <td class="py-3.5 px-4 text-xs text-gray-500"><i class="fa-regular fa-clock text-gray-400 text-xs mr-1"></i>{{ $tr->duration_minutes }} Menit</td>
                                <td class="py-3.5 px-4 text-right text-gray-700 tabular-nums">Rp {{ number_format($tr->price, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-gray-900">1</td>
                                <td class="py-3.5 px-4 text-right font-bold text-gray-900 tabular-nums">Rp {{ number_format($tr->price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            @endforelse
                        </tbody>
                        <tfoot class="border-t-2 border-rose-100 text-sm">
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-right font-semibold text-gray-600">Subtotal:</td>
                                <td class="py-2 px-4 text-right font-bold text-gray-900 tabular-nums">Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            @if(($booking->discount_amount ?? 0) > 0)
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-right font-semibold text-emerald-600">Diskon Voucher:</td>
                                <td class="py-2 px-4 text-right font-bold text-emerald-600 tabular-nums">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if(($booking->transport_fee ?? 0) > 0)
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-right font-semibold text-gray-600">Ongkos Transport (Home Service):</td>
                                <td class="py-2 px-4 text-right font-bold text-gray-900 tabular-nums">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr class="bg-rose-50/70 text-base">
                                <td colspan="4" class="py-3 px-4 text-right font-black text-gray-900">Total Akhir:</td>
                                <td class="py-3 px-4 text-right font-black text-[#f45472] tabular-nums">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- PAYMENT VERIFICATION CARD --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 font-headline flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-rose-500 text-base"></i>
                            Status Pembayaran & Verifikasi
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">Skema: <span class="font-bold uppercase text-[#f45472]">{{ $booking->payment_type === 'cash' ? 'Cash di Salon (DP 35%)' : 'Cashless (Full 100%)' }}</span> &middot; Metode: <span class="font-bold uppercase text-gray-800">{{ $booking->payment_method ?? 'QRIS' }}</span></p>
                        @if($booking->payment_type === 'cash')
                            <div class="mt-2 text-xs space-y-0.5 bg-rose-50/50 p-3 rounded-2xl border border-rose-100">
                                <p class="text-emerald-700 font-semibold">DP 35% Terbayar: <strong>Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</strong></p>
                                <p class="text-amber-800 font-bold">Sisa Wajib Pelunasan Cash: <strong>Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</strong></p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @if($booking->payment_status === 'paid')
                            <span class="px-4 py-2 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 font-bold text-xs flex items-center gap-1.5 shadow-2xs">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                                <span>Terverifikasi Lunas</span>
                            </span>
                        @elseif($booking->payment_status === 'dp_paid')
                            <form method="POST" action="{{ route('admin.bookings.verify-payment', $booking->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-5 py-2.5 rounded-full bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 shadow-md transition-all flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                    <span>Tandai Lunas (Pelunasan Cash Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }})</span>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.bookings.verify-payment', $booking->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-5 py-2.5 rounded-full bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 shadow-md transition-all flex items-center gap-1.5">
                                    <i class="fa-solid fa-circle-check text-xs"></i>
                                    <span>Verifikasi Pembayaran Sekarang</span>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($booking->payment_proof)
                    <div class="mt-4 pt-4 border-t border-rose-50">
                        <p class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-image text-rose-500 text-xs"></i>
                            Pratinjau Bukti Transfer Pelanggan:
                        </p>
                        <a href="{{ \App\Support\ImageHelper::url($booking->payment_proof) }}" target="_blank" class="inline-block border-2 border-rose-100 rounded-2xl overflow-hidden hover:opacity-90 transition-opacity shadow-2xs">
                            <img src="{{ \App\Support\ImageHelper::url($booking->payment_proof) }}" alt="Bukti Pembayaran" class="max-h-64 object-cover">
                        </a>
                    </div>
                @endif
            </div>

            {{-- CUSTOMER REVIEW SECTION --}}
            @if($booking->review)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 mb-8">
                <div class="flex items-center justify-between mb-4 border-b border-rose-100 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 font-headline flex items-center gap-2">
                            <i class="fa-solid fa-star text-amber-400 text-base"></i>
                            Ulasan Pelanggan
                        </h3>
                        <p class="text-xs text-gray-500 mt-0.5">Diberikan pada {{ $booking->review->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-1 text-amber-500 bg-amber-50 px-3.5 py-1.5 rounded-full border border-amber-200/80">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $booking->review->rating)
                                <i class="fa-solid fa-star text-amber-400 text-xs"></i>
                            @else
                                <i class="fa-regular fa-star text-amber-200 text-xs"></i>
                            @endif
                        @endfor
                        <span class="ml-1 text-xs font-bold text-amber-800">{{ $booking->review->rating }}/5</span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <p class="text-sm text-gray-800 font-medium italic mb-4">
                            "{{ $booking->review->comment ?? 'Pelanggan tidak memberikan komentar.' }}"
                        </p>

                        @if($booking->review->admin_reply)
                            <div class="bg-rose-50/50 border border-rose-100 rounded-2xl p-4 mt-4">
                                <span class="text-xs font-bold text-[#f45472] uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                    <i class="fa-solid fa-reply text-xs"></i>
                                    Balasan Admin:
                                </span>
                                <p class="text-xs text-gray-700 leading-relaxed font-medium">{{ $booking->review->admin_reply }}</p>
                            </div>
                        @else
                            <div class="bg-purple-50/60 border border-purple-100 rounded-2xl p-4 mt-4">
                                <span class="text-xs font-bold text-purple-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                    <i class="fa-solid fa-reply text-xs"></i>
                                    Balas Ulasan Ini
                                </span>
                                <form action="{{ route('admin.bookings.reply-review', $booking->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <textarea name="admin_reply" rows="2" class="w-full text-xs rounded-xl border-purple-200 focus:border-purple-400 focus:ring-purple-400 p-2.5 mb-2.5 font-medium" placeholder="Tuliskan balasan terima kasih atau tanggapan resmi..."></textarea>
                                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-colors shadow-xs flex items-center gap-1.5">
                                        <i class="fa-solid fa-paper-plane text-xs"></i>
                                        <span>Kirim Balasan</span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    @if($booking->review->photo)
                    <div class="w-full md:w-1/3">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-image text-xs"></i>
                            Foto Hasil:
                        </p>
                        <a href="{{ \App\Support\ImageHelper::url($booking->review->photo) }}" target="_blank" class="block rounded-2xl overflow-hidden border border-rose-100 hover:opacity-90 transition-opacity shadow-2xs">
                            <img src="{{ \App\Support\ImageHelper::url($booking->review->photo) }}" alt="Review Photo" class="w-full h-40 object-cover">
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</x-admin-layout>
