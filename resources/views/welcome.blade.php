<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Yalia Beauty') }} — Salon Kecantikan & Booking Online</title>

        <!-- Google Fonts: Playfair Display & Work Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- FontAwesome Icons 6.5.1 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Leaflet JS Map CSS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
        <!-- Leaflet JS Map Script -->
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <!-- Alpine.js CDN -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body {
                font-family: 'Work Sans', sans-serif;
            }
            .font-serif-heading {
                font-family: 'Playfair Display', serif;
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body x-data="{ mobileMenuOpen: false }" class="bg-[#fff8f8] text-[#25181c] antialiased selection:bg-[#ffd2e1] selection:text-[#b01f44] min-h-screen flex flex-col">

        <!-- 1. NAVBAR (STICKY + MOBILE HAMBURGER) -->
        <header class="sticky top-0 z-50 bg-[#fff8f8]/95 backdrop-blur-md border-b border-[#e0bec1]/60 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between w-full">
                
                <!-- BRAND LOGO WITH TRANSPARENT SVG -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty Logo" class="h-10 sm:h-12 w-auto object-contain">
                    <div class="flex flex-col">
                        <span class="font-serif-heading font-bold text-xl tracking-tight text-[#25181c] group-hover:text-[#b01f44] transition-colors">
                            Yalia Beauty
                        </span>
                        <span class="text-xs tracking-widest uppercase font-semibold text-[#9b4054] -mt-1">
                            Salon & Nail Spa
                        </span>
                    </div>
                </a>

                <!-- DESKTOP NAV LINKS -->
                <nav class="hidden xl:flex items-center space-x-6 font-medium text-sm text-[#594043]">
                    <a href="#layanan" class="hover:text-[#b01f44] transition-colors whitespace-nowrap">Layanan</a>
                    <a href="#keunggulan" class="hover:text-[#b01f44] transition-colors whitespace-nowrap">Kenapa Yalia</a>
                    <a href="#galeri" class="hover:text-[#b01f44] transition-colors whitespace-nowrap">Galeri</a>
                    <a href="#membership" class="hover:text-[#b01f44] transition-colors whitespace-nowrap">Membership VIP</a>
                    <a href="#testimoni" class="hover:text-[#b01f44] transition-colors whitespace-nowrap">Testimoni</a>
                    <a href="#lokasi" class="hover:text-[#b01f44] transition-colors whitespace-nowrap">Lokasi</a>
                    <a href="#faq" class="hover:text-[#b01f44] transition-colors whitespace-nowrap">FAQ</a>
                </nav>

                <!-- RIGHT CONTAINER: AUTH/CTA BUTTONS + HAMBURGER ICON RIGHT NEXT TO EACH OTHER -->
                <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                    <div class="hidden sm:flex items-center gap-2 sm:gap-3">
                        @auth
                            <a href="{{ route('user.dashboard') }}" 
                               class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-[#b01f44] text-white text-xs sm:text-sm font-semibold shadow-md hover:bg-[#910030] transition-all">
                                Dashboard Saya
                            </a>
                        @else
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center justify-center px-4 py-2.5 rounded-full text-xs sm:text-sm font-semibold text-[#b01f44] hover:bg-[#ffd2e1]/50 transition-all">
                                Masuk
                            </a>
                            <a href="{{ route('user.bookings.create') }}" 
                               class="inline-flex items-center justify-center px-5 py-2.5 rounded-full bg-[#b01f44] text-white text-xs sm:text-sm font-semibold shadow-md hover:bg-[#910030] transition-all">
                                Booking Sekarang
                            </a>
                        @endauth
                    </div>

                    <!-- HAMBURGER BUTTON (RIGHT NEXT TO BUTTONS) -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" 
                            type="button" 
                            class="xl:hidden p-2 rounded-xl text-[#594043] hover:text-[#b01f44] hover:bg-[#ffe8ed] focus:outline-none transition-colors"
                            aria-label="Toggle Menu">
                        <i x-show="!mobileMenuOpen" class="fa-solid fa-bars text-xl"></i>
                        <i x-show="mobileMenuOpen" x-cloak class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>

            </div>

            <!-- MOBILE MENU DROPDOWN -->
            <div x-show="mobileMenuOpen" 
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="xl:hidden bg-[#fff8f8] border-b border-[#e0bec1] px-4 pt-2 pb-6 space-y-3">
                <a @click="mobileMenuOpen = false" href="#layanan" class="block py-2 text-sm font-medium text-[#594043] hover:text-[#b01f44]">Layanan Unggulan</a>
                <a @click="mobileMenuOpen = false" href="#keunggulan" class="block py-2 text-sm font-medium text-[#594043] hover:text-[#b01f44]">Kenapa Yalia</a>
                <a @click="mobileMenuOpen = false" href="#galeri" class="block py-2 text-sm font-medium text-[#594043] hover:text-[#b01f44]">Galeri & Hasil Kerja</a>
                <a @click="mobileMenuOpen = false" href="#membership" class="block py-2 text-sm font-medium text-[#594043] hover:text-[#b01f44]">Membership VIP</a>
                <a @click="mobileMenuOpen = false" href="#testimoni" class="block py-2 text-sm font-medium text-[#594043] hover:text-[#b01f44]">Ulasan Pelanggan</a>
                <a @click="mobileMenuOpen = false" href="#lokasi" class="block py-2 text-sm font-medium text-[#594043] hover:text-[#b01f44]">Lokasi & Jam Operasional</a>
                <a @click="mobileMenuOpen = false" href="#faq" class="block py-2 text-sm font-medium text-[#594043] hover:text-[#b01f44]">FAQ</a>
                <div class="pt-4 border-t border-[#e0bec1]/60 flex flex-col gap-2">
                    @auth
                        <a href="{{ route('user.dashboard') }}" class="w-full py-3 text-center rounded-full bg-[#b01f44] text-white font-semibold text-sm">Dashboard Saya</a>
                    @else
                        <a href="{{ route('login') }}" class="w-full py-2.5 text-center rounded-full border border-[#b01f44] text-[#b01f44] font-semibold text-sm">Masuk</a>
                        <a href="{{ route('user.bookings.create') }}" class="w-full py-3 text-center rounded-full bg-[#b01f44] text-white font-semibold text-sm shadow-md">Booking Sekarang</a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-grow">

            <!-- 2. HERO SECTION -->
            <section class="relative overflow-hidden py-12 lg:py-20 bg-gradient-to-b from-[#fff8f8] via-[#fdf5f6] to-[#fff8f8]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center w-full">
                        
                        <!-- HERO LEFT CONTENT -->
                        <div class="flex flex-col items-start w-full">
                            
                            <!-- MINT COMPLEMENTARY TRUST BADGE -->
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-[#d2fff0] border border-[#7e9990]/40 text-[#2a3330] text-xs font-semibold tracking-wide mb-6 shadow-xs">
                                <i class="fa-solid fa-circle-check text-[#059669]"></i>
                                <span>100% Higienis, Steril & Terapis Bersertifikasi</span>
                            </div>

                            <!-- HEADLINE -->
                            <h1 class="font-serif-heading font-bold text-4xl sm:text-5xl lg:text-6xl text-[#25181c] leading-[1.15] mb-6">
                                Perawatan Salon & Nail Art Elegance,<br>
                                <span class="text-[#b01f44] italic">Tanpa Antri Online</span>
                            </h1>

                            <!-- SUBHEADLINE -->
                            <p class="text-base sm:text-lg text-[#594043] leading-relaxed mb-8 max-w-2xl">
                                Nikmati perawatan kecantikan profesional — mulai dari Facial Glow, Manicure Nail Art, Hair Spa, hingga Body Treatment. Bebas pilih datang ke Studio Salon atau dipanggil ke rumah Anda.
                            </p>

                            <!-- CTA BUTTON GROUP -->
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto mb-10">
                                <a href="{{ route('user.bookings.create') }}" 
                                   class="inline-flex items-center justify-center px-8 py-4 rounded-2xl bg-[#b01f44] text-white font-semibold text-base shadow-[0_4px_16px_rgba(176,31,68,0.25)] hover:bg-[#910030] hover:-translate-y-0.5 transition-all text-center">
                                    <span>Booking Sekarang</span>
                                    <i class="fa-solid fa-arrow-right ml-2"></i>
                                </a>
                                <a href="#layanan" 
                                   class="inline-flex items-center justify-center px-8 py-4 rounded-2xl border-2 border-[#b01f44] text-[#b01f44] font-semibold text-base hover:bg-[#ffd2e1]/40 transition-all text-center">
                                    Lihat Layanan
                                </a>
                            </div>

                            <!-- TRUST STATS (HORIZONTAL SIDE-BY-SIDE WITH TOP BORDER ONLY) -->
                            <div class="pt-6 border-t border-[#e0bec1]/60 w-full flex flex-row items-center justify-between gap-2 sm:gap-4 text-center">
                                <div class="flex-1">
                                    <span class="font-serif-heading font-bold text-xl sm:text-2xl lg:text-3xl text-[#b01f44] block">
                                        4.9 <i class="fa-solid fa-star text-sm text-[#f59e0b]"></i>
                                    </span>
                                    <p class="text-xs text-[#594043] font-semibold mt-1">1,200+ Ulasan</p>
                                </div>
                                <div class="flex-1 border-l border-[#e0bec1]/40 px-2">
                                    <span class="font-serif-heading font-bold text-xl sm:text-2xl lg:text-3xl text-[#b01f44] block">100%</span>
                                    <p class="text-xs text-[#594043] font-semibold mt-1">Peralatan Steril</p>
                                </div>
                                <div class="flex-1 border-l border-[#e0bec1]/40 px-2">
                                    <span class="font-serif-heading font-bold text-xl sm:text-2xl lg:text-3xl text-[#b01f44] block">Flexi</span>
                                    <p class="text-xs text-[#594043] font-semibold mt-1">Salon & Home</p>
                                </div>
                            </div>

                        </div>

                        <!-- HERO RIGHT VISUAL CARD SHOWCASE -->
                        <div class="relative w-full">
                            <div class="relative mx-auto max-w-md lg:max-w-none w-full">
                                
                                <!-- DECORATIVE GLOW -->
                                <div class="absolute -inset-4 rounded-3xl bg-gradient-to-tr from-[#ffd2e1] to-[#f4dde1] blur-2xl opacity-60"></div>
                                
                                <!-- MAIN FEATURED CARD -->
                                <div class="relative bg-white rounded-2xl p-6 sm:p-8 border border-[#e0bec1] shadow-[0_10px_30px_rgba(37,24,28,0.06)] w-full">
                                    
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full bg-[#ffe8ed] text-[#b01f44] text-xs font-semibold">Treatment Terpopuler</span>
                                        </div>
                                        <span class="text-xs font-semibold text-[#785341] bg-[#ffdbcb]/40 px-3 py-1 rounded-full flex items-center gap-1">
                                            <i class="fa-solid fa-star text-[#f59e0b]"></i> 5.0 Rating
                                        </span>
                                    </div>

                                    <!-- SERVICE CARD PREVIEW -->
                                    <div class="p-5 rounded-2xl bg-[#fff8f8] border border-[#e0bec1]/60 mb-6">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="font-serif-heading font-bold text-lg text-[#25181c]">Hydrating Facial & Gel Nail Art</h3>
                                                <p class="text-xs text-[#594043] mt-1">Serum Organik + Manicure Kuku Custom</p>
                                            </div>
                                            <span class="font-serif-heading font-bold text-lg text-[#b01f44]">Rp 250rb</span>
                                        </div>
                                        <div class="mt-4 flex items-center gap-4 text-xs font-medium text-[#594043]">
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fa-regular fa-clock text-[#9b4054]"></i>
                                                60 Menit
                                            </span>
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fa-solid fa-location-dot text-[#9b4054]"></i>
                                                Salon / Dipanggil
                                            </span>
                                        </div>
                                    </div>

                                    <!-- REALTIME SLOT PREVIEW -->
                                    <div class="space-y-3">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-[#594043]">Jadwal Ketersediaan Slot Hari Ini</p>
                                        <div class="grid grid-cols-3 gap-2 text-center text-xs font-semibold">
                                            <div class="py-2 rounded-xl bg-[#ffe8ed] text-[#b01f44] border border-[#b01f44]/20">10.00 WIB</div>
                                            <div class="py-2 rounded-xl bg-[#ffe8ed] text-[#b01f44] border border-[#b01f44]/20">14.00 WIB</div>
                                            <div class="py-2 rounded-xl bg-[#ffe8ed] text-[#b01f44] border border-[#b01f44]/20">16.30 WIB</div>
                                        </div>
                                    </div>

                                    <!-- ACTION LINK -->
                                    <div class="mt-6 text-center">
                                        <a href="{{ route('user.bookings.create') }}" class="text-xs font-semibold text-[#b01f44] hover:underline inline-flex items-center gap-1">
                                            <span>Pilih jadwal & beautician favorit Anda</span>
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <!-- 3. LAYANAN UNGGULAN -->
            <section id="layanan" class="py-16 lg:py-24 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Katalog Treatment</span>
                        <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c] mt-2 mb-4">
                            Layanan Perawatan Favorit
                        </h2>
                        <p class="text-base text-[#594043]">
                            Dirancang dengan produk medis bersertifikasi & formula organik untuk hasil kecantikan maksimal.
                        </p>
                    </div>

                    <!-- GRID 4 KARTU LAYANAN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 w-full">
                        
                        <!-- CARD 1: FACIAL -->
                        <div class="bg-[#fff8f8] rounded-2xl p-6 border border-[#e0bec1]/70 hover:shadow-[0_8px_24px_rgba(37,24,28,0.08)] hover:-translate-y-1 transition-all flex flex-col justify-between w-full">
                            <div>
                                <div class="w-12 h-12 rounded-2xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center mb-6">
                                    <i class="fa-solid fa-spa text-xl"></i>
                                </div>
                                <h3 class="font-serif-heading font-bold text-xl text-[#25181c] mb-2">Facial & Skincare</h3>
                                <p class="text-xs text-[#594043] leading-relaxed mb-4">Deep cleansing, detox jerawat, serum anti-aging, dan totok wajah pencerah alami.</p>
                            </div>
                            <div class="pt-4 border-t border-[#e0bec1]/50 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-[#594043] block">Mulai dari</span>
                                    <span class="text-sm font-bold text-[#b01f44]">Rp 180.000</span>
                                </div>
                                <a href="{{ route('user.bookings.create') }}" class="px-4 py-2 rounded-xl bg-[#b01f44] text-white text-xs font-semibold hover:bg-[#910030] transition-colors">Booking</a>
                            </div>
                        </div>

                        <!-- CARD 2: HAIR -->
                        <div class="bg-[#fff8f8] rounded-2xl p-6 border border-[#e0bec1]/70 hover:shadow-[0_8px_24px_rgba(37,24,28,0.08)] hover:-translate-y-1 transition-all flex flex-col justify-between w-full">
                            <div>
                                <div class="w-12 h-12 rounded-2xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center mb-6">
                                    <i class="fa-solid fa-scissors text-xl"></i>
                                </div>
                                <h3 class="font-serif-heading font-bold text-xl text-[#25181c] mb-2">Hair Treatment</h3>
                                <p class="text-xs text-[#594043] leading-relaxed mb-4">Creambath buah, hair spa nutrisi tinggi, keratin smoothing, dan pewarnaan tren.</p>
                            </div>
                            <div class="pt-4 border-t border-[#e0bec1]/50 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-[#594043] block">Mulai dari</span>
                                    <span class="text-sm font-bold text-[#b01f44]">Rp 150.000</span>
                                </div>
                                <a href="{{ route('user.bookings.create') }}" class="px-4 py-2 rounded-xl bg-[#b01f44] text-white text-xs font-semibold hover:bg-[#910030] transition-colors">Booking</a>
                            </div>
                        </div>

                        <!-- CARD 3: NAIL ART -->
                        <div class="bg-[#fff8f8] rounded-2xl p-6 border border-[#e0bec1]/70 hover:shadow-[0_8px_24px_rgba(37,24,28,0.08)] hover:-translate-y-1 transition-all flex flex-col justify-between w-full">
                            <div>
                                <div class="w-12 h-12 rounded-2xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center mb-6">
                                    <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
                                </div>
                                <h3 class="font-serif-heading font-bold text-xl text-[#25181c] mb-2">Nail Art & Pedicure</h3>
                                <p class="text-xs text-[#594043] leading-relaxed mb-4">Custom gel polish art, manicure spa, kuku palsu extension, dan vitamin kuku.</p>
                            </div>
                            <div class="pt-4 border-t border-[#e0bec1]/50 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-[#594043] block">Mulai dari</span>
                                    <span class="text-sm font-bold text-[#b01f44]">Rp 120.000</span>
                                </div>
                                <a href="{{ route('user.bookings.create') }}" class="px-4 py-2 rounded-xl bg-[#b01f44] text-white text-xs font-semibold hover:bg-[#910030] transition-colors">Booking</a>
                            </div>
                        </div>

                        <!-- CARD 4: BODY SPA -->
                        <div class="bg-[#fff8f8] rounded-2xl p-6 border border-[#e0bec1]/70 hover:shadow-[0_8px_24px_rgba(37,24,28,0.08)] hover:-translate-y-1 transition-all flex flex-col justify-between w-full">
                            <div>
                                <div class="w-12 h-12 rounded-2xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center mb-6">
                                    <i class="fa-solid fa-hot-tub-person text-xl"></i>
                                </div>
                                <h3 class="font-serif-heading font-bold text-xl text-[#25181c] mb-2">Body Treatment & Spa</h3>
                                <p class="text-xs text-[#594043] leading-relaxed mb-4">Body scrub lulur rempah, pijat aromaterapi relaksasi, dan lulur susu murni.</p>
                            </div>
                            <div class="pt-4 border-t border-[#e0bec1]/50 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-[#594043] block">Mulai dari</span>
                                    <span class="text-sm font-bold text-[#b01f44]">Rp 220.000</span>
                                </div>
                                <a href="{{ route('user.bookings.create') }}" class="px-4 py-2 rounded-xl bg-[#b01f44] text-white text-xs font-semibold hover:bg-[#910030] transition-colors">Booking</a>
                            </div>
                        </div>

                    </div>

                </div>
            </section>

            <!-- 4. KENAPA PILIH YALIA (VALUE PROPOSITION) -->
            <section id="keunggulan" class="py-16 lg:py-24 bg-[#fff8f8]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Komitmen Kualitas</span>
                        <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c] mt-2 mb-4">
                            Kenapa Memilih Yalia Beauty?
                        </h2>
                        <p class="text-base text-[#594043]">
                            Kami menggabungkan standar kesehatan tinggi dengan pelayanan ramah nan profesional.
                        </p>
                    </div>

                    <!-- 4 VALUE PROPOSITIONS HORIZONTAL GRID -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 w-full">
                        
                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs text-left w-full">
                            <div class="w-12 h-12 rounded-2xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center mb-5">
                                <i class="fa-solid fa-user-nurse text-xl"></i>
                            </div>
                            <h3 class="font-serif-heading font-bold text-lg text-[#25181c] mb-2">Terapis Bersertifikasi</h3>
                            <p class="text-xs text-[#594043] leading-relaxed">Seluruh beautician berpengalaman min 3 tahun dan telah lulus ujian sertifikasi resmi.</p>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs text-left w-full">
                            <div class="w-12 h-12 rounded-2xl bg-[#d2fff0] text-[#2a3330] flex items-center justify-center mb-5 border border-[#7e9990]/40">
                                <i class="fa-solid fa-shield-virus text-xl text-[#059669]"></i>
                            </div>
                            <h3 class="font-serif-heading font-bold text-lg text-[#25181c] mb-2">100% Higienis & Steril</h3>
                            <p class="text-xs text-[#594043] leading-relaxed">Peralatan kuku & jarum sekali pakai, disinfeksi UV instrumen sebelum & sesudah perawatan.</p>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs text-left w-full">
                            <div class="w-12 h-12 rounded-2xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center mb-5">
                                <i class="fa-solid fa-bottle-droplet text-xl"></i>
                            </div>
                            <h3 class="font-serif-heading font-bold text-lg text-[#25181c] mb-2">Produk Premium BPOM</h3>
                            <p class="text-xs text-[#594043] leading-relaxed">Hanya menggunakan bahan skincare & kutek gel bersertifikasi BPOM yang aman bagi kulit.</p>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs text-left w-full">
                            <div class="w-12 h-12 rounded-2xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center mb-5">
                                <i class="fa-solid fa-calendar-check text-xl"></i>
                            </div>
                            <h3 class="font-serif-heading font-bold text-lg text-[#25181c] mb-2">Booking Online Tanpa Antri</h3>
                            <p class="text-xs text-[#594043] leading-relaxed">Pilih jam & beautician favorit Anda dari HP, konfirmasi instan via WhatsApp & Midtrans.</p>
                        </div>

                    </div>

                </div>
            </section>

            <!-- 5. GALERI HASIL KERJA -->
            <section id="galeri" class="py-16 lg:py-24 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Galeri Karya</span>
                        <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c] mt-2 mb-4">
                            Hasil Karya & Nail Art Custom
                        </h2>
                        <p class="text-base text-[#594043]">
                            Intip beberapa kreasi manicure, facial glow, dan penataan rambut hasil beautician kami.
                        </p>
                    </div>

                    <!-- GRID GALERI DINAMIS -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 w-full">
                        @forelse($galleryTreatments as $item)
                        <div class="relative overflow-hidden rounded-2xl bg-[#ffe8ed] aspect-square group border border-[#e0bec1]/50 w-full">
                            @if($item->images)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-[#ffd2e1] via-[#ffe8ed] to-[#ffdbcb] flex flex-col items-center justify-center p-4 text-center">
                                    <i class="fa-solid fa-sparkles text-3xl text-[#b01f44] mb-2 opacity-80"></i>
                                    <span class="font-serif-heading font-bold text-sm text-[#25181c]">{{ $item->name }}</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 flex flex-col justify-end p-4 bg-gradient-to-t from-[#25181c]/90 via-[#25181c]/30 to-transparent opacity-90 group-hover:opacity-100 transition-opacity">
                                <span class="text-xs font-bold text-white leading-tight font-serif-heading">{{ $item->name }}</span>
                                <span class="text-[11px] text-[#ffd2e1] mt-0.5 font-medium flex items-center justify-between">
                                    <span>{{ $item->category?->name ?? 'Treatment' }}</span>
                                    <span class="font-bold text-white">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</span>
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-8 text-center text-sm text-gray-500">
                            Belum ada galeri treatment tersedia.
                        </div>
                        @endforelse
                    </div>

                </div>
            </section>

            <!-- 6. TESTIMONI PELANGGAN -->
            <section id="testimoni" class="py-16 lg:py-24 bg-[#fff8f8]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Ulasan Asli</span>
                        <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c] mt-2 mb-4">
                            Apa Kata Pelanggan Kami?
                        </h2>
                        <p class="text-base text-[#594043]">
                            Pengalaman nyata pelanggan yang telah mencoba perawatan di salon maupun layanan panggilan rumah.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                        @forelse($approvedReviews as $review)
                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs flex flex-col justify-between w-full">
                            <div>
                                <div class="text-[#f59e0b] text-sm mb-3 space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="{{ $i <= $review->rating ? 'fa-solid' : 'fa-regular' }} fa-star"></i>
                                    @endfor
                                </div>
                                <p class="text-xs text-[#594043] leading-relaxed italic mb-4">
                                    "{{ $review->comment }}"
                                </p>
                                @if($review->photo)
                                    <div class="mb-4 rounded-xl overflow-hidden max-h-36">
                                        <img src="{{ Storage::url($review->photo) }}" alt="Foto Ulasan" class="w-full h-full object-cover">
                                    </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-3 pt-4 border-t border-[#e0bec1]/50">
                                <div class="w-9 h-9 rounded-full bg-[#ffd2e1] text-[#b01f44] font-bold text-xs flex items-center justify-center shrink-0 uppercase">
                                    {{ substr($review->Users?->name ?? 'U', 0, 2) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-xs font-bold text-[#25181c] block truncate">{{ $review->Users?->name ?? 'Pelanggan Yalia' }}</span>
                                    <span class="text-xs text-[#594043] truncate block">
                                        {{ $review->Bookings?->treatments?->first()?->name ?? 'Pelanggan Salon' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs flex flex-col justify-between w-full">
                            <div>
                                <div class="text-[#f59e0b] text-sm mb-3 space-x-1">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <p class="text-xs text-[#594043] leading-relaxed italic mb-4">
                                    "Sangat puas dengan layanan Home Visit nya! Mbak beautician-nya ramah banget, alat-alatnya steril. Hasil facialnya langsung kelihatan glowing segar."
                                </p>
                            </div>
                            <div class="flex items-center gap-3 pt-4 border-t border-[#e0bec1]/50">
                                <div class="w-9 h-9 rounded-full bg-[#ffd2e1] text-[#b01f44] font-bold text-xs flex items-center justify-center">SR</div>
                                <div>
                                    <span class="text-xs font-bold text-[#25181c] block">Siti Rahmawati</span>
                                    <span class="text-xs text-[#594043]">Pelanggan Home Visit</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs flex flex-col justify-between w-full">
                            <div>
                                <div class="text-[#f59e0b] text-sm mb-3 space-x-1">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <p class="text-xs text-[#594043] leading-relaxed italic mb-4">
                                    "Tempat salonnya wangi, estetik, dan menenangkan. Creambath & massage-nya juara bikin pegal-pegal langsung hilang. Pasti langganan!"
                                </p>
                            </div>
                            <div class="flex items-center gap-3 pt-4 border-t border-[#e0bec1]/50">
                                <div class="w-9 h-9 rounded-full bg-[#ffd2e1] text-[#b01f44] font-bold text-xs flex items-center justify-center">DP</div>
                                <div>
                                    <span class="text-xs font-bold text-[#25181c] block">Dian Permata</span>
                                    <span class="text-xs text-[#594043]">Pelanggan Studio Salon</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 border border-[#e0bec1] shadow-xs flex flex-col justify-between w-full">
                            <div>
                                <div class="text-[#f59e0b] text-sm mb-3 space-x-1">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <p class="text-xs text-[#594043] leading-relaxed italic mb-4">
                                    "Nail art nya rapi banget dan awet tahan 3 minggu lebih! Gak gampang mengelupas. Sistem booking online Midtrans juga praktis."
                                </p>
                            </div>
                            <div class="flex items-center gap-3 pt-4 border-t border-[#e0bec1]/50">
                                <div class="w-9 h-9 rounded-full bg-[#ffd2e1] text-[#b01f44] font-bold text-xs flex items-center justify-center">AL</div>
                                <div>
                                    <span class="text-xs font-bold text-[#25181c] block">Anisa Larasati</span>
                                    <span class="text-xs text-[#594043]">Gold Member VIP</span>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>

                </div>
            </section>

            <!-- 7. MEMBERSHIP / VIP TIER CARDS -->
            <section id="membership" class="py-16 lg:py-24 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Loyalty Rewards</span>
                        <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c] mt-2 mb-4">
                            Keanggotaan VIP Membership
                        </h2>
                        <p class="text-base text-[#594043]">
                            Kumpulkan poin setiap perawatan dan nikmati diskon khusus serta voucher gratis di setiap tier.
                        </p>
                    </div>

                    <!-- 3 VIP TIER CARDS WITH GRADIENTS -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                        
                        <!-- SILVER TIER CARD -->
                        <div class="rounded-2xl p-8 text-white shadow-lg flex flex-col justify-between relative overflow-hidden bg-[#0f172a] w-full" 
                             style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0369a1 70%, #0c4a6e 100%);">
                            <div>
                                <span class="text-xs uppercase tracking-widest text-[#e2e8f0] font-semibold">Tier Starter</span>
                                <h3 class="font-serif-heading font-bold text-2xl mt-1 mb-4 text-white">Silver Badge</h3>
                                <div class="text-2xl font-bold text-[#38bdf8] mb-6">Gratis saat daftar</div>
                                <ul class="space-y-3 text-xs text-slate-200 mb-8">
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#38bdf8]"></i> Kumpulkan 1 Poin tiap transaksi Rp 10rb</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#38bdf8]"></i> Diskon 5% di hari ulang tahun</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#38bdf8]"></i> Voucher diskon Rp 20rb booking pertama</li>
                                </ul>
                            </div>
                            <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-white/20 hover:bg-white/30 text-white font-semibold text-xs text-center border border-white/30 transition-all">Daftar Akun Baru</a>
                        </div>

                        <!-- GOLD TIER CARD -->
                        <div class="rounded-2xl p-8 text-white shadow-xl flex flex-col justify-between relative overflow-hidden transform md:-translate-y-2 border-2 border-[#f59e0b] bg-[#451a03] w-full" 
                             style="background: linear-gradient(135deg, #451a03 0%, #78350f 40%, #b45309 70%, #78350f 100%);">
                            <div class="absolute top-4 right-4 bg-[#f59e0b] text-[#451a03] text-xs font-bold uppercase px-3 py-1 rounded-full">Paling Populer</div>
                            <div>
                                <span class="text-xs uppercase tracking-widest text-[#fde68a] font-semibold">Tier Favorit</span>
                                <h3 class="font-serif-heading font-bold text-2xl mt-1 mb-4 text-white">Gold VIP</h3>
                                <div class="text-2xl font-bold text-[#fbbf24] mb-6">500 Poin Terkumpul</div>
                                <ul class="space-y-3 text-xs text-amber-100 mb-8">
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#fbbf24]"></i> Diskon 10% untuk semua perawatan salon</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#fbbf24]"></i> Prioritas bebas antri slot jam sibuk</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#fbbf24]"></i> Gratis Creambath di bulan ulang tahun</li>
                                </ul>
                            </div>
                            <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-[#f59e0b] hover:bg-[#d97706] text-[#451a03] font-bold text-xs text-center shadow-md transition-all">Gabung Gold VIP</a>
                        </div>

                        <!-- PURPLE VIP TIER CARD -->
                        <div class="rounded-2xl p-8 text-white shadow-lg flex flex-col justify-between relative overflow-hidden bg-[#1e1b4b] w-full" 
                             style="background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 40%, #6b21a8 70%, #2e1065 100%);">
                            <div>
                                <span class="text-xs uppercase tracking-widest text-[#e9d5ff] font-semibold">Tier Eksklusif</span>
                                <h3 class="font-serif-heading font-bold text-2xl mt-1 mb-4 text-white">Purple Elite VIP</h3>
                                <div class="text-2xl font-bold text-[#c084fc] mb-6">1.500 Poin Terkumpul</div>
                                <ul class="space-y-3 text-xs text-purple-200 mb-8">
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#c084fc]"></i> Diskon 15% tanpa minimum transaksi</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#c084fc]"></i> Layanan panggil Home Visit tanpa ongkos jalan</li>
                                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[#c084fc]"></i> Layanan VIP Room Privat khusus di Salon</li>
                                </ul>
                            </div>
                            <a href="{{ route('register') }}" class="w-full py-3 rounded-xl bg-white/20 hover:bg-white/30 text-white font-semibold text-xs text-center border border-white/30 transition-all">Gabung Elite VIP</a>
                        </div>

                    </div>

                </div>
            </section>

            <!-- 8. CARA BOOKING -->
            <section class="py-16 lg:py-24 bg-[#fff8f8]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Alur Praktis</span>
                        <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c] mt-2 mb-4">
                            3 Langkah Mudah Booking Online
                        </h2>
                        <p class="text-base text-[#594043]">
                            Pesan perawatan favorit Anda kurang dari 2 menit dari HP.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center w-full">
                        
                        <div class="bg-white rounded-2xl p-8 border border-[#e0bec1] shadow-xs w-full">
                            <div class="w-12 h-12 rounded-full bg-[#b01f44] text-white font-bold text-lg flex items-center justify-center mx-auto mb-6 shadow-sm">1</div>
                            <h3 class="font-serif-heading font-bold text-xl text-[#25181c] mb-2">Pilih Layanan & Lokasi</h3>
                            <p class="text-xs text-[#594043] leading-relaxed">Tentukan jenis perawatan (Facial, Hair, Nail) serta pilih datang ke Salon atau Dipanggil ke Rumah.</p>
                        </div>

                        <div class="bg-white rounded-2xl p-8 border border-[#e0bec1] shadow-xs w-full">
                            <div class="w-12 h-12 rounded-full bg-[#b01f44] text-white font-bold text-lg flex items-center justify-center mx-auto mb-6 shadow-sm">2</div>
                            <h3 class="font-serif-heading font-bold text-xl text-[#25181c] mb-2">Pilih Jadwal & Beautician</h3>
                            <p class="text-xs text-[#594043] leading-relaxed">Lihat slot jam ketersediaan realtime dan pilih beautician favorit langganan Anda.</p>
                        </div>

                        <div class="bg-white rounded-2xl p-8 border border-[#e0bec1] shadow-xs w-full">
                            <div class="w-12 h-12 rounded-full bg-[#b01f44] text-white font-bold text-lg flex items-center justify-center mx-auto mb-6 shadow-sm">3</div>
                            <h3 class="font-serif-heading font-bold text-xl text-[#25181c] mb-2">Konfirmasi & Pembayaran</h3>
                            <p class="text-xs text-[#594043] leading-relaxed">Bayar praktis via QRIS/Transfer Midtrans atau pilih opsi Cash saat perawatan selesai.</p>
                        </div>

                    </div>

                </div>
            </section>

            <!-- 9. LOKASI & JAM OPERASIONAL -->
            <section id="lokasi" class="py-16 lg:py-24 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center w-full">
                        
                        <!-- LEFT COLUMN: LOKASI INFO -->
                        <div class="w-full space-y-6">
                            <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Kunjungi Kami</span>
                            <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c]">
                                Lokasi Studio & Jam Operasional
                            </h2>
                            <p class="text-sm text-[#594043] leading-relaxed">
                                Studio Salon Yalia Beauty berlokasi strategis di pusat kota dengan area parkir luas dan suasana interior nyaman.
                            </p>

                            <div class="space-y-4 pt-4 border-t border-[#e0bec1]/60 w-full">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-location-dot text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-[#25181c]">Alamat Studio Salon</h4>
                                        <p class="text-xs text-[#594043] mt-0.5">Jl. Beauty Beauty No. 88, Kota Beauty, Indonesia</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-[#ffd2e1] text-[#b01f44] flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-clock text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-[#25181c]">Jam Operasional</h4>
                                        <p class="text-xs text-[#594043] mt-0.5">Senin – Minggu: 09.00 – 20.00 WIB (Buka Setiap Hari)</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-[#d2fff0] text-[#2a3330] flex items-center justify-center shrink-0 border border-[#7e9990]/40">
                                        <i class="fa-brands fa-whatsapp text-lg text-[#059669]"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-[#25181c]">Bantuan WhatsApp Langsung</h4>
                                        <p class="text-xs text-[#594043] mt-0.5">+62 822-2702-3362 (Customer Service Quick Response)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4">
                                <a href="https://wa.me/6282227023362" target="_blank" rel="noopener" 
                                   class="inline-flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-[#059669] hover:bg-[#047857] text-white font-bold text-xs shadow-md transition-all"
                                   style="background-color: #059669; color: #ffffff;">
                                    <i class="fa-brands fa-whatsapp text-base"></i>
                                    <span>Tanya Customer Service via WhatsApp</span>
                                    <i class="fa-solid fa-arrow-right ml-1"></i>
                                </a>
                            </div>

                        </div>

                        <!-- RIGHT COLUMN: LEAFLET JS MAP CONTAINER -->
                        <div class="w-full">
                            <div class="bg-[#fff8f8] rounded-2xl p-4 sm:p-6 border border-[#e0bec1] shadow-md w-full relative">
                                <div id="yalia-map" class="w-full h-80 lg:h-[420px] rounded-xl border border-[#e0bec1]/60 shadow-inner overflow-hidden z-0"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const lat = {{ (float) config('booking.salon.latitude', -7.4563287) }};
                    const lng = {{ (float) config('booking.salon.longitude', 110.567513) }};
                    const address = {{ \Illuminate\Support\Js::from(config('booking.salon.address', 'GHV9+F2 Candi, Kabupaten Boyolali, Jawa Tengah')) }};

                    const map = L.map('yalia-map', {
                        ariaLabel: 'Peta Lokasi Studio Salon Yalia Beauty',
                        keyboard: true
                    }).setView([lat, lng], 16);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a>'
                    }).addTo(map);

                    const customIcon = L.divIcon({
                        className: 'custom-leaflet-marker',
                        html: `<div class="w-10 h-10 rounded-full bg-[#b01f44] text-white flex items-center justify-center text-lg shadow-lg border-2 border-white transform -translate-x-1/2 -translate-y-1/2">
                                <i class="fa-solid fa-location-dot"></i>
                               </div>`,
                        iconSize: [40, 40],
                        iconAnchor: [20, 20],
                        popupAnchor: [0, -20]
                    });

                    const marker = L.marker([lat, lng], {
                        icon: customIcon,
                        alt: "Studio Salon Yalia Beauty",
                        title: "Studio Salon Yalia Beauty"
                    }).addTo(map);

                    const gmapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

                    marker.bindPopup(`
                        <div class="p-2 text-center font-sans">
                            <h4 class="font-serif-heading font-bold text-sm text-[#25181c] mb-1" style="color:#25181c; font-family:'Playfair Display',serif;">Studio Salon Yalia Beauty</h4>
                            <p class="text-xs text-[#594043] leading-relaxed mb-2" style="color:#594043;">${address}</p>
                            <a href="${gmapsUrl}" target="_blank" rel="noopener" class="inline-block px-3 py-1.5 rounded-lg bg-[#b01f44] text-white text-xs font-bold hover:bg-[#8f1937] transition-colors" style="background-color:#b01f44; color:#ffffff;">
                                Buka Petunjuk Arah di Google Maps &rarr;
                            </a>
                        </div>
                    `).openPopup();
                });
            </script>

            <!-- 10. FAQ ACCORDION -->
            <section id="faq" class="py-16 lg:py-24 bg-[#fff8f8]">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                    
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <span class="text-xs uppercase tracking-widest font-semibold text-[#9b4054]">Pertanyaan Umum</span>
                        <h2 class="font-serif-heading font-bold text-3xl sm:text-4xl text-[#25181c] mt-2 mb-4">
                            Pertanyaan Sering Diajukan
                        </h2>
                        <p class="text-base text-[#594043]">
                            Temukan jawaban seputar layanan, jadwal, dan metode pembayaran kami.
                        </p>
                    </div>

                    <!-- ACCORDION CONTAINER -->
                    <div x-data="{ activeFaq: 1 }" class="space-y-4 w-full">
                        
                        <!-- FAQ ITEM 1 -->
                        <div class="bg-white rounded-2xl border border-[#e0bec1] overflow-hidden shadow-xs w-full">
                            <button @click="activeFaq = (activeFaq === 1 ? null : 1)" 
                                    type="button"
                                    class="w-full p-6 text-left font-serif-heading font-bold text-lg text-[#25181c] flex items-center justify-between focus:outline-none">
                                <span>Bagaimana cara jadwal ulang (reschedule) booking saya?</span>
                                <i class="fa-solid" :class="activeFaq === 1 ? 'fa-minus text-[#b01f44]' : 'fa-plus text-[#b01f44]'"></i>
                            </button>
                            <div x-show="activeFaq === 1" x-cloak class="px-6 pb-6 text-xs text-[#594043] leading-relaxed border-t border-[#e0bec1]/40 pt-4">
                                Anda dapat melakukan reschedule jadwal minimal 3 jam sebelum jam perawatan melalui menu Dashboard Pengguna di bagian "Riwayat Booking" atau menghubungi Customer Service kami via WhatsApp.
                            </div>
                        </div>

                        <!-- FAQ ITEM 2 -->
                        <div class="bg-white rounded-2xl border border-[#e0bec1] overflow-hidden shadow-xs w-full">
                            <button @click="activeFaq = (activeFaq === 2 ? null : 2)" 
                                    type="button"
                                    class="w-full p-6 text-left font-serif-heading font-bold text-lg text-[#25181c] flex items-center justify-between focus:outline-none">
                                <span>Apakah peralatan kuku dan kecantikan dijamin steril?</span>
                                <i class="fa-solid" :class="activeFaq === 2 ? 'fa-minus text-[#b01f44]' : 'fa-plus text-[#b01f44]'"></i>
                            </button>
                            <div x-show="activeFaq === 2" x-cloak class="px-6 pb-6 text-xs text-[#594043] leading-relaxed border-t border-[#e0bec1]/40 pt-4">
                                Ya, 100%! Semua instrumen kuku logam melewati proses sterilisasi autoklaf & sinar UV medis. Handuk dan kuas yang digunakan selalu dicuci bersih dan dikemas steril per pelanggan.
                            </div>
                        </div>

                        <!-- FAQ ITEM 3 -->
                        <div class="bg-white rounded-2xl border border-[#e0bec1] overflow-hidden shadow-xs w-full">
                            <button @click="activeFaq = (activeFaq === 3 ? null : 3)" 
                                    type="button"
                                    class="w-full p-6 text-left font-serif-heading font-bold text-lg text-[#25181c] flex items-center justify-between focus:outline-none">
                                <span>Metode pembayaran apa saja yang didukung?</span>
                                <i class="fa-solid" :class="activeFaq === 3 ? 'fa-minus text-[#b01f44]' : 'fa-plus text-[#b01f44]'"></i>
                            </button>
                            <div x-show="activeFaq === 3" x-cloak class="px-6 pb-6 text-xs text-[#594043] leading-relaxed border-t border-[#e0bec1]/40 pt-4">
                                Kami mendukung pembayaran otomatis online melalui Midtrans (QRIS GoPay/OVO/ShopeePay, Transfer Bank BCA/Mandiri/BRI, Kartu Kredit) serta opsi bayar Cash langsung di salon/saat home visit.
                            </div>
                        </div>

                        <!-- FAQ ITEM 4 -->
                        <div class="bg-white rounded-2xl border border-[#e0bec1] overflow-hidden shadow-xs w-full">
                            <button @click="activeFaq = (activeFaq === 4 ? null : 4)" 
                                    type="button"
                                    class="w-full p-6 text-left font-serif-heading font-bold text-lg text-[#25181c] flex items-center justify-between focus:outline-none">
                                <span>Bagaimana syarat dan jangkauan area untuk Home Visit?</span>
                                <i class="fa-solid" :class="activeFaq === 4 ? 'fa-minus text-[#b01f44]' : 'fa-plus text-[#b01f44]'"></i>
                            </button>
                            <div x-show="activeFaq === 4" x-cloak class="px-6 pb-6 text-xs text-[#594043] leading-relaxed border-t border-[#e0bec1]/40 pt-4">
                                Layanan Home Visit mencakup area radius hingga 15 km dari Studio Salon. Beautician kami membawa seluruh peralatan lengkap termasuk lampu & alas steril, Anda tinggal duduk santai di rumah.
                            </div>
                        </div>

                    </div>

                </div>
            </section>

        </main>

        <!-- FOOTER -->
        <footer class="bg-[#25181c] text-[#fff8f8] py-12 border-t border-[#594043]" style="background-color: #25181c; color: #ffffff;">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8 w-full">
                
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('logo/yalia-logos.svg') }}" alt="Yalia Beauty Salon Logo" class="h-16 w-auto object-contain">
                    </div>
                    <p class="text-xs text-[#ffd2e1]/90 max-w-sm leading-relaxed mb-4" style="color: rgba(255, 210, 225, 0.9);">
                        Platform booking perawatan kecantikan salon dan pemanggilan beautician ke rumah terpercaya.
                    </p>
                    <p class="text-xs text-white/70" style="color: rgba(255, 255, 255, 0.7);">
                        © {{ date('Y') }} Yalia Beauty. All rights reserved.
                    </p>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#ffd2e1] mb-4" style="color: #ffd2e1;">Tautan Pintas</h4>
                    <ul class="space-y-2 text-xs text-white/90" style="color: rgba(255, 255, 255, 0.9);">
                        <li><a href="#layanan" class="hover:text-white transition-colors">Menu Treatment</a></li>
                        <li><a href="#keunggulan" class="hover:text-white transition-colors">Kenapa Yalia</a></li>
                        <li><a href="#galeri" class="hover:text-white transition-colors">Galeri Karya</a></li>
                        <li><a href="#membership" class="hover:text-white transition-colors">Membership VIP</a></li>
                        <li><a href="#faq" class="hover:text-white transition-colors">FAQ Pertanyaan</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-[#ffd2e1] mb-4" style="color: #ffd2e1;">Hubungi Kami</h4>
                    <ul class="space-y-2 text-xs text-white/90" style="color: rgba(255, 255, 255, 0.9);">
                        <li><i class="fa-solid fa-location-dot mr-2 text-[#ffd2e1]"></i> Studio Salon Yalia Beauty</li>
                        <li><i class="fa-brands fa-whatsapp mr-2 text-[#ffd2e1]"></i> WhatsApp: +62 822-2702-3362</li>
                        <li><i class="fa-solid fa-clock mr-2 text-[#ffd2e1]"></i> Operasional: 09.00 - 20.00 WIB</li>
                    </ul>
                </div>

            </div>
        </footer>

    </body>
</html>
