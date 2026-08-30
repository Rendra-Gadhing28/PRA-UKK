<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Bookings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Integrasi Midtrans Core API (bukan Snap) khusus channel QRIS. Dipilih
 * karena skema `bookings` sudah punya kolom `qris_code` / `qris_image_url`
 * yang perlu diisi dengan QR string & URL gambar QR dari Midtrans, untuk
 * ditampilkan langsung di halaman pembayaran kita sendiri.
 *
 * Dokumentasi: https://docs.midtrans.com/reference/qris
 */
class MidtransQrisService
{
    private function baseUrl(): string
    {
        $isProduction = (bool) config('booking.midtrans.is_production');

        return $isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    private function serverKey(): string
    {
        $key = config('booking.midtrans.server_key');

        if (blank($key)) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum diset di .env');
        }

        return $key;
    }

    /**
     * Buat charge QRIS baru untuk sebuah booking, lalu simpan detail QR
     * (qris_code, qris_image_url, midtrans_order_id, midtrans_transaction_id)
     * ke booking tersebut.
     */
    public function createCharge(Bookings $booking): Bookings
    {
        $orderId = $booking->midtrans_order_id ?: $booking->booking_code;

        $chargeAmount = ($booking->payment_type === 'cash' && (float) $booking->dp_amount > 0)
            ? (float) $booking->dp_amount
            : (float) $booking->total_amount;

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->withHeaders(['Accept' => 'application/json'])
            ->timeout(15)
            ->post("{$this->baseUrl()}/charge", [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) round($chargeAmount),
                ],
                'qris' => [
                    'acquirer' => 'gopay',
                ],
            ]);

        if (! $response->successful()) {
            Log::error('MidtransQrisService: charge gagal', [
                'booking_id' => $booking->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new RuntimeException('Gagal membuat pembayaran QRIS, silakan coba lagi.');
        }

        $data = $response->json();

        $qrAction = collect($data['actions'] ?? [])
            ->firstWhere('name', 'generate-qr-code');

        $booking->update([
            'midtrans_order_id' => $orderId,
            'midtrans_transaction_id' => $data['transaction_id'] ?? null,
            'qris_code' => $data['qr_string'] ?? null,
            'qris_image_url' => $qrAction['url'] ?? null,
            'payment_status' => 'pending',
        ]);

        return $booking->fresh();
    }

    /**
     * Cek status transaksi terkini langsung ke Midtrans (dipakai sebagai
     * fallback verifikasi manual, di luar webhook).
     *
     * @return array{transaction_status: string, fraud_status: ?string}|null
     */
    public function checkStatus(string $orderId): ?array
    {
        $response = Http::withBasicAuth($this->serverKey(), '')
            ->withHeaders(['Accept' => 'application/json'])
            ->timeout(10)
            ->get("{$this->baseUrl()}/{$orderId}/status");

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        return [
            'transaction_status' => $data['transaction_status'] ?? 'unknown',
            'fraud_status' => $data['fraud_status'] ?? null,
        ];
    }

    /**
     * Verifikasi signature notifikasi webhook Midtrans.
     * signature = SHA512(order_id + status_code + gross_amount + server_key)
     */
    public function isValidSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey());

        return hash_equals($expected, $signatureKey);
    }
}
