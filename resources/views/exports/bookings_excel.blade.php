<table>
    <thead>
        <tr>
            <th colspan="12" style="font-size: 14pt; font-weight: bold; color: #be185d;">
                LAPORAN DAFTAR RESERVASI DAN BOOKING — YALIA BEAUTY SALON
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 9pt; font-style: italic; color: #64748b;">
                Alamat: GHV9+F2 Candi, Kabupaten Boyolali, Jawa Tengah | WhatsApp: 0822-2702-3362
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 9pt; color: #475569;">
                Waktu Unduh: {{ $downloadedAt->translatedFormat('l, d F Y H:i') }} WIB
            </th>
            <th colspan="6" style="font-size: 9pt; color: #475569; text-align: right;">
                @if(!empty($filters['start_date']) && !empty($filters['end_date']))
                    Periode: {{ $filters['start_date'] }} s/d {{ $filters['end_date'] }}
                @else
                    Periode: Semua Riwayat
                @endif
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 9pt; font-weight: bold; color: #0f172a;">
                Total Reservasi: {{ $bookings->count() }} Data
            </th>
            <th colspan="6" style="font-size: 9pt; font-weight: bold; color: #be185d; text-align: right;">
                Total Omset: Rp {{ number_format($totalAmount, 0, ',', '.') }}
            </th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">No</th>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">Kode Booking</th>
            <th style="font-weight: bold; text-align: left; background-color: #f45472; color: #ffffff;">Nama Pelanggan</th>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">No. Handphone</th>
            <th style="font-weight: bold; text-align: left; background-color: #f45472; color: #ffffff;">Terapis / Beautician</th>
            <th style="font-weight: bold; text-align: left; background-color: #f45472; color: #ffffff;">Layanan Treatment</th>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">Tanggal Booking</th>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">Waktu Layanan</th>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">Tipe Kunjungan</th>
            <th style="font-weight: bold; text-align: right; background-color: #f45472; color: #ffffff;">Total Biaya (Rp)</th>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">Status Pembayaran</th>
            <th style="font-weight: bold; text-align: center; background-color: #f45472; color: #ffffff;">Status Reservasi</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bookings as $index => $b)
            @php
                $statusText = is_object($b->status)
                    ? (method_exists($b->status, 'badgeLabel') ? $b->status->badgeLabel() : $b->status->value)
                    : (string) $b->status;
                $phone = $b->user?->phone ?? '-';
            @endphp
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-weight: bold;">{{ $b->booking_code }}</td>
                <td>{{ $b->user?->name ?? 'Guest' }}</td>
                <td style="text-align: center;">'{{ $phone }}</td>
                <td>{{ $b->beautician?->name ?? 'Auto Assign' }}</td>
                <td>{{ $b->treatments->pluck('name')->join(', ') ?: '-' }}</td>
                <td style="text-align: center;">{{ $b->booking_date ? $b->booking_date->format('Y-m-d') : '-' }}</td>
                <td style="text-align: center;">{{ ($b->time_start ?? '') . ' - ' . ($b->time_end ?? '') }}</td>
                <td style="text-align: center;">{{ $b->booking_type === 'home' ? 'Home Service' : 'Ke Salon' }}</td>
                <td style="text-align: right;">{{ $b->total_amount }}</td>
                <td style="text-align: center;">{{ $b->payment_status ? ucfirst($b->payment_status) : 'Lunas' }}</td>
                <td style="text-align: center;">{{ $statusText }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="12" style="text-align: center; color: #94a3b8; font-style: italic;">
                    Tidak ada data reservasi yang ditemukan.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="9" style="font-weight: bold; text-align: right; background-color: #ffe4e6; color: #9f1239;">
                TOTAL NILAI KESELURUHAN
            </th>
            <th style="font-weight: bold; text-align: right; background-color: #ffe4e6; color: #9f1239;">
                {{ $totalAmount }}
            </th>
            <th colspan="2" style="background-color: #ffe4e6;"></th>
        </tr>
    </tfoot>
</table>
