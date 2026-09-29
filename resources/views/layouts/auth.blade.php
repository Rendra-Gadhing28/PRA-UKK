<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Yalia Beauty')</title>

    {{-- Vite: Tailwind + Alpine --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('logo/yalia-logos.svg') }}" type="image/svg+xml">
    <link rel="alternate icon" href="{{ asset('logo/yalia-logos-trnsprnt.png') }}" type="image/png">
</head>
<body class="antialiased">

    @yield('content')

    {{-- Toast Notifications & Action Modals --}}
    <x-toast />
    <x-action-modal />

</body>
</html>