<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-gray-100">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('logo/yalia-logos.svg') }}" type="image/svg+xml">
        <link rel="alternate icon" href="{{ asset('logo/yalia-logos-trnsprnt.png') }}" type="image/png">

        <!-- Styles & Scripts (Moved to top of head to prevent FOUC) -->
        <style>[x-cloak] { display: none !important; }</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts & Preconnect (Sesi 2 Optimalisasi) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
        
        <!-- FontAwesome non-blocking -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">
        <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"></noscript>
        <script defer type="module" src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs"></script>
        @stack('styles')
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col relative overflow-x-hidden bg-white">
        {{-- Ambient Soft Pink Gradient Mesh & Orbs (Soft 70% Opacity Touch) --}}
        <div class="fixed inset-0 pointer-events-none -z-20 opacity-70" aria-hidden="true" style="background: linear-gradient(135deg, rgba(253, 226, 237, 0.75) 0%, rgba(249, 208, 227, 0.65) 50%, rgba(247, 198, 222, 0.65) 100%);"></div>
        <div class="fixed w-[600px] h-[600px] -top-36 -left-36 rounded-full bg-[#5C1439]/10 blur-[120px] -z-10 pointer-events-none opacity-70" aria-hidden="true"></div>
        <div class="fixed w-[550px] h-[550px] top-1/3 -right-36 rounded-full bg-[#E0247E]/12 blur-[110px] -z-10 pointer-events-none opacity-70" aria-hidden="true"></div>
        <div class="fixed w-[500px] h-[500px] bottom-10 left-1/4 rounded-full bg-[#F4B942]/10 blur-[100px] -z-10 pointer-events-none opacity-70" aria-hidden="true"></div>

        <div class="min-h-screen flex flex-col justify-between relative z-10">
            @include('layouts.navigation')

            <!-- Page Content -->
            <main class="flex-grow flex flex-col">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
            <x-toast />
            <x-action-modal />
        </div>
        @stack('scripts')
    </body>
</html>

