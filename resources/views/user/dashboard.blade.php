<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Yalia Beauty</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Motion.dev (Motion One) & Lenis -->
    <script src="https://cdn.jsdelivr.net/npm/motion@11.11.13/dist/motion.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        accent: '#f45472',
                        'accent-soft': '#ff8fa4',
                        'accent-clear': '#ffd2e1',
                        'accent-deep': '#E91E63',
                        dark: '#2b1a1f',
                        brown: '#5b3a29',
                        'bg-main': '#fdf5f6',
                        'off-white': '#fff8f9',
                    },
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
                        sans: ['"DM Sans"', 'sans-serif'],
                    },
                    borderRadius: {
                        'xl': '12px',
                        '2xl': '24px',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #fdf5f6;
            font-family: 'DM Sans', sans-serif;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(91, 58, 41, 0.05);
        }
    </style>
</head>
<body class="min-h-screen pb-12">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" id="dashboard-content">

        {{-- Header Sambutan --}}
        <div class="mb-8 opacity-0" id="header-section">
            <div class="flex items-center gap-4">
                {{-- Avatar --}}
                <div class="relative">
                    <div class="w-14 h-14 rounded-full bg-rose-100 flex items-center justify-center border-2 border-rose-200">
                        <span class="text-xl font-bold text-rose-500">Y</span>
                    </div>
                    {{-- Badge membership --}}
                    <span class="absolute -bottom-1 -right-1 text-[10px] px-2 py-0.5 rounded-full font-bold bg-purple-100 text-purple-700 uppercase tracking-wider">
                        Platinum
                    </span>
                </div>

                <div>
                    <h1 class="text-2xl font-display font-extrabold text-dark">
                        Halo, Yalia! 👋
                    </h1>
                    <p class="text-gray-500 text-sm">
                        Selamat datang di Yalia Beauty — Rabu, 7 Agustus 2026
                    </p>
                </div>

                {{-- Tombol Booking Baru --}}
                <div class="ml-auto hidden sm:block">
                    <a href="#"
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-bold rounded-xl px-6 py-3 shadow-lg shadow-rose-200 transition-all duration-300 hover:-translate-y-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Booking Sekarang
                    </a>
                </div>
            </div>
        </div>

        {{-- Kartu Statistik --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" id="stats-section">
            {{-- Total Booking --}}
            <div class="glass-card rounded-2xl p-5 shadow-sm opacity-0 stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-lg">📋</div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Booking</span>
                </div>
                <p class="text-3xl font-display font-extrabold text-dark">24</p>
            </div>

            {{-- Booking Selesai --}}
            <div class="glass-card rounded-2xl p-5 shadow-sm opacity-0 stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-lg">✅</div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Selesai</span>
                </div>
                <p class="text-3xl font-display font-extrabold text-dark">20</p>
            </div>

            {{-- Total Poin --}}
            <div class="glass-card rounded-2xl p-5 shadow-sm opacity-0 stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center text-lg">⭐</div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Poin</span>
                </div>
                <p class="text-3xl font-display font-extrabold text-dark">1,250</p>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="glass-card rounded-2xl p-5 shadow-sm opacity-0 stat-card">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-lg">💸</div>
                    <span class="text-xs text-gray-500 font-bold uppercase tracking-wider">Spending</span>
                </div>
                <p class="text-xl font-display font-extrabold text-dark">Rp 4.500.000</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Booking Aktif + Riwayat --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Booking Aktif --}}
                <div class="glass-card rounded-2xl shadow-sm overflow-hidden opacity-0" id="active-booking">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-display font-extrabold text-dark flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                            Booking Aktif
                        </h2>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-xs">1 booking</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <div class="px-6 py-6 hover:bg-white/50 transition-colors">
                            <div class="flex items-start gap-4">
                                {{-- Tanggal --}}
                                <div class="flex-shrink-0 w-14 text-center">
                                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">AGU</div>
                                    <div class="text-3xl font-display font-extrabold text-rose-500 leading-none">07</div>
                                    <div class="text-[10px] font-bold text-gray-400 mt-1">14:00</div>
                                </div>

                                {{-- Detail --}}
                                <div class="flex-1 min-w-0">
                                    <p class="font-display font-extrabold text-dark text-base">BK-20260807-001</p>
                                    <p class="text-gray-500 text-sm mt-1">
                                        Nail Art Express, Spa Pedicure
                                    </p>
                                    <p class="text-gray-400 text-xs mt-1">Beautician: Sarah Amelia</p>
                                </div>

                                {{-- Status Badge --}}
                                <div class="flex-shrink-0">
                                    <span class="inline-block text-[10px] px-3 py-1 rounded-full font-bold uppercase tracking-wider bg-blue-50 text-blue-600">
                                        Dikonfirmasi
                                    </span>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-lg font-display font-extrabold text-rose-600">
                                    Rp 350.000
                                </span>
                                <a href="#"
                                   class="text-xs font-bold text-rose-500 hover:text-rose-700 uppercase tracking-widest transition-colors">
                                    Lihat Detail →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Riwayat Booking --}}
                <div class="glass-card rounded-2xl shadow-sm overflow-hidden opacity-0" id="history-booking">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="font-display font-extrabold text-dark">Riwayat Booking</h2>
                        <a href="#"
                           class="text-[10px] font-bold text-rose-500 hover:text-rose-700 uppercase tracking-widest transition-colors">
                            Lihat Semua →
                        </a>
                    </div>

                    <div class="divide-y divide-gray-50">
                        <div class="px-6 py-4 flex items-center gap-4 hover:bg-white/50 transition-colors">
                            {{-- Ikon Treatment --}}
                            <div class="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 bg-rose-50 flex items-center justify-center text-xl">
                                💆
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-dark truncate">
                                    Facial Glow, Eye Treatment
                                </p>
                                <p class="text-xs text-gray-400 font-medium">
                                    01 Agu 2026
                                </p>
                            </div>

                            <div class="text-right flex-shrink-0">
                                <span class="block text-sm font-display font-extrabold text-dark">
                                    Rp 450.000
                                </span>
                                <span class="text-[10px] font-bold uppercase text-green-500 tracking-wider">
                                    ✓ Selesai
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Membership + Voucher --}}
            <div class="space-y-6 opacity-0" id="right-column">

                {{-- Card Membership --}}
                <div class="rounded-2xl p-6 text-white relative overflow-hidden bg-gradient-to-br from-purple-500 to-purple-700 shadow-xl shadow-purple-200">
                    {{-- Dekorasi --}}
                    <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white opacity-10 -translate-y-8 translate-x-8"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 rounded-full bg-white opacity-5 translate-y-8 -translate-x-8"></div>

                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] opacity-80">Membership</span>
                            <span class="text-2xl">💎</span>
                        </div>
                        <h3 class="text-2xl font-display font-extrabold mb-1">Platinum</h3>
                        <p class="text-xs opacity-80 font-medium">20 booking selesai</p>

                        <div class="mt-6">
                            <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider mb-2">
                                <span>Status Tertinggi</span>
                                <span>MAX</span>
                            </div>
                            <div class="w-full bg-white/20 rounded-full h-1.5">
                                <div class="bg-white rounded-full h-1.5" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Voucher Card --}}
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="font-display font-extrabold text-dark mb-4">Voucher Saya</h3>
                    <div class="space-y-3">
                        <div class="p-3 border-2 border-dashed border-rose-200 rounded-xl bg-rose-50/30">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-xs font-bold text-rose-500 uppercase tracking-widest">Diskon 20%</p>
                                    <p class="text-[10px] text-gray-400 font-medium">Berakhir dalam 2 hari</p>
                                </div>
                                <button class="text-[10px] font-bold bg-rose-500 text-white px-3 py-1.5 rounded-lg uppercase tracking-wider">Salin</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lenis
        const lenis = new Lenis();
        function raf(time) {
            lenis.raf(time);
            requestAnimationFrame(raf);
        }
        requestAnimationFrame(raf);

        // Motion.dev Animations
        const { animate, stagger } = Motion;

        // Entrance Sequence
        animate("#header-section", { opacity: [0, 1], y: [20, 0] }, { duration: 0.8 });
        
        animate(".stat-card", 
            { opacity: [0, 1], y: [20, 0] }, 
            { delay: stagger(0.1, { start: 0.2 }), duration: 0.5 }
        );

        animate("#active-booking", { opacity: [0, 1], x: [-20, 0] }, { delay: 0.4, duration: 0.6 });
        animate("#history-booking", { opacity: [0, 1], x: [-20, 0] }, { delay: 0.6, duration: 0.6 });
        animate("#right-column", { opacity: [0, 1], x: [20, 0] }, { delay: 0.5, duration: 0.6 });
    </script>
</body>
</html>
