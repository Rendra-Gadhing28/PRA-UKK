<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-3 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Pengaturan Profil Admin
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola data identitas akun administrator dan keamanan kata sandi</p>
            </div>
            
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.dashboard') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-rose-200 text-rose-950 hover:bg-rose-50 text-xs font-bold shadow-2xs transition-all">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ADMIN EXECUTIVE BANNER CARD --}}
            <div class="bg-white/90 backdrop-blur-md rounded-3xl p-6 border border-rose-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-5 relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-rose-100/40 blur-2xl pointer-events-none"></div>

                <div class="flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left relative z-10">
                    <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-[#f45472] to-[#ff8fa4] p-0.5 shadow-md shrink-0">
                        <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=f45472&color=fff&size=128' }}"
                             alt="{{ $user->name }}"
                             class="w-full h-full object-cover rounded-full bg-white">
                    </div>
                    <div>
                        <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap mb-1">
                            <h1 class="font-headline text-xl font-bold text-gray-900 leading-tight">
                                {{ $user->name }}
                            </h1>
                            <span class="px-2.5 py-0.5 bg-rose-100 text-rose-950 border border-rose-200 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1">
                                <i class="fa-solid fa-shield-halved text-xs text-[#f45472]"></i>
                                Admin Executive
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 font-medium font-mono">
                            {{ $user->email }}
                        </p>
                        <p class="text-xs text-gray-400 font-medium mt-1">
                            Terdaftar sejak: {{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- 1. INFORMASI PROFIL SECTION --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-rose-100 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-rose-50">
                    <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center text-[#f45472] shrink-0">
                        <i class="fa-solid fa-user-gear text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-headline text-base font-bold text-gray-900">Informasi Profil Admin</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Perbarui nama lengkap dan alamat email utama akun administrator Anda.</p>
                    </div>
                </div>

                @if (session('status') === 'profile-updated')
                    <div x-data="{ show: true }" x-show="show" x-transition class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs font-bold flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span>Profil administrator berhasil diperbarui!</span>
                        </div>
                        <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-950">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="space-y-4 max-w-xl">
                    @csrf
                    @method('PATCH')

                    {{-- Upload Foto Profil Avatar --}}
                    <div x-data="{ avatarPreview: '{{ $user->avatar_url }}' }">
                        <label for="admin_avatar" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-camera text-rose-500 text-xs"></i>
                            Foto Profil Admin
                        </label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-[#f45472] to-[#ff8fa4] p-0.5 shadow-md shrink-0 overflow-hidden">
                                <img :src="avatarPreview" alt="Avatar Preview" class="w-full h-full object-cover rounded-full bg-white">
                            </div>
                            <div class="flex-1">
                                <input type="file" id="admin_avatar" name="avatar" accept="image/*"
                                       @change="const file = $event.target.files[0]; if (file) { avatarPreview = URL.createObjectURL(file); }"
                                       class="block w-full text-xs text-rose-950 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#f45472] hover:file:bg-rose-100 cursor-pointer">
                                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
                            </div>
                        </div>
                        @error('avatar')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Admin --}}
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-user text-rose-500 text-xs"></i>
                            Nama Lengkap
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus 
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800"
                               placeholder="Nama lengkap administrator">
                        @error('name')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Admin --}}
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-envelope text-rose-500 text-xs"></i>
                            Alamat Email Utama
                        </label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required 
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 font-mono"
                               placeholder="email@admin.com">
                        @error('email')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-xs font-bold shadow-md transition-all cursor-pointer">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Simpan Perubahan Profil</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- 2. UBAH KATA SANDI SECTION --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-rose-100 space-y-6">
                <div class="flex items-center gap-3 pb-4 border-b border-rose-50">
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                        <i class="fa-solid fa-key text-base"></i>
                    </div>
                    <div>
                        <h3 class="font-headline text-base font-bold text-gray-900">Ubah Kata Sandi</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Gunakan kata sandi kuat dengan kombinasi huruf, angka, dan simbol demi keamanan akun admin.</p>
                    </div>
                </div>

                @if (session('status') === 'password-updated')
                    <div x-data="{ show: true }" x-show="show" x-transition class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 text-xs font-bold flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                            <span>Kata sandi administrator berhasil diperbarui!</span>
                        </div>
                        <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-950">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.profile.password.update') }}" class="space-y-4 max-w-xl">
                    @csrf
                    @method('PUT')

                    {{-- Kata Sandi Saat Ini --}}
                    <div>
                        <label for="update_password_current_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-lock text-amber-500 text-xs"></i>
                            Kata Sandi Saat Ini
                        </label>
                        <input id="update_password_current_password" name="current_password" type="password" required 
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800"
                               placeholder="Masukkan kata sandi lama">
                        @error('current_password', 'updatePassword')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kata Sandi Baru --}}
                    <div>
                        <label for="update_password_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-key text-rose-500 text-xs"></i>
                            Kata Sandi Baru
                        </label>
                        <input id="update_password_password" name="password" type="password" required 
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800"
                               placeholder="Minimal 8 karakter">
                        @error('password', 'updatePassword')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Kata Sandi Baru --}}
                    <div>
                        <label for="update_password_password_confirmation" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield text-emerald-500 text-xs"></i>
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" required 
                               class="w-full px-3.5 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800"
                               placeholder="Ulangi kata sandi baru">
                        @error('password_confirmation', 'updatePassword')
                            <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-3">
                        <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#b01f44] text-white hover:bg-[#8b1532] text-xs font-bold shadow-md transition-all cursor-pointer">
                            <i class="fa-solid fa-key text-xs"></i>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-admin-layout>
