{{--
    Komponen Toast Notification.

    Menampilkan notifikasi dari session flash di pojok kanan bawah.
    Mendukung 4 tipe: success, error, warning, info.
    Auto-close setelah 4 detik dengan progress bar.
    Maksimal 3 toast sekaligus.

    Cara pakai di controller:
        ToastHelper::success('Berhasil disimpan.');
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
            setTimeout(() => this.removeToast(id), 4000);
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
        @elseif(session('status') === 'password-updated')
            addToast('success', 'Password berhasil diperbarui.');
        @elseif(session('status') === 'profile-updated')
            addToast('success', 'Profil berhasil diperbarui.');
        @endif
    "

    @toast.window="addToast($event.detail.type, $event.detail.message)"
    class="fixed bottom-5 right-5 z-50 space-y-3 w-80 max-w-[calc(100vw-2.5rem)]"
    aria-live="polite"
    aria-label="Notifikasi"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-full"
            class="relative flex items-start gap-3 bg-white rounded-2xl shadow-xl border overflow-hidden pr-4 pl-4 pt-4 pb-3"
            :class="{
                'border-green-400': toast.type === 'success',
                'border-red-400': toast.type === 'error',
                'border-yellow-400': toast.type === 'warning',
                'border-blue-400': toast.type === 'info',
            }"
            role="alert"
        >
            {{-- Ikon --}}
            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mt-0.5"
                 :class="{
                     'bg-green-100 text-green-600': toast.type === 'success',
                     'bg-red-100 text-red-600': toast.type === 'error',
                     'bg-yellow-100 text-yellow-600': toast.type === 'warning',
                     'bg-blue-100 text-blue-600': toast.type === 'info',
                 }">
                {{-- Success --}}
                <svg x-show="toast.type === 'success'" class="w-4 h-4 py-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                {{-- Error --}}
                <svg x-show="toast.type === 'error'" class="w-4 h-4 py-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{-- Warning --}}
                <svg x-show="toast.type === 'warning'" class="w-4 h-4 py-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                {{-- Info --}}
                <svg x-show="toast.type === 'info'" class="w-4 h-4 py-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            {{-- Pesan --}}
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-sm font-medium text-gray-800" x-text="toast.message"></p>
            </div>

            {{-- Tombol Tutup --}}
            <button @click="removeToast(toast.id)"
                    class="absolute top-3 right-3 text-gray-300 hover:text-gray-500 transition-colors p-0.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            {{-- Progress Bar (auto-close indicator) --}}
            <div class="absolute bottom-0 left-0 right-0 h-0.5 rounded-b-full"
                 :class="{
                     'bg-green-400': toast.type === 'success',
                     'bg-red-400': toast.type === 'error',
                     'bg-yellow-400': toast.type === 'warning',
                     'bg-blue-400': toast.type === 'info',
                 }"
                 x-data="{ width: 100 }"
                 x-init="
                     $nextTick(() => {
                         setTimeout(() => { width = 0 }, 50)
                     })
                 "
                 :style="`width: ${width}%; transition: width 3.9s linear`">
            </div>
        </div>
    </template>
</div>