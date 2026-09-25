<?php

namespace App\Exceptions;

use Exception;

class BookingException extends Exception
{
    /**
     * Render exception ke response JSON dengan status HTTP 409 Conflict.
     */
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 409); // 409 Conflict
    }
}
