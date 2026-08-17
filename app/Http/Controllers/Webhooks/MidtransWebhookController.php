<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Bookings;
use App\Services\Payment\MidtransQrisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Menerima notifikasi server-to-server dari Midtrans (sandbox) dan meng-update
 * payment_status booking. Ini sumber kebenaran utama; polling di frontend
 * cuma "menanyakan" nilai yang diupdate lewat sini.
 *
 * Daftarkan URL ini di Midtrans Dashboard sandbox: Settings > Configuration
 * > Payment Notification URL, contoh: https://domain-anda.com/webhooks/midtrans
 */
class MidtransWebhookController extends Controller
{
    public function __construct(
        private readonly MidtransQrisService $midtransQris,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (! $orderId || ! $statusCode || ! $grossAmount || ! $signatureKey) {
            return response()->json(['message' => 'Payload tidak lengkap.'], 422);
        }

        if (! $this->midtransQris->isValidSignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('MidtransWebhookController: signature tidak valid', ['order_id' => $orderId]);

            return response()->json(['message' => 'Invalid signature.'], 403);
        }

        $booking = Bookings::where('midtrans_order_id', $orderId)->first();

        if (! $booking) {
            Log::warning('MidtransWebhookController: booking tidak ditemukan', ['order_id' => $orderId]);

            return response()->json(['message' => 'Booking tidak ditemukan.'], 404);
        }

        // Booking sudah final (paid), abaikan notifikasi susulan yang tidak relevan.
        if ($booking->payment_status === 'paid') {
            return response()->json(['message' => 'OK']);
        }

        match (true) {
            in_array($transactionStatus, ['capture', 'settlement'], true)
                && $fraudStatus !== 'deny' => $booking->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                    'payment_verified_at' => now(),
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? $booking->midtrans_transaction_id,
                    'version' => $booking->version + 1,
                ]),

            in_array($transactionStatus, ['expire', 'cancel', 'deny'], true) => $booking->update([
                'payment_status' => 'unpaid',
                'status' => 'canceled',
                'cancel_reason' => "Pembayaran {$transactionStatus} via Midtrans.",
                'canceled_at' => now(),
                'version' => $booking->version + 1,
            ]),

            $transactionStatus === 'pending' => $booking->update([
                'payment_status' => 'pending',
                'version' => $booking->version + 1,
            ]),

            default => Log::info('MidtransWebhookController: status diabaikan', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
            ]),
        };

        return response()->json(['message' => 'OK']);
    }
}
