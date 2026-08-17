{{--
    Component Action/Status Modal Notification
    Menampilkan Popup Modal saat aksi selesai (Booking, Ganti Password, Login, Logout)
--}}
@php
    $modalData = null;

    if (session('modal') && in_array(session('modal.type'), ['logout', 'delete_account'], true)) {
        $modalData = session('modal');
    }
@endphp


<div
    x-data="{
        showModal: {{ $modalData ? 'true' : 'false' }},
        title: '{{ $modalData['title'] ?? '' }}',
        message: '{{ $modalData['message'] ?? '' }}',
        type: '{{ $modalData['type'] ?? 'success' }}',
        closeModal() {
            this.showModal = false;
        }
    }"
    x-on:show-action-modal.window="
        title = $event.detail.title || 'Informasi';
        message = $event.detail.message || '';
        type = $event.detail.type || 'success';
        showModal = true;
    "
    x-show="showModal"
    x-cloak
    class="fixed inset-0 z-[500] flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="showModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="closeModal()"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"
    ></div>

    {{-- Modal Content --}}
    <div
        x-show="showModal"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative bg-white rounded-3xl shadow-2xl overflow-hidden max-w-md w-full p-6 text-center z-10 border-2"
        :class="{
            'border-green-400': type === 'success',
            'border-red-400': type === 'error' || type === 'logout',
            'border-yellow-400': type === 'warning',
            'border-blue-400': type === 'info'
        }"
    >
        {{-- Decorative Icon Background --}}
        <div class="mx-auto flex items-center justify-center w-16 h-16 rounded-full mb-4"
             :class="{
                 'bg-green-100 text-green-500': type === 'success',
                 'bg-red-100 text-red-500': type === 'error' || type === 'logout',
                 'bg-yellow-100 text-yellow-500': type === 'warning',
                 'bg-blue-100 text-blue-500': type === 'info'
             }">
            <template x-if="type === 'success'">
                <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </template>
            <template x-if="type === 'logout'">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </template>
            <template x-if="type === 'info'">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </template>
        </div>

        <h3 class="text-xl font-bold text-[#5b3a29] mb-2 font-display" id="modal-title" x-text="title"></h3>
        <p class="text-sm text-[#5b3a29]/70 mb-6 leading-relaxed" x-text="message"></p>

        <div class="flex justify-center">
            <button
                type="button"
                @click="closeModal()"
                class="w-full py-3 px-6 rounded-full text-white font-semibold shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none"
                :class="{
                    'bg-green-500 hover:bg-green-600': type === 'success',
                    'bg-red-500 hover:bg-red-600': type === 'error' || type === 'logout',
                    'bg-yellow-500 hover:bg-yellow-600': type === 'warning',
                    'bg-blue-500 hover:bg-blue-600': type === 'info'
                }"
            >
                Mengerti & Lanjutkan
            </button>
        </div>
    </div>
</div>
