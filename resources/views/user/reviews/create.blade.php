@extends('layouts.app')

@section('title', 'Beri Ulasan - Yalia Beauty')

@section('content')
<div class="min-h-screen py-24 px-4 sm:px-6 flex justify-center items-center relative overflow-hidden bg-slate-50">
    <!-- Ambient -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-50"></div>

    <div class="relative z-10 w-full max-w-xl">
        
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-slate-800">Bagaimana pengalamanmu?</h1>
            <p class="text-slate-500 mt-2">Beri tahu kami pendapatmu tentang layanan {{ $treatment->name }} bersama {{ $booking->beautician->name ?? 'Beautician Kami' }}</p>
        </div>

        <form action="{{ route('user.treatments.review.store', ['booking' => $booking->id, 'treatment' => $treatment->id]) }}" method="POST" enctype="multipart/form-data" class="bg-white/60 backdrop-blur-xl border border-white/40 p-8 rounded-3xl shadow-xl space-y-6">
            @csrf

            <!-- Rating -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-3 text-center">Berikan Rating (1-5)</label>
                <div class="flex justify-center items-center gap-2 flex-row-reverse justify-end" style="direction: rtl;">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" class="peer hidden" {{ old('rating') == $i ? 'checked' : '' }} required />
                        <label for="star{{ $i }}" class="cursor-pointer text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400 transition-colors">
                            <svg class="w-10 h-10 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        </label>
                    @endfor
                </div>
                @error('rating') <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p> @enderror
            </div>

            <!-- Comment -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Tulis Ulasan (Opsional)</label>
                <textarea name="comment" rows="4" class="w-full bg-white/50 border border-slate-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition-all placeholder-slate-400" placeholder="Ceritakan pengalamanmu... (contoh: Terapisnya ramah dan tempatnya nyaman banget!)">{{ old('comment') }}</textarea>
                @error('comment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Photo Upload -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Upload Foto Hasil (Opsional)</label>
                <div class="relative">
                    <input type="file" name="photo" id="photo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer" />
                </div>
                <p class="text-xs text-slate-400 mt-2">Maks 5MB. Format: JPG, PNG, WebP.</p>
                @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Actions -->
            <div class="pt-4 flex items-center justify-between gap-4 border-t border-slate-200/50">
                <a href="{{ route('user.bookings.show', $booking) }}" class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm hover:bg-slate-50 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-purple-600 text-white font-semibold text-sm hover:bg-purple-700 transition-all shadow-md hover:shadow-lg">
                    Kirim Ulasan
                </button>
            </div>
        </form>

    </div>
</div>

<style>
    /* Styling to make stars turn gold from right to left in RTL mode */
    .flex-row-reverse > input:checked ~ label,
    .flex-row-reverse > label:hover,
    .flex-row-reverse > label:hover ~ label {
        color: #facc15 !important;
    }
</style>
@endsection
