<?php

 class ToastHelper
{
    public static function success(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'success',
            'message' => $message,
            'description' => $description,
        ]);
    }
    
    public static function error(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'error',
            'message' => $message,
            'description' => $description,
        ]);
    }
    
    public static function warning(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'warning',
            'message' => $message,
            'description' => $description,
        ]);
    }
    
    public static function info(string $message, string $description = '')
    {
        session()->flash('toast', [
            'type' => 'info',
            'message' => $message,
            'description' => $description,
        ]);
    }
}
?>