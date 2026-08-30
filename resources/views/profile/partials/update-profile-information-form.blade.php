<section>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Upload Foto Profil Avatar --}}
        <div x-data="{ avatarPreview: '{{ $user->avatar_url }}' }">
            <x-input-label for="avatar" value="Foto Profil Avatar" />
            <div class="mt-2 flex items-center gap-4">
                <div class="relative w-16 h-16 rounded-full bg-gradient-to-br from-[#FF6B8A] to-[#FFB6C1] p-0.5 shadow-sm shrink-0 overflow-hidden">
                    <img :src="avatarPreview" alt="Avatar Preview" class="w-full h-full object-cover rounded-full bg-white">
                </div>
                <div class="flex-1">
                    <input type="file" id="avatar" name="avatar" accept="image/*"
                           @change="const file = $event.target.files[0]; if (file) { avatarPreview = URL.createObjectURL(file); }"
                           class="block w-full text-xs text-rose-950 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#f45472] hover:file:bg-rose-100 cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
        </div>

        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-xs mt-2 text-rose-950">
                        Alamat email Anda belum terverifikasi.

                        <button form="send-verification" class="underline text-xs text-[#b01f44] hover:text-[#8f1937] font-semibold rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-xs text-emerald-600">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Save CTA --}}

        <div class="flex items-center gap-4">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#b01f44] hover:bg-[#8f1937] text-white font-bold text-xs shadow-sm transition-all flex items-center gap-2" style="background-color:#b01f44; color:#ffffff;">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Perubahan</span>
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-xs font-semibold text-emerald-600 flex items-center gap-1"
                ><i class="fa-solid fa-circle-check text-xs"></i> Perubahan Tersimpan.</p>
            @endif
        </div>
    </form>
</section>
