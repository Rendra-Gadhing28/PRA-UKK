<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan & Booking Yalia Beauty</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #f45472; padding-bottom: 10px; }
        .header h1 { color: #f45472; margin: 0; font-size: 22px; }
        .summary-box { width: 100%; margin-bottom: 20px; border-collapse: collapse; }
        .summary-box td { padding: 10px; border: 1px solid #ddd; background: #fafafa; }
        .summary-box td strong { font-size: 14px; color: #5b3a29; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.data th { background-color: #f45472; color: white; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>YALIA BEAUTY SALON</h1>
        <p>Laporan Ringkasan Performa & Booking — {{ $now->translatedFormat('F Y') }}</p>
    </div>

    <table class="summary-box">
        <tr>
            <td>
                <span>Total Pemasukan:</span><br>
                <strong>Rp {{ number_format($income, 0, ',', '.') }}</strong>
            </td>
            <td>
                <span>Total Pengeluaran:</span><br>
                <strong>Rp {{ number_format($expense, 0, ',', '.') }}</strong>
            </td>
            <td>
                <span>Laba Bersih:</span><br>
                <strong>Rp {{ number_format($income - $expense, 0, ',', '.') }}</strong>
            </td>
        </tr>
    </table>

    <h3>Daftar Reservasi Bulan Ini</h3>
    <table class="data">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Treatment</th>
                <th>Tanggal</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $b)
            <tr>
                <td>{{ $b->booking_code }}</td>
                <td>{{ $b->user?->name ?? 'Guest' }}</td>
                <td>{{ $b->treatments->pluck('name')->join(', ') ?: 'N/A' }}</td>

                <td>{{ $b->booking_date->format('d/m/Y') }}</td>
                <td class="text-right">Rp {{ number_format($b->total_amount, 0, ',', '.') }}</td>
                <td>{{ is_object($b->status) && method_exists($b->status, 'badgeLabel') ? $b->status->badgeLabel() : ucfirst((string)$b->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center;">Tidak ada data reservasi bulan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
