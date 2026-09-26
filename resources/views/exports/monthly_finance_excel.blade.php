<table>
    <thead>
        <tr>
            <th colspan="12" style="font-size: 14pt; font-weight: bold; color: #be185d;">
                LAPORAN KEUANGAN DAN PERFORMA RESERVASI BULANAN — YALIA BEAUTY SALON
            </th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 9pt; font-style: italic; color: #64748b;">
                Alamat: GHV9+F2 Candi, Kabupaten Boyolali, Jawa Tengah | WhatsApp: 0822-2702-3362
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size: 9.5pt; font-weight: bold; color: #475569;">
                Periode Laporan: {{ $month->translatedFormat('F Y') }}
            </th>
            <th colspan="6" style="font-size: 9pt; color: #475569; text-align: right;">
                Waktu Unduh: {{ $downloadedAt->translatedFormat('l, d F Y H:i') }} WIB
            </th>
        </tr>
        <tr>
            <th colspan="3" style="font-size: 9pt; background-color: #f1f5f9; color: #1e293b; text-align: center;">
                Total Reservasi: <strong>{{ $bookings->count() }} Booking</strong>
            </th>
            <th colspan="3" style="font-size: 9pt; background-color: #ecfdf5; color: #047857; text-align: center;">
                Total Omset (Pemasukan): <strong>Rp {{ number_format($income, 0, ',', '.') }}</strong>
            </th>
            <th colspan="3" style="font-size: 9pt; background-color: #fff1f2; color: #be123c; text-align: center;">
                Total Pengeluaran: <strong>Rp {{ number_format($expense, 0, ',', '.') }}</strong>
            </th>
            <th colspan="3" style="font-size: 9pt; background-color: #eff6ff; color: #1d4ed8; text-align: center;">
                Laba Bersih: <strong>Rp {{ number_format($netProfit, 0, ',', '.') }}</strong>
            </th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr>
            <th colspan="12" style="font-size: 11pt; font-weight: bold; color: #334155;">
                Rincian Riwayat Reservasi Bulan Ini
            </th>
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
                    Tidak ada transaksi booking pada periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <th colspan="9" style="font-weight: bold; text-align: right; background-color: #ffe4e6; color: #9f1239;">
                TOTAL PEMASUKAN BERHASIL (OMSET)
            </th>
            <th style="font-weight: bold; text-align: right; background-color: #ffe4e6; color: #9f1239;">
                {{ $income }}
            </th>
            <th colspan="2" style="background-color: #ffe4e6;"></th>
        </tr>
    </tfoot>
</table>
