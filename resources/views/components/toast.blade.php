{{--
    Komponen Toast Notification Yalia Beauty.
    Menampilkan notifikasi dari session flash di pojok kanan bawah.
    Mendukung 4 tipe: success, error, warning, info.
    Auto-close setelah 4 detik dengan progress bar.
--}}
<div
    x-data="{
        toasts: [],
        addToast(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message });
            if (this.toasts.length > 3) {
                this.toasts.shift();
            }
            setTimeout(() => this.removeToast(id), 4200);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    x-init="
        @if(session('toast'))
            addToast('{{ session('toast.type') }}', '{{ addslashes(session('toast.message')) }}');
        @elseif(session('success'))
            addToast('success', '{{ addslashes(session('success')) }}');
        @elseif(session('error'))
            addToast('error', '{{ addslashes(session('error')) }}');
        @elseif(session('warning'))
            addToast('warning', '{{ addslashes(session('warning')) }}');
        @elseif(session('info'))
            addToast('info', '{{ addslashes(session('info')) }}');
        @elseif(session('status') === 'password-updated')
            addToast('success', 'Kata sandi berhasil diperbarui.');
        @elseif(session('status') === 'profile-updated')
            addToast('success', 'Profil berhasil diperbarui.');
        @elseif(session('status'))
            addToast('info', '{{ addslashes(session('status')) }}');
        @endif
    "

    @toast.window="addToast($event.detail.type, $event.detail.message)"
    class="fixed bottom-5 right-5 z-50 space-y-3 w-84 max-w-[calc(100vw-2.5rem)]"
    aria-live="polite"
    aria-label="Notifikasi"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-full scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-full scale-95"
            class="relative flex items-center gap-3 bg-white/95 backdrop-blur-xl rounded-2xl shadow-xl border overflow-hidden p-3.5 pr-8"
            :class="{
                'border-emerald-200 bg-gradient-to-r from-white via-white to-emerald-50/40': toast.type === 'success',
                'border-rose-200 bg-gradient-to-r from-white via-white to-rose-50/40': toast.type === 'error',
                'border-amber-200 bg-gradient-to-r from-white via-white to-amber-50/40': toast.type === 'warning',
                'border-blue-200 bg-gradient-to-r from-white via-white to-blue-50/40': toast.type === 'info',
            }"
            role="alert"
        >
            {{-- Brand Logo & Status Badge Container --}}
            <div class="relative shrink-0 flex items-center justify-center">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#f45472] to-[#ff8fa4] p-0.5 shadow-xs flex items-center justify-center shrink-0">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" 
                         alt="Yalia Logo" 
                         class="w-full h-full object-contain rounded-full bg-white p-0.5">
                </div>

                {{-- Status Mini Badge --}}
                <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full flex items-center justify-center text-xs text-white shadow-2xs"
                     :class="{
                         'bg-emerald-500': toast.type === 'success',
                         'bg-rose-500': toast.type === 'error',
                         'bg-amber-500': toast.type === 'warning',
                         'bg-blue-500': toast.type === 'info',
                     }">
                    <i class="fa-solid text-xs" :class="{
                        'fa-check': toast.type === 'success',
                        'fa-xmark': toast.type === 'error',
                        'fa-exclamation': toast.type === 'warning',
                        'fa-info': toast.type === 'info',
                    }"></i>
                </div>
            </div>

            {{-- Pesan Notifikasi --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span class="text-xs font-bold uppercase tracking-wider font-headline"
                          :class="{
                              'text-emerald-800': toast.type === 'success',
                              'text-rose-950': toast.type === 'error',
                              'text-amber-950': toast.type === 'warning',
                              'text-blue-950': toast.type === 'info',
                          }"
                          x-text="toast.type === 'success' ? 'Sukses' : (toast.type === 'error' ? 'Gagal' : (toast.type === 'warning' ? 'Peringatan' : 'Informasi'))">
                    </span>
                    <span class="text-xs text-gray-500">• Yalia Beauty</span>
                </div>
                <p class="text-xs font-semibold text-gray-800 leading-snug line-clamp-2" x-text="toast.message"></p>
            </div>

            {{-- Tombol Tutup --}}
            <button @click="removeToast(toast.id)"
                    class="absolute top-2.5 right-2.5 text-rose-950 hover:text-rose-600 transition-colors p-1 rounded-full hover:bg-rose-100"
                    aria-label="Tutup Notifikasi">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>

            {{-- Animated Progress Bar --}}
            <div class="absolute bottom-0 left-0 right-0 h-1 rounded-b-full origin-left"
                 :class="{
                     'bg-emerald-500': toast.type === 'success',
                     'bg-rose-500': toast.type === 'error',
                     'bg-amber-500': toast.type === 'warning',
                     'bg-blue-500': toast.type === 'info',
                 }"
                 x-data="{ scale: 1 }"
                 x-init="
                     $nextTick(() => {
                         setTimeout(() => { scale = 0 }, 50)
                     })
                 "
                 :style="`transform: scaleX(${scale}); transition: transform 4.1s linear`">
            </div>
        </div>
    </template>
</div>