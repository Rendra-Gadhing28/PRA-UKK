<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Daftar Booking Yalia Beauty</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #f45472; padding-bottom: 10px; }
        .header h1 { color: #f45472; margin: 0; font-size: 20px; }
        .header p { margin: 2px 0 0; color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f45472; color: white; font-size: 11px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>YALIA BEAUTY SALON</h1>
        <p>Laporan Data Reservasi Pelanggan</p>
        <p>Dicetak Pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Beautician</th>
                <th>Treatment</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status Bayar</th>
                <th>Status Booking</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $index => $b)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $b->booking_code }}</strong></td>
                <td>{{ $b->user?->name ?? 'Guest' }}<br><small>{{ $b->user?->phone ?? '-' }}</small></td>
                <td>{{ $b->beautician?->name ?? 'Auto' }}</td>
                <td>{{ $b->treatments->pluck('name')->join(', ') ?: 'N/A' }}</td>
                <td>{{ $b->booking_date ? $b->booking_date->format('d/m/Y') : '-' }}<br><small>{{ $b->time_start ?? '' }}</small></td>
                <td class="text-right">Rp {{ number_format($b->total_amount, 0, ',', '.') }}</td>
                <td class="text-center">{{ ucfirst($b->payment_status) }}</td>
                <td class="text-center">{{ ucfirst($b->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Tidak ada data reservasi ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
