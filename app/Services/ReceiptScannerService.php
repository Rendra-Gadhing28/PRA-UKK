<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReceiptScannerService
{
    /**
     * Parse receipt image using Gemini 2.5 Flash API.
     *
     * @param string $imagePath Absolute or relative storage path to receipt image file.
     * @return array Parsed receipt structured data.
     * @throws Exception
     */
    public function parseReceipt(string $imagePath): array
    {
        if (! file_exists($imagePath)) {
            throw new Exception("File gambar struk tidak ditemukan di lokasi: {$imagePath}");
        }

        $apiKey = Config::get('services.gemini.api_key');
        if (empty($apiKey) || $apiKey === 'your_gemini_api_key_here') {
            throw new Exception("GEMINI_API_KEY belum dikonfigurasi di file .env");
        }

        $model = Config::get('services.gemini.model', 'gemini-2.5-flash');

        // Detect MIME Type
        $mimeType = mime_content_type($imagePath);
        if (! in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
            $mimeType = 'image/jpeg';
        }

        // Base64 Encode
        $imageData = base64_encode(file_get_contents($imagePath));

        $promptText = <<<PROMPT
Anda adalah sistem OCR Struk Belanja cerdas. Analisis foto struk belanja ini dan ekstrak seluruh informasi transaksi ke dalam format JSON berikut:

{
  "merchant": "Nama Toko / Merchant",
  "branch": "Cabang Toko (jika ada, atau null)",
  "transaction_date": "YYYY-MM-DD HH:MM:SS (format tanggal ISO 8601. Jika jam tidak tertera, gunakan 00:00:00)",
  "payment_method": "Metode Pembayaran (Cash / QRIS / Debit / Credit / Transfer / null)",
  "total_amount": 0.00,
  "items": [
    {
      "item_name": "Nama Barang / Produk / Layanan",
      "qty": 1,
      "unit_price": 0.00,
      "subtotal": 0.00,
      "category": "Kebutuhan"
    }
  ]
}

Aturan Kategori untuk setiap item:
Pilih salah satu dari daftar berikut yang paling sesuai: 'Kebutuhan', 'Makanan', 'Operasional', 'Kecantikan', 'Lainnya'.

PENTING: Respon HANYA berupa objek JSON valid tanpa teks tambahan atau tag markdown (tanpa ```json ... ```).
PROMPT;

        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(45)->post($endpoint, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $promptText,
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data'      => $imageData,
                                ],
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'response_mime_type' => 'application/json',
                    'temperature'        => 0.1,
                ],
            ]);

            if ($response->failed()) {
                Log::error('Gemini API Error Response', ['body' => $response->body()]);
                throw new Exception("Gagal menghubungi Gemini API: " . $response->status() . " - " . $response->body());
            }

            $responseData = $response->json();
            $rawJsonText = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

            // Clean markdown tags if present
            $cleanJson = trim($rawJsonText);
            $cleanJson = preg_replace('/^```json\s*/i', '', $cleanJson);
            $cleanJson = preg_replace('/^```\s*/i', '', $cleanJson);
            $cleanJson = preg_replace('/\s*```$/i', '', $cleanJson);

            $parsedData = json_decode($cleanJson, true);

            if (! is_array($parsedData)) {
                throw new Exception("Gagal menguraikan format JSON dari Gemini API.");
            }

            // Normalisasi data default jika ada field yang null
            return [
                'merchant'         => $parsedData['merchant'] ?? 'Merchant Tidak Diketahui',
                'branch'           => $parsedData['branch'] ?? null,
                'transaction_date' => $parsedData['transaction_date'] ?? date('Y-m-d H:i:s'),
                'payment_method'   => $parsedData['payment_method'] ?? 'Cash',
                'total_amount'     => (float) ($parsedData['total_amount'] ?? 0),
                'items'            => array_map(function ($item) {
                    return [
                        'item_name'  => $item['item_name'] ?? 'Item',
                        'qty'        => (int) ($item['qty'] ?? 1),
                        'unit_price' => (float) ($item['unit_price'] ?? 0),
                        'subtotal'   => (float) ($item['subtotal'] ?? 0),
                        'category'   => $item['category'] ?? 'Kebutuhan',
                    ];
                }, $parsedData['items'] ?? []),
            ];

        } catch (Exception $e) {
            Log::error('ReceiptScannerService Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
