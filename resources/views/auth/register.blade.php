{{--
    Halaman registrasi akun pelanggan.
    Layout: split-screen (visual di kiri, form glass-card di kanan),
    dilengkapi opsi daftar via Google (Socialite) dan form manual
    (name, email, phone, password) sesuai skema tabel users di PRD.

    Ukuran dioptimalkan untuk layar laptop (lg breakpoint ke atas):
    card lebih ramping, input lebih pendek, spacing lebih rapat,
    sehingga seluruh form muat tanpa scroll di viewport ~800px tinggi.
--}}
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Daftar Akun | Yalia Beauty</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Work+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { background-color: #fff8f9; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(176, 31, 68, 0.08);
        }
        .organic-blob {
            position: absolute;
            z-index: -1;
            filter: blur(60px);
            opacity: 0.4;
            border-radius: 100%;
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen font-sans text-on-surface antialiased overflow-x-hidden bg-[#fff8f9]">
<main class="flex min-h-screen">

    {{-- Sisi Kiri: Visual --}}
    <section class="hidden lg:flex lg:w-2/5 xl:w-1/2 relative overflow-hidden bg-[#d23b5b]">
        <div class="absolute inset-0 z-0">
            <img
                class="w-full h-full object-cover opacity-90"
                src="{{ asset('images/register.webp') }}"
                alt="Yalia Beauty treatment ambiance"
            >
        </div>
        <div class="relative z-10 w-full h-full flex flex-col justify-between p-8 xl:p-10 bg-gradient-to-t from-[#b01f44]/40 via-transparent to-transparent">
            <div class="flex items-center gap-3">
                <span class="font-[Playfair_Display] text-2xl font-bold text-white tracking-tight">Yalia Beauty</span>
            </div>
            <div class="max-w-sm">
                <h2 class="font-[Playfair_Display] text-[32px] leading-[1.2] font-semibold text-white mb-3">Elevate Your Ritual.</h2>
                <p class="font-sans text-sm text-white/90">Bergabunglah dengan komunitas eksklusif kami dan nikmati treatment kecantikan yang dipersonalisasi sesuai kebutuhanmu.</p>
            </div>
        </div>
    </section>

    {{-- Sisi Kanan: Form Registrasi --}}
    <section class="w-full lg:w-3/5 xl:w-1/2 flex items-center justify-center p-4 md:p-6 relative">
        <div class="organic-blob bg-[#ffd2e1] w-48 h-48 top-[-10%] right-[-10%]"></div>
        <div class="organic-blob bg-[#ffb2ba] w-56 h-56 bottom-[-15%] left-[-10%]"></div>

        <div class="w-full max-w-[380px] glass-card rounded-[24px] p-6 md:p-7 shadow-[0_20px_50px_rgba(43,26,31,0.08)]">
            <div class="mb-5 text-center lg:text-left">
                <div class="lg:hidden flex justify-center mb-4">
                    <span class="font-[Playfair_Display] text-xl font-bold text-[#b01f44]">Yalia Beauty</span>
                </div>
                <h1 class="font-[Playfair_Display] text-2xl font-semibold text-[#2b1a1f] mb-1">Buat Akun Baru</h1>
                <p class="text-xs text-[#594043]">Daftar sekarang untuk mulai perawatan.</p>
            </div>

            {{-- Daftar dengan Google --}}
            <a
                href="{{ route('auth.google') }}"
                class="flex items-center justify-center gap-2.5 w-full border border-[#e0bec1] bg-white/60 rounded-lg py-2.5 px-4 text-[#2b1a1f] font-semibold hover:bg-white hover:shadow-md transition-all duration-300 mb-4"
            >
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <span class="text-xs">Daftar dengan Google</span>
            </a>

            {{-- Divider --}}
            <div class="relative mb-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-[#e0bec1]"></div>
                </div>
                <div class="relative flex justify-center text-[9px] uppercase tracking-[0.2em] font-bold">
                    <span class="px-3 bg-transparent text-[#8d7072]">Atau Formulir</span>
                </div>
            </div>

            {{-- Pesan error umum (mis. gagal login Google) --}}
            @if (session('error'))
                <div class="mb-4 rounded-lg bg-[#ffdad6] text-[#93000a] text-xs px-3 py-2">
                    {{ session('error') }}
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('register') }}"
                x-data="{ isLoading: false, showPassword: false, showPasswordConfirm: false }"
                @submit="isLoading = true"
                class="space-y-3.5"
            >
                @csrf

                {{-- Nama Lengkap --}}
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[#2b1a1f] ml-0.5" for="name">Nama Lengkap</label>
                    <div class="relative">
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name') }}"
                            required
                            autofocus
                            autocomplete="name"
                            placeholder="Nama lengkap kamu"
                            class="w-full h-10 bg-white/60 border border-[#e0bec1] rounded-lg px-3.5 pr-10 text-sm font-sans focus:ring-2 focus:ring-[#f45472]/20 focus:border-[#f45472] transition-all duration-300 @error('name') border-red-400 @enderror"
                        >
                        <span class="material-symbols-outlined absolute right-3 top-2.5 text-[18px] text-[#594043]">person</span>
                    </div>
                    @error('name')
                        <p class="text-[11px] text-red-500 ml-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[#2b1a1f] ml-0.5" for="email">Alamat Email</label>
                    <div class="relative">
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="email@contoh.com"
                            class="w-full h-10 bg-white/60 border border-[#e0bec1] rounded-lg px-3.5 pr-10 text-sm font-sans focus:ring-2 focus:ring-[#f45472]/20 focus:border-[#f45472] transition-all duration-300 @error('email') border-red-400 @enderror"
                        >
                        <span class="material-symbols-outlined absolute right-3 top-2.5 text-[18px] text-[#594043]">mail</span>
                    </div>
                    @error('email')
                        <p class="text-[11px] text-red-500 ml-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor Telepon --}}
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[#2b1a1f] ml-0.5" for="phone">Nomor Telepon</label>
                    <div class="relative">
                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            value="{{ old('phone') }}"
                            required
                            autocomplete="tel"
                            placeholder="08xxxxxxxxxx"
                            pattern="^(\+62|62|0)8[1-9][0-9]{6,10}$"
                            title="Gunakan format: 08xx, +628xx, atau 628xx"
                            class="w-full h-10 bg-white/60 border border-[#e0bec1] rounded-lg px-3.5 pr-10 text-sm font-sans focus:ring-2 focus:ring-[#f45472]/20 focus:border-[#f45472] transition-all duration-300 @error('phone') border-red-400 @enderror"
                        >
                        <span class="material-symbols-outlined absolute right-3 top-2.5 text-[18px] text-[#594043]">call</span>
                    </div>
                    @error('phone')
                        <p class="text-[11px] text-red-500 ml-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[#2b1a1f] ml-0.5" for="password">Password</label>
                    <div class="relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="Minimal 8 karakter"
                            class="w-full h-10 bg-white/60 border border-[#e0bec1] rounded-lg px-3.5 pr-10 text-sm font-sans focus:ring-2 focus:ring-[#f45472]/20 focus:border-[#f45472] transition-all duration-300 @error('password') border-red-400 @enderror"
                        >
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-2.5 text-[#594043] hover:text-[#b01f44] transition-colors">
                            <span class="material-symbols-outlined text-[18px]" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    <p class="text-[10px] text-[#8d7072] ml-0.5">Kombinasi huruf besar, kecil, &amp; angka.</p>
                    @error('password')
                        <p class="text-[11px] text-red-500 ml-0.5">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-[#2b1a1f] ml-0.5" for="password_confirmation">Konfirmasi Password</label>
                    <div class="relative">
                        <input
                            :type="showPasswordConfirm ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="Ulangi password"
                            class="w-full h-10 bg-white/60 border border-[#e0bec1] rounded-lg px-3.5 pr-10 text-sm font-sans focus:ring-2 focus:ring-[#f45472]/20 focus:border-[#f45472] transition-all duration-300"
                        >
                        <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute right-3 top-2.5 text-[#594043] hover:text-[#b01f44] transition-colors">
                            <span class="material-symbols-outlined text-[18px]" x-text="showPasswordConfirm ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <div class="pt-1">
                    <button
                        type="submit"
                        :disabled="isLoading"
                        class="w-full h-10 bg-[#f45472] text-white font-bold text-xs uppercase tracking-widest rounded-full shadow-[0_8px_20px_rgba(244,84,114,0.3)] hover:shadow-[0_12px_24px_rgba(244,84,114,0.4)] hover:scale-[1.02] active:scale-[0.98] disabled:opacity-70 disabled:scale-100 transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <svg x-show="isLoading" x-cloak class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Memproses...' : 'Daftar Sekarang'">Daftar Sekarang</span>
                    </button>
                </div>

                <div class="text-center pt-1">
                    <p class="text-xs text-[#594043]">
                        Sudah punya akun?
                        <a class="text-[#b01f44] font-semibold hover:underline decoration-2 underline-offset-4" href="{{ route('login') }}">Masuk sekarang</a>
                    </p>
                </div>
            </form>
        </div>
    </section>
</main>
</body>
</html>