<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('logo/yalia-logos.svg') }}" type="image/svg+xml">
        <link rel="alternate icon" href="{{ asset('logo/yalia-logos-trnsprnt.png') }}" type="image/png">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="stylesheet" type="text/css" href="loading-bar.css"/>
        <script type="text/javascript" src="loading-bar.js"></script>
        <title>{{ config('app.name', 'Yalia Beauty') }} — Admin Executive</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </head>
    <body class="font-sans antialiased bg-[#fdf5f6] text-gray-900">
        <div class="min-h-screen relative">
            
            {{-- Admin Dedicated Sidebar --}}
            @include('layouts.sidebar')

            {{-- Main Content Area with Desktop Sidebar Offset --}}
            <div class="md:ml-64 flex flex-col min-h-screen">
                
                {{-- Header Slot (if provided) --}}
                @if (isset($header))
                    <header class="bg-white/80 backdrop-blur-md border-b border-rose-100 px-4 sm:px-6 lg:px-8 py-5">
                        {{ $header }}
                    </header>
                @endif

                {{-- Page Content --}}
                <main class="flex-1">
                    {{ $slot }}
                </main>

                {{-- Toast Notifications --}}
                <x-toast />
            </div>

        </div>
    </body>
</html>
