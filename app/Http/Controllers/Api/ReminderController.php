<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Notification\BookingReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ReminderController extends Controller
{
    private BookingReminderService $reminderService;

    public function __construct(BookingReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }

    public function trigger(): JsonResponse
    {
        try {
            $this->reminderService->processReminders();
            return response()->json([
                "status" => "success",
                "message" => "Reminders processed successfully"
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to process reminders", ["error" => $e->getMessage()]);
            return response()->json([
                "status" => "error",
                "message" => "Failed to process reminders"
            ], 500);
        }
    }
}

