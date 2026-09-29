@extends('layouts.app')

@section('title', 'Beri Ulasan Perawatan — Yalia Beauty')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .review-font  { font-family: 'Work Sans', sans-serif; }
    .review-serif { font-family: 'Playfair Display', serif; }
</style>
@endpush

@section('content')
<div class="review-font min-h-screen pt-28 pb-24 px-4 sm:px-6 relative text-[#2B0F23]">

    <div class="max-w-2xl mx-auto space-y-6">

        {{-- ── Header & Gamification Banner ── --}}
        <div class="text-center space-y-2">
            <p class="text-xs font-bold uppercase tracking-widest text-primary">Yalia Beauty Review</p>
            <h1 class="review-serif text-3xl sm:text-4xl font-extrabold text-[#2B0F23] leading-tight">
                Bagikan Pengalaman Cantikmu
            </h1>
            <p class="text-sm text-[#5C1439]/80 max-w-md mx-auto">
                Bantu kami meningkatkan kualitas layanan & dapatkan reward instan di setiap ulasan.
            </p>

            {{-- Reward Badge --}}
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-[#FFF0F2] via-[#FFE5EC] to-[#FFF0F2] border border-[#F4DDE1] shadow-sm text-xs font-black text-primary mt-2">
                <i class="fa-solid fa-gift text-amber-500 text-sm"></i>
                <span>Dapatkan Bonus +15 PTS Poin Loyalty Setelah Mengirimkan Ulasan!</span>
            </div>
        </div>

        {{-- ── Main Review Form ── --}}
        <form action="{{ route('user.treatments.review.store', ['booking' => $booking->id, 'treatment' => $treatment->id]) }}"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-6"
              x-data="{
                  treatmentRating: {{ old('rating', 5) }},
                  beauticianRating: {{ old('beautician_rating', 5) }},
                  selectedTags: [],
                  tagsList: [
                      'Ramah & Sopan',
                      'Pijatan Pas & Nyaman',
                      'Sangat Teliti & Rapi',
                      'Tepat Waktu',
                      'Bersih & Higienis',
                      'Komunikatif & Edukatif'
                  ],
                  toggleTag(tag) {
                      if (this.selectedTags.includes(tag)) {
                          this.selectedTags = this.selectedTags.filter(t => t !== tag);
                      } else {
                          this.selectedTags.push(tag);
                      }
                  },
                  ratingText(r) {
                      switch(parseInt(r)) {
                          case 1: return 'Sangat Kecewa';
                          case 2: return 'Kurang Puas';
                          case 3: return 'Cukup Baik';
                          case 4: return 'Puas & Nyaman';
                          case 5: return 'Luar Biasa Glowing!';
                          default: return 'Pilih Bintang';
                      }
                  }
              }">
            @csrf

            {{-- ══ CARD 1: Penilaian Layanan Treatment ══ --}}
            <div class="bg-white rounded-3xl border border-[#F4DDE1] p-6 sm:p-7 shadow-sm space-y-6">
                <div class="flex items-center gap-4 pb-5 border-b border-[#F4DDE1]">
                    <div class="w-16 h-16 rounded-2xl bg-[#FFF0F2] border border-[#F4DDE1] overflow-hidden shrink-0">
                        <img src="{{ $treatment->image_url }}" alt="{{ $treatment->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-primary bg-[#FFF0F2] px-2 py-0.5 rounded-md border border-[#F4DDE1]">
                            Layanan Perawatan
                        </span>
                        <h2 class="review-serif text-lg sm:text-xl font-bold text-[#2B0F23] mt-1 truncate">
                            {{ $treatment->name }}
                        </h2>
                        <p class="text-xs text-[#5C1439]/70">Durasi: {{ $treatment->duration_minutes }} menit · Kode: #{{ $booking->booking_code }}</p>
                    </div>
                </div>

                {{-- Interactive Star Rating Treatment --}}
                <div class="text-center space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-[#5C1439]/70">
                        Rating Hasil Perawatan
                    </label>

                    <div class="flex items-center justify-center gap-2">
                        <template x-for="star in 5" :key="'tr-'+star">
                            <button type="button"
                                    @click="treatmentRating = star"
                                    class="text-3xl sm:text-4xl transition-transform hover:scale-110 active:scale-95 cursor-pointer focus:outline-none"
                                    :class="star <= treatmentRating ? 'text-amber-400' : 'text-gray-200'">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        </template>
                    </div>

                    <p class="text-xs font-extrabold text-primary" x-text="ratingText(treatmentRating)"></p>
                    <input type="hidden" name="rating" :value="treatmentRating">
                    @error('rating') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Komentar --}}
                <div>
                    <label class="block text-xs font-bold text-[#2B0F23] mb-1.5">
                        Ceritakan Pengalaman Anda (Opsional)
                    </label>
                    <textarea name="comment"
                              rows="3"
                              class="w-full text-sm rounded-2xl border border-[#F4DDE1] focus:border-primary focus:ring-2 focus:ring-primary/15 p-3.5 placeholder-[#5C1439]/40 bg-[#FFF8FA]"
                              placeholder="Bagikan kesan Anda tentang suasana, aroma terapi, hasil glowing kulit, atau kenyamanan tempat...">{{ old('comment') }}</textarea>
                    @error('comment') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Upload Foto --}}
                <div>
                    <label class="block text-xs font-bold text-[#2B0F23] mb-1.5">
                        Foto Hasil Treatment (Opsional)
                    </label>
                    <div class="relative">
                        <input type="file" name="photo" id="reviewPhoto" accept="image/*"
                               class="w-full text-xs text-[#5C1439]/70 file:mr-3 file:py-2.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-[#FFF0F2] file:text-primary hover:file:bg-[#FFE5EC] cursor-pointer" />
                    </div>
                    <span class="text-xs text-[#5C1439]/60 mt-1 block">Format: JPG, PNG, WebP. Maksimal 5 MB.</span>
                    @error('photo') <p class="text-rose-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- ══ CARD 2: Penilaian Khusus Terapis / Beautician ══ --}}
            @if($booking->beautician)
            <div class="bg-white rounded-3xl border border-[#F4DDE1] p-6 sm:p-7 shadow-sm space-y-6">
                <div class="flex items-center gap-4 pb-5 border-b border-[#F4DDE1]">
                    <div class="w-14 h-14 rounded-full bg-[#FFF0F2] border border-[#F4DDE1] overflow-hidden shrink-0">
                        <img src="{{ $booking->beautician->photo_url }}" alt="{{ $booking->beautician->name }}" class="w-full h-full object-cover">
                    </div>
                    <div class="min-w-0">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-200">
                            Terapis & Beautician
                        </span>
                        <h3 class="review-serif text-lg font-bold text-[#2B0F23] mt-1 truncate">
                            {{ $booking->beautician->name }}
                        </h3>
                        <p class="text-xs text-[#5C1439]/70">Pelayanan spesialis kecantikan Yalia</p>
                    </div>
                </div>

                {{-- Interactive Star Rating Beautician --}}
                <div class="text-center space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-[#5C1439]/70">
                        Rating Pelayanan Terapis
                    </label>

                    <div class="flex items-center justify-center gap-2">
                        <template x-for="star in 5" :key="'bt-'+star">
                            <button type="button"
                                    @click="beauticianRating = star"
                                    class="text-3xl sm:text-4xl transition-transform hover:scale-110 active:scale-95 cursor-pointer focus:outline-none"
                                    :class="star <= beauticianRating ? 'text-amber-400' : 'text-gray-200'">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        </template>
                    </div>

                    <p class="text-xs font-extrabold text-primary" x-text="ratingText(beauticianRating)"></p>
                    <input type="hidden" name="beautician_rating" :value="beauticianRating">
                </div>

                {{-- Quick Compliment Chips --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-[#2B0F23]">
                        Apa yang paling Anda sukai dari terapis ini? (Pilih satu atau lebih)
                    </label>

                    <div class="flex flex-wrap gap-2">
                        <template x-for="tag in tagsList" :key="tag">
                            <button type="button"
                                    @click="toggleTag(tag)"
                                    class="px-3.5 py-1.5 rounded-full text-xs font-bold border transition-all cursor-pointer flex items-center gap-1.5 active:scale-95"
                                    :class="selectedTags.includes(tag)
                                        ? 'bg-primary text-white border-primary shadow-xs'
                                        : 'bg-[#FFF8FA] text-[#2B0F23] border-[#F4DDE1] hover:border-primary/40'">
                                <i class="fa-solid text-xs" :class="selectedTags.includes(tag) ? 'fa-check' : 'fa-plus'"></i>
                                <span x-text="tag"></span>
                            </button>
                        </template>
                    </div>

                    {{-- Hidden Inputs for tags --}}
                    <template x-for="tag in selectedTags" :key="'input-'+tag">
                        <input type="hidden" name="beautician_tags[]" :value="tag">
                    </template>
                </div>
            </div>
            @endif

            {{-- ══ CARD 3: Actions ══ --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                <a href="{{ route('user.bookings.show', $booking) }}"
                   class="w-full sm:w-auto px-6 py-3 rounded-full border border-[#F4DDE1] text-xs font-bold text-[#5C1439] hover:bg-[#FFF0F2] transition-colors text-center">
                    Batal
                </a>

                <button type="submit"
                        class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-gradient-to-r from-primary via-[#C82D53] to-secondary text-white text-xs font-black shadow-md hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-sparkles text-amber-300"></i>
                    <span>Kirim Ulasan & Klaim +15 PTS</span>
                </button>
            </div>

        </form>

    </div>
</div>
@endsection
