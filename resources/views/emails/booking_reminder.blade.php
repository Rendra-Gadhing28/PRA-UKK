@php
    $logoPngPath = public_path('logo/yalia-logos-trnsprnt.png');
    $hasPng = file_exists($logoPngPath);
    $logoPngData = $hasPng ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPngPath)) : '';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title>Pengingat Reservasi - Yalia Beauty Salon</title>
    <style>
        :root { color-scheme: light dark; supported-color-schemes: light dark; }
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; margin: 0; padding: 20px; -webkit-font-smoothing: antialiased; }
        .wrapper { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.01); border: 1px solid #e5e7eb; }
        .brand-header { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); padding: 32px 24px; text-align: center; color: #ffffff; position: relative; }
        .logo-title { font-size: 24px; font-weight: 800; letter-spacing: 0.5px; margin: 0; font-family: 'Playfair Display', Georgia, serif; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .logo-tagline { font-size: 13px; color: #ccfbf1; margin-top: 4px; font-weight: 500; text-transform: uppercase; letter-spacing: 1.5px; }
        .body-content { padding: 32px 28px; }
        .reminder-pill { display: inline-flex; align-items: center; padding: 6px 16px; background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; border-radius: 9999px; font-size: 13px; margin-bottom: 20px; }
        .greeting { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 10px; }
        .intro-text { font-size: 14px; color: #4b5563; line-height: 1.6; margin-bottom: 24px; }
        .card { background-color: #fafafa; border: 1px solid #f3f4f6; border-radius: 12px; padding: 20px; margin-bottom: 24px; }
        .card-title { font-size: 14px; font-weight: 700; color: #0f766e; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px; border-bottom: 2px solid #ccfbf1; padding-bottom: 8px; display: flex; align-items: center; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 8px 0; font-size: 14px; vertical-align: top; }
        .info-label { color: #6b7280; width: 40%; font-weight: 500; }
        .info-value { color: #111827; font-weight: 700; width: 60%; text-align: right; }
        .status-paid { color: #059669; background-color: #d1fae5; padding: 3px 10px; border-radius: 6px; font-size: 12px; display: inline-block; }
        .status-pending { color: #d97706; background-color: #fef3c7; padding: 3px 10px; border-radius: 6px; font-size: 12px; display: inline-block; }
        .alert-box { background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 14px 16px; border-radius: 6px; font-size: 13px; color: #92400e; margin-bottom: 24px; line-height: 1.5; display: flex; align-items: flex-start; }
        .action-container { text-align: center; margin: 32px 0 16px 0; }
        .btn-primary { display: inline-block; background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); color: #ffffff !important; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3); transition: all 0.2s ease; }
        .footer { background-color: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #f3f4f6; color: #9ca3af; font-size: 12px; line-height: 1.6; }
        .footer strong { color: #6b7280; }
        .icon-inline { width: 16px; height: 16px; vertical-align: -3px; display: inline-block; margin-right: 6px; }

        @media (prefers-color-scheme: dark) {
            body { background-color: #111827 !important; color: #f9fafb !important; }
            .wrapper { background-color: #1f2937 !important; border-color: #374151 !important; }
            .card { background-color: #111827 !important; border-color: #374151 !important; }
            .info-label { color: #9ca3af !important; }
            .info-value { color: #f9fafb !important; }
            .greeting { color: #f9fafb !important; }
            .intro-text { color: #d1d5db !important; }
            .footer { background-color: #1f2937 !important; border-color: #374151 !important; color: #9ca3af !important; }
        }
    </style>
</head>
<body>
    <div class="wrapper" role="article" aria-label="Pengingat Reservasi Yalia Beauty">
        <!-- HEADER -->
        <div class="brand-header">
            @if(isset($message) && $hasPng)
                <div style="background: #ffffff; display: inline-block; padding: 10px 22px; border-radius: 14px; margin-bottom: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <img src="{{ $message->embed($logoPngPath) }}" alt="Yalia Beauty Logo" style="max-height: 55px; width: auto; display: block; margin: 0 auto;">
                </div>
            @elseif($hasPng)
                <div style="background: #ffffff; display: inline-block; padding: 10px 22px; border-radius: 14px; margin-bottom: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <img src="{{ $logoPngData }}" alt="Yalia Beauty Logo" style="max-height: 55px; width: auto; display: block; margin: 0 auto;">
                </div>
            @else
                <h1 class="logo-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:-3px; margin-right:6px;"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    YALIA BEAUTY
                </h1>
            @endif
            <div class="logo-tagline">Glow Up Your Beauty & Salon Service</div>
        </div>

        <!-- BODY -->
        <div class="body-content">
            <div class="reminder-pill">
                <svg class="icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                Pengingat Reservasi {{ $reminderType }}
            </div>

            <div class="greeting">Halo Kak {{ $booking->user->name ?? 'Pelanggan Setia' }},</div>
            <p class="intro-text">
                Ini adalah pengingat otomatis bahwa Anda memiliki jadwal perawatan di <strong>Yalia Beauty Salon</strong>. Kami siap menyambut kehadiran Anda dengan pelayanan terbaik kami!
            </p>

            <!-- RINCIAN RESERVASI CARD -->
            <div class="card">
                <div class="card-title">
                    <svg class="icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    Rincian Reservasi
                </div>
                <table class="info-table" role="presentation" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="info-label">Kode Booking</td>
                        <td class="info-value">{{ $booking->booking_code ?? 'BK-YALIA-8888' }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">Tanggal Perawatan</td>
                        <td class="info-value">
                            {{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->translatedFormat('l, d F Y') : date('l, d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Waktu Layanan</td>
                        <td class="info-value">
                            {{ $booking->time_start ? \Carbon\Carbon::parse($booking->time_start)->format('H:i') : '10:00' }} WIB
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Tipe Layanan</td>
                        <td class="info-value">
                            @if(($booking->booking_type ?? '') === 'home_service')
                                <svg class="icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                Home Service (Panggilan)
                            @else
                                <svg class="icon-inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="22.01"></line><line x1="15" y1="22" x2="15" y2="22.01"></line><line x1="12" y1="22" x2="12" y2="22.01"></line><line x1="12" y1="2" x2="12" y2="6"></line></svg>
                                Salon Visit (Datang ke Salon)
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="info-label">Status Pembayaran</td>
                        <td class="info-value">
                            <span class="{{ ($booking->payment_status ?? 'pending') === 'paid' ? 'status-paid' : 'status-pending' }}">
                                {{ strtoupper($booking->payment_status ?? 'PENDING') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            @if(($booking->payment_status ?? 'pending') !== 'paid')
                <div class="alert-box">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0; margin-right:10px; color:#d97706;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <div>
                        <strong>Perhatian:</strong> Status pembayaran Anda saat ini masih <strong>PENDING</strong>. Silakan lakukan penyelesaian pembayaran QRIS/DP agar jadwal reservasi tidak terbatalkan otomatis.
                    </div>
                </div>
            @endif

            <!-- TOMBOL CTA -->
            <div class="action-container">
                <a href="{{ config('app.url') }}/dashboard/booking/{{ $booking->id ?? '1' }}" class="btn-primary">
                    Lihat & Kelola Reservasi Saya
                </a>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p><strong>Yalia Beauty Salon</strong><br>
            GHV9+F2 Candi, Kabupaten Boyolali, Jawa Tengah<br>
            Email: {{ config('mail.from.address') }} | WhatsApp Customer Care Active</p>
            <p>&copy; {{ date('Y') }} Yalia Beauty Platform. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
