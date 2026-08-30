<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    {{-- Design Tokens & Typography --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <div class="pt-28 pb-16 bg-transparent min-h-screen relative overflow-hidden font-sans">
        {{-- Background Ambient Blobs --}}
        <div class="absolute top-[-10%] left-[-10%] w-[400px] h-[400px] bg-rose-200/30 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[350px] h-[350px] bg-amber-200/20 rounded-full blur-[80px] pointer-events-none"></div>

        <div class="max-w-3xl mx-auto px-4 sm:px-6 space-y-6 relative z-10">

            {{-- Profile Header Banner Card --}}
            <div class="bg-white/90 backdrop-blur-xl border border-rose-100 rounded-2xl p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                    <div class="relative w-14 h-14 rounded-full bg-gradient-to-br from-[#FF6B8A] via-[#FF8FA3] to-[#FFB6C1] p-0.5 shadow-sm shrink-0">
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=FF6B8A&color=fff&size=128' }}"
                             alt="{{ $user->name }}"
                             width="56"
                             height="56"
                             decoding="async"
                             class="w-full h-full object-cover rounded-full bg-white">
                    </div>
                    <div>
                        <h1 class="font-display text-xl font-extrabold text-[#5b3a29] leading-tight">
                            {{ $user->name }}
                        </h1>
                        <p class="text-xs text-gray-500 font-medium mt-0.5">
                            {{ $user->email }}
                        </p>
                        <div class="flex items-center justify-center sm:justify-start gap-2 mt-2 flex-wrap">
                            <span class="px-2.5 py-0.5 bg-rose-50 text-[#f45472] border border-rose-200 rounded-full text-xs font-bold uppercase tracking-wider">
                                Member Yalia
                            </span>
                            <span class="px-2.5 py-0.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-full text-xs font-bold flex items-center gap-1">
                                <i class="fas fa-coins text-amber-500" aria-hidden="true"></i>
                                {{ number_format($user->total_points ?? 0) }} PTS
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3 Main Sections Stacked Cards --}}
            <div class="space-y-6">
                
                {{-- 1. Profile Information Section --}}
                <div x-data="{ saving: false }" class="bg-white/90 backdrop-blur-xl rounded-2xl p-5 sm:p-6 border border-rose-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-rose-100">
                        <div class="w-8 h-8 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-[#f45472] shrink-0">
                            <i class="fas fa-user-gear text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-display text-base font-bold text-[#5b3a29]">Informasi Profil</h2>
                            <p class="text-xs text-gray-500">Perbarui nama lengkap dan alamat email utama Anda.</p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <div x-show="saving" class="py-2">
                            <x-skeleton.form fields="2" />
                        </div>
                        <div x-show="!saving" @submit="saving = true">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                {{-- 2. Update Password Section --}}
                <div x-data="{ saving: false }" class="bg-white/90 backdrop-blur-xl rounded-2xl p-5 sm:p-6 border border-rose-100 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-rose-100">
                        <div class="w-8 h-8 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-center text-amber-600 shrink-0">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-display text-base font-bold text-[#5b3a29]">Ubah Kata Sandi</h2>
                            <p class="text-xs text-gray-500">Gunakan kata sandi kombinasi angka dan huruf demi keamanan akun.</p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        <div x-show="saving" x-cloak class="py-2">
                            <x-skeleton.form fields="3" />
                        </div>
                        <div x-show="!saving" @submit="saving = true">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- 3. Delete Account Section --}}
                <div class="bg-red-50/40 backdrop-blur-xl rounded-2xl p-5 sm:p-6 border border-red-200 shadow-sm transition-all hover:shadow-md">
                    <div class="flex items-center gap-3 mb-4 pb-3 border-b border-red-200/60">
                        <div class="w-8 h-8 rounded-xl bg-red-100 border border-red-200 flex items-center justify-center text-red-600 shrink-0">
                            <i class="fas fa-triangle-exclamation text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-display text-base font-bold text-red-900">Hapus Akun</h2>
                            <p class="text-xs text-red-700">Hapus akun secara permanen beserta seluruh data riwayat booking.</p>
                        </div>
                    </div>

                    <div class="max-w-md">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
