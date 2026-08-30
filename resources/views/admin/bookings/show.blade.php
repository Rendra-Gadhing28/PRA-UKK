<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.bookings.index') }}" class="p-2 rounded-full bg-white border border-rose-200 text-rose-950 hover:text-rose-600 hover:bg-rose-50 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
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
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-sm font-semibold shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Struk Pembayaran
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- TOP INFO CARDS GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Customer Info Card --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Informasi Pemesan</span>
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100">
                                Customer
                            </span>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl bg-rose-100 text-[#f45472] font-bold flex items-center justify-center text-sm shrink-0">
                                    {{ strtoupper(substr($booking->user->name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900">{{ $booking->user->name ?? 'Guest' }}</h4>
                                    <p class="text-xs text-gray-500">{{ $booking->user->email ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-gray-50 space-y-1.5 text-xs text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-phone text-rose-400 w-4"></i>
                                    <span>{{ $booking->user->phone ?? 'Tidak ada No HP' }}</span>
                                </div>
                                <div class="flex items-start gap-2">
                                    <i class="fas fa-location-dot text-rose-400 w-4 mt-0.5"></i>
                                    <span class="leading-snug">{{ $booking->address ?: 'Perawatan di Studio Salon' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Beautician Info & Assignment Card --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-purple-600">Beautician Terapis</span>
                            @if($booking->beautician)
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-600 border border-purple-100">
                                    Ditugaskan
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-600 border border-amber-100">
                                    Belum Ditugaskan
                                </span>
                            @endif
                        </div>

                        @if($booking->beautician)
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ $booking->beautician->photo_url ?? '' }}" alt="{{ $booking->beautician->name }}" class="w-12 h-12 rounded-2xl object-cover border border-rose-100">
                                <div>
                                    <h4 class="font-bold text-sm text-gray-900">{{ $booking->beautician->name }}</h4>
                                    <p class="text-xs text-purple-600 font-medium">{{ $booking->beautician->specialization ?? 'Beautician' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="p-3 bg-amber-50/60 rounded-2xl border border-amber-100 text-xs text-amber-800 mb-3">
                                ℹ️ Belum ada beautician yang ditugaskan untuk reservasi ini.
                            </div>
                        @endif
                    </div>

                    {{-- Form Change Beautician --}}
                    <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="mt-2 pt-3 border-t border-gray-100">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $booking->status }}">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Tugaskan / Ganti Beautician</label>
                        <div class="flex gap-2">
                            <select name="beautician_id" class="flex-1 text-xs rounded-xl border-gray-200 focus:border-[#f45472]">
                                <option value="">-- Pilih Beautician --</option>
                                @foreach($beauticians as $b)
                                    <option value="{{ $b->id }}" {{ $booking->beautician_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-1.5 bg-[#f45472] text-white text-xs font-bold rounded-xl hover:bg-[#d93856]">Simpan</button>
                        </div>
                    </form>
                </div>

                {{-- Status & Quick Action Card --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Status & Jadwal</span>
                            @php
                                $statusBg = match($booking->status) {
                                    'completed' => 'bg-emerald-100 text-emerald-700',
                                    'confirmed' => 'bg-blue-100 text-blue-700',
                                    'in_progress' => 'bg-amber-100 text-amber-700',
                                    'canceled' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp
                            @php $stVal = is_object($booking->status) ? $booking->status->value : (string)$booking->status; @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusBg }}">
                                {{ is_object($booking->status) && method_exists($booking->status, 'badgeLabel') ? $booking->status->badgeLabel() : ucfirst($stVal) }}
                            </span>
                        </div>

                        <div class="space-y-2 text-xs mb-4">
                            <div class="flex justify-between py-1 border-b border-gray-50">
                                <span class="text-gray-500">Tanggal Booking:</span>
                                <span class="font-bold text-gray-900">{{ $booking->booking_date ? $booking->booking_date->format('d M Y') : '-' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-gray-50">
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
                        <form method="POST" action="{{ route('admin.bookings.update-status', $booking->id) }}" class="mt-2 pt-3 border-t border-gray-100" x-data="{ selectedStatus: '{{ $stVal }}' }">
                            @csrf
                            @method('PATCH')
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Ubah Status Reservasi</label>
                            <div class="space-y-2">
                                <select name="status" x-model="selectedStatus" class="w-full text-xs rounded-xl border-gray-200 focus:border-[#f45472]">
                                    <option value="pending" {{ $stVal === 'pending' ? 'selected' : '' }}>Pending (Menunggu)</option>
                                    <option value="confirmed" {{ $stVal === 'confirmed' ? 'selected' : '' }}>Confirmed (Terkonfirmasi)</option>
                                    <option value="in_progress" {{ $stVal === 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Berlangsung)</option>
                                    <option value="completed">Completed (Selesai)</option>
                                    <option value="canceled">Canceled (Batalkan)</option>
                                </select>

                                <div x-show="selectedStatus === 'canceled'" x-cloak class="mt-2">
                                    <input type="text" name="cancel_reason" placeholder="Alasan pembatalan..." class="w-full text-xs rounded-xl border-rose-200">
                                </div>

                                <button type="submit" class="w-full py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-all">
                                    Update Status Reservasi
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="mt-3 pt-3 border-t border-gray-100 text-center">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                <i class="fas fa-lock text-xs"></i>
                                Status {{ $stVal === 'completed' ? 'Selesai' : 'Dibatalkan' }} (tidak dapat diubah)
                            </span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- ITEMIZED TREATMENTS TABLE --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4 font-headline">Rincian Treatment & Biaya</h3>

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
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($booking->bookingTreatments as $item)
                            <tr>
                                <td class="py-3.5 px-4 font-bold text-gray-900">
                                    {{ $item->Treatments?->name ?? 'Treatment Item' }}
                                </td>
                                <td class="py-3.5 px-4 text-xs text-gray-500">
                                    {{ $item->Treatments?->duration_minutes ?? 60 }} Menit
                                </td>
                                <td class="py-3.5 px-4 text-right text-gray-700">
                                    Rp {{ number_format($item->price_per_unit, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-gray-900">
                                    {{ $item->quantity }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-gray-900">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            @foreach($booking->treatments as $tr)
                            <tr>
                                <td class="py-3.5 px-4 font-bold text-gray-900">{{ $tr->name }}</td>
                                <td class="py-3.5 px-4 text-xs text-gray-500">{{ $tr->duration_minutes }} Menit</td>
                                <td class="py-3.5 px-4 text-right text-gray-700">Rp {{ number_format($tr->price, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-4 text-center font-bold text-gray-900">1</td>
                                <td class="py-3.5 px-4 text-right font-bold text-gray-900">Rp {{ number_format($tr->price, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            @endforelse
                        </tbody>
                        <tfoot class="border-t-2 border-rose-100 text-sm">
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-right font-semibold text-gray-600">Subtotal:</td>
                                <td class="py-2 px-4 text-right font-bold text-gray-900">Rp {{ number_format($booking->subtotal ?? $booking->total_amount, 0, ',', '.') }}</td>
                            </tr>
                            @if(($booking->discount_amount ?? 0) > 0)
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-right font-semibold text-emerald-600">Diskon Voucher:</td>
                                <td class="py-2 px-4 text-right font-bold text-emerald-600">- Rp {{ number_format($booking->discount_amount, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if(($booking->transport_fee ?? 0) > 0)
                            <tr>
                                <td colspan="4" class="py-2 px-4 text-right font-semibold text-gray-600">Ongkos Transport (Home Service):</td>
                                <td class="py-2 px-4 text-right font-bold text-gray-900">+ Rp {{ number_format($booking->transport_fee, 0, ',', '.') }}</td>
                            </tr>
                            @endif
                            <tr class="bg-rose-50/70 text-base">
                                <td colspan="4" class="py-3 px-4 text-right font-black text-gray-900">Total Akhir:</td>
                                <td class="py-3 px-4 text-right font-black text-[#f45472]">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- PAYMENT VERIFICATION CARD --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 font-headline">Status Pembayaran & Verifikasi</h3>
                        <p class="text-xs text-gray-500">Skema: <span class="font-bold uppercase text-[#f45472]">{{ $booking->payment_type === 'cash' ? 'Cash di Salon (DP 35%)' : 'Cashless (Full 100%)' }}</span> &middot; Metode: <span class="font-bold uppercase text-gray-800">{{ $booking->payment_method ?? 'QRIS' }}</span></p>
                        @if($booking->payment_type === 'cash')
                            <div class="mt-2 text-xs space-y-0.5">
                                <p class="text-emerald-700 font-semibold">DP 35% Terbayar: <strong>Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</strong></p>
                                <p class="text-amber-800 font-bold">Sisa Wajib Pelunasan Cash: <strong>Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }}</strong></p>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3">
                        @if($booking->payment_status === 'paid')
                            <span class="px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 font-bold text-sm flex items-center gap-1.5">
                                ✓ Terverifikasi Lunas
                            </span>
                        @elseif($booking->payment_status === 'dp_paid')
                            <form method="POST" action="{{ route('admin.bookings.verify-payment', $booking->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-5 py-2.5 rounded-full bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 shadow-md transition-all">
                                    ✓ Tandai Lunas (Pelunasan Cash Rp {{ number_format($booking->remaining_amount, 0, ',', '.') }})
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.bookings.verify-payment', $booking->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-5 py-2.5 rounded-full bg-emerald-600 text-white font-bold text-xs hover:bg-emerald-700 shadow-md transition-all">
                                    ✓ Verifikasi Pembayaran Sekarang
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($booking->payment_proof)
                    <div class="mt-4">
                        <p class="text-xs font-bold text-gray-700 mb-2">Pratinjau Bukti Transfer Pelanggan:</p>
                        <a href="{{ \App\Support\ImageHelper::url($booking->payment_proof) }}" target="_blank" class="inline-block border-2 border-rose-100 rounded-2xl overflow-hidden hover:opacity-90 transition-opacity">
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
                        <h3 class="text-lg font-bold text-gray-900 font-headline">Ulasan Pelanggan</h3>
                        <p class="text-xs text-gray-500">Diberikan pada {{ $booking->review->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-1 text-yellow-500 bg-yellow-50 px-3 py-1.5 rounded-full border border-yellow-200">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $booking->review->rating)
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @else
                                <svg class="w-4 h-4 text-yellow-200 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endif
                        @endfor
                        <span class="ml-1 text-sm font-bold">{{ $booking->review->rating }}/5</span>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-6">
                    <div class="flex-1">
                        <p class="text-sm text-gray-800 font-medium italic mb-4">
                            "{{ $booking->review->comment ?? 'Pelanggan tidak memberikan komentar.' }}"
                        </p>

                        @if($booking->review->admin_reply)
                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 mt-4">
                                <span class="text-xs font-bold text-rose-500 uppercase tracking-widest mb-2 block">Balasan Admin:</span>
                                <p class="text-sm text-gray-700">{{ $booking->review->admin_reply }}</p>
                            </div>
                        @else
                            {{-- TODO: Provide a form to reply --}}
                            <div class="bg-purple-50/50 border border-purple-100 rounded-2xl p-4 mt-4">
                                <span class="text-xs font-bold text-purple-600 uppercase tracking-widest mb-2 block">Balas Ulasan Ini</span>
                                <form action="{{ route('admin.bookings.reply-review', $booking->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <textarea name="admin_reply" rows="2" class="w-full text-sm rounded-xl border-purple-200 focus:border-purple-400 focus:ring-purple-400 p-2 mb-2" placeholder="Tuliskan balasan terima kasih atau tanggapan resmi..."></textarea>
                                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl transition-colors">
                                        Kirim Balasan
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    @if($booking->review->photo)
                    <div class="w-full md:w-1/3">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Foto Hasil:</p>
                        <a href="{{ \App\Support\ImageHelper::url($booking->review->photo) }}" target="_blank" class="block rounded-2xl overflow-hidden border border-gray-200 hover:opacity-90 transition-opacity">
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
