<?php

declare(strict_types=1);

namespace App\Services\Notification;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    private string $token;
    private string $apiUrl = "https://api.fonnte.com/send";

    public function __construct()
    {
        $this->token = config("services.fonnte.token", env("FONNTE_TOKEN", ""));
    }

    public function sendMessage(string $target, string $message): bool
    {
        if (blank($this->token)) {
            Log::warning("Fonnte token is missing. WhatsApp message not sent.", [
                "target" => $target,
                "message" => $message,
            ]);
            return false;
        }

        try {
            $response = Http::withHeaders([
                "Authorization" => $this->token,
            ])->post($this->apiUrl, [
                "target" => $target,
                "message" => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data["status"]) && $data["status"] === true) {
                    return true;
                }
            }

            Log::error("Fonnte API response error", [
                "target" => $target,
                "response" => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error("Fonnte HTTP request failed", [
                "target" => $target,
                "error" => $e->getMessage(),
            ]);
            return false;
        }
    }
}

