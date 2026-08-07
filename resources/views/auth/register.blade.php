<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Yalia Beauty</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
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
                        'sm': '8px',
                    },
                    backgroundImage: {
                        'gradient-brand': 'linear-gradient(135deg, #FF6B8A, #E91E63)',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #fdf5f6;
            font-family: 'DM Sans', sans-serif;
            overflow-x: hidden;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(91, 58, 41, 0.1);
        }
        .input-focus:focus {
            border-color: #f45472;
            box-shadow: 0 0 0 4px rgba(244, 84, 114, 0.1);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-pink-50 to-rose-100 px-4 py-12 relative overflow-hidden">

    <!-- Background Decorative Elements -->
    <div class="fixed top-[-10%] right-[-10%] w-[50%] h-[50%] bg-accent-clear opacity-30 blur-[120px] rounded-full"></div>
    <div class="fixed bottom-[-10%] left-[-10%] w-[50%] h-[50%] bg-accent-soft opacity-30 blur-[120px] rounded-full"></div>

    <div id="register-container" class="w-full max-w-md relative z-10 opacity-0">
        {{-- Logo & Judul --}}
        <div class="text-center mb-8">
            <a href="#" class="inline-flex items-center gap-3 mb-4 group">
                <div class="w-12 h-12 rounded-full bg-gradient-brand p-0.5 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <div class="w-full h-full rounded-full bg-white flex items-center justify-center">
                        <span class="text-xl">🌸</span>
                    </div>
                </div>
                <span class="text-2xl font-display font-extrabold bg-gradient-brand bg-clip-text text-transparent">Yalia Beauty</span>
            </a>
            <h1 class="text-3xl font-display font-extrabold text-dark tracking-tight">Buat Akun Baru</h1>
            <p class="text-gray-500 text-sm mt-2">Daftar sekarang untuk mulai perawatan!</p>
        </div>

        {{-- Card Form Register --}}
        <div class="glass-card rounded-2xl shadow-2xl p-8 md:p-10">

            {{-- Tombol Login Google --}}
            <a href={{  route('auth.google') }}
               class="flex items-center justify-center gap-3 w-full border border-gray-200 bg-white/50 rounded-xl py-3.5 px-4 text-dark font-bold hover:bg-white hover:shadow-md transition-all duration-300 mb-6 group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span class="text-sm">Daftar dengan Google</span>
            </a>

            {{-- Divider --}}
            <div class="relative mb-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-[10px] uppercase tracking-[0.2em] font-bold">
                    <span class="px-4 bg-transparent text-gray-400">Atau Formulir</span>
                </div>
            </div>

            {{-- Form Register --}}
            <form action="#"
                  x-data="{ isLoading: false }"
                  @submit.prevent="isLoading = true; setTimeout(() => isLoading = false, 2000)"
                  class="space-y-4">

                {{-- Nama Lengkap --}}
                <div>
                    <label for="name" class="block text-xs font-bold uppercase tracking-widest text-accent mb-2 ml-1">
                        Nama Lengkap
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        required
                        placeholder="Masukkan nama lengkap"
                        class="w-full rounded-xl border border-gray-200 bg-white/50 px-4 py-3 text-sm focus:outline-none input-focus transition-all duration-300"
                    >
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold uppercase tracking-widest text-accent mb-2 ml-1">
                        Email Address
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        placeholder="email@contoh.com"
                        class="w-full rounded-xl border border-gray-200 bg-white/50 px-4 py-3 text-sm focus:outline-none input-focus transition-all duration-300"
                    >
                </div>

                {{-- Password --}}
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-xs font-bold uppercase tracking-widest text-accent mb-2 ml-1">
                        Password
                    </label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            required
                            placeholder="Buat password"
                            class="w-full rounded-xl border border-gray-200 bg-white/50 px-4 py-3 text-sm focus:outline-none input-focus transition-all duration-300 pr-11"
                        >
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-accent transition-colors p-1.5">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit"
                        :disabled="isLoading"
                        class="w-full bg-gradient-brand text-white font-display font-bold rounded-xl py-4 shadow-lg shadow-accent/20 hover:shadow-accent/40 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:translate-y-0 transition-all duration-300 flex items-center justify-center gap-2 uppercase tracking-widest text-xs mt-2">
                    <svg x-show="isLoading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" x-cloak>
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    <span x-text="isLoading ? 'Memproses...' : 'Daftar Sekarang'">Daftar Sekarang</span>
                </button>
            </form>
        </div>

        {{-- Link ke Login --}}
        <p class="text-center text-sm text-gray-600 mt-8">
            Sudah punya akun?
            <a href="#" class="text-accent font-bold hover:text-accent-deep transition-colors ml-1">
                Masuk sekarang
            </a>
        </p>

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

        // Entrance Animation
        animate(
            "#register-container",
            { opacity: [0, 1], y: [40, 0] },
            { 
                duration: 1, 
                easing: [0.22, 1, 0.36, 1] 
            }
        );

        // Staggered Entrance for Form Elements
        animate(
            ".text-center > *, .glass-card > *",
            { opacity: [0, 1], y: [20, 0] },
            { 
                delay: stagger(0.1, { start: 0.4 }),
                duration: 0.6
            }
        );
    </script>
</body>
</html>
