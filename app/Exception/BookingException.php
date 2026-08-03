<?php

namespace App\Exceptions;

use Exception;

class BookingException extends Exception
{   
    //return response json with status code 409
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 409); // 409 Conflict
    }
}