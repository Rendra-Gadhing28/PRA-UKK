<?php

declare(strict_types=1);

namespace App\Services\Notification;

use App\Models\Bookings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingReminderService
{
    private FonnteService $fonnteService;

    public function __construct(FonnteService $fonnteService)
    {
        $this->fonnteService = $fonnteService;
    }

    public function processReminders(): void
    {
        $now = Carbon::now();

        $bookings = Bookings::with(["user", "treatment"])
            ->whereIn("status", ["pending", "confirmed", "dp_paid"])
            ->where(function ($query) use ($now) {
                // Hanya ambil yang booking date-nya hari ini atau besok untuk efisiensi
                $query->whereDate("booking_date", ">=", $now->toDateString())
                      ->whereDate("booking_date", "<=", $now->copy()->addDays(2)->toDateString());
            })
            ->where(function($q) {
                $q->where("is_h24_reminded", false)
                  ->orWhere("is_h1_reminded", false)
                  ->orWhere("is_m30_reminded", false);
            })
            ->get();

        foreach ($bookings as $booking) {
            $bookingTime = Carbon::parse($booking->booking_date . " " . $booking->time_start);
            
            // Lewati jika jadwal sudah berlalu (misal selisih negatif)
            if ($now->greaterThanOrEqualTo($bookingTime)) {
                continue;
            }

            $diffInMinutes = $now->diffInMinutes($bookingTime);
            $diffInHours = $now->diffInHours($bookingTime);

            $targetEmail = $booking->user->email ?? null;

            if (!$targetEmail) {
                continue;
            }

            // --- 1. Reminder H-24 Jam (<= 24 jam & belum diingatkan) ---
            if ($diffInHours <= 24 && $diffInHours > 1 && !$booking->is_h24_reminded) {
                try {
                    \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\BookingReminderMail($booking, "H-1 Hari"));
                    $booking->update(["is_h24_reminded" => true]);
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email H-24", ["email" => $targetEmail, "error" => $e->getMessage()]);
                }
            }
            
            // --- 2. Reminder H-1 Jam (<= 1 jam & belum diingatkan) ---
            if ($diffInHours <= 1 && $diffInMinutes > 30 && !$booking->is_h1_reminded) {
                try {
                    \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\BookingReminderMail($booking, "H-1 Jam"));
                    $booking->update(["is_h1_reminded" => true]);
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email H-1", ["email" => $targetEmail, "error" => $e->getMessage()]);
                }
            }
            
            // --- 3. Reminder H-30 Menit (<= 30 menit & belum diingatkan) ---
            if ($diffInMinutes <= 30 && !$booking->is_m30_reminded) {
                try {
                    \Illuminate\Support\Facades\Mail::to($targetEmail)->send(new \App\Mail\BookingReminderMail($booking, "H-30 Menit"));
                    $booking->update(["is_m30_reminded" => true]);
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email H-30", ["email" => $targetEmail, "error" => $e->getMessage()]);
                }
            }
        }
    }

    private function formatMessage(Bookings $booking, string $type): string
    {
        $treatmentName = $booking->treatment->name ?? "Perawatan";
        $date = Carbon::parse($booking->booking_date)->translatedFormat("l, d F Y");
        $time = Carbon::parse($booking->time_start)->format("H:i") . " WIB";
        
        $greeting = "Halo Kak " . ($booking->user->name ?? "Pelanggan") . ",\n\n";
        
        $body = "Ini adalah pengingat otomatis ($type) untuk jadwal reservasi Anda di Yalia Beauty Salon.\n\n";
        $body .= "?? Layanan: *$treatmentName*\n";
        $body .= "?? Tanggal: $date\n";
        $body .= "? Waktu: $time\n\n";

        if ($booking->status === "pending") {
            $body .= "?? *Status Pembayaran Anda masih PENDING.*\nMohon segera selesaikan pembayaran DP/Lunas agar reservasi tidak hangus ya Kak.\n\n";
        }

        $footer = "Terima kasih dan kami tunggu kedatangannya!\n\n_Pesan ini dikirim otomatis oleh sistem_";

        return $greeting . $body . $footer;
    }
}

