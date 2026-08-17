@extends('layouts.app')

@section('title', 'Booking Perawatan Luxury - Yalia Beauty')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-display { font-family: 'Playfair Display', serif; }
    .font-body { font-family: 'Work Sans', sans-serif; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }

    @keyframes floatParticle {
        0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
        50% { transform: translateY(-15px) rotate(15deg); opacity: 0.9; }
    }
    .animate-particle-1 { animation: floatParticle 6s ease-in-out infinite; }
    .animate-particle-2 { animation: floatParticle 8s ease-in-out infinite 2s; }
    .animate-particle-3 { animation: floatParticle 7s ease-in-out infinite 1s; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#fff5f7] via-[#fdf5f6] to-[#fff0f3] font-body py-32 px-4 relative overflow-hidden" x-data="bookingWizard()" x-init="init()">

    {{-- Luxury Ambient Glowing Particles & Orbs --}}
    <div class="absolute top-10 left-1/4 w-96 h-96 bg-[#f45472]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 right-10 w-80 h-80 bg-[#ffd1dc]/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-72 h-72 bg-[#f9c5cf]/30 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Floating Beauty Sparkles --}}
    <div class="absolute top-24 left-12 text-[#f45472]/30 text-2xl animate-particle-1 pointer-events-none">✦</div>
    <div class="absolute top-40 right-20 text-[#d94060]/25 text-3xl animate-particle-2 pointer-events-none">✧</div>
    <div class="absolute top-2/3 left-20 text-[#f45472]/30 text-2xl animate-particle-3 pointer-events-none">🌸</div>
    <div class="absolute bottom-32 right-16 text-[#d94060]/30 text-2xl animate-particle-1 pointer-events-none">✨</div>

    <div class="max-w-3xl mx-auto relative z-10">

        {{-- Header Section --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-[#f45472]/20 shadow-sm text-xs font-semibold text-[#f45472] uppercase tracking-wider mb-3">
                <span>✨ Reservasi Online Yalia Beauty</span>
            </div>
            <h1 class="font-display text-3xl md:text-5xl font-extrabold text-[#5b3a29] mb-3 tracking-tight">
                Booking Perawatan
            </h1>
            <p class="text-[#5b3a29]/75 text-sm md:text-base max-w-lg mx-auto leading-relaxed">
                Nikmati pengalaman perawatan kecantikan terbaik. Lengkapi 4 langkah mudah berikut ini.
            </p>
        </div>

        {{-- Stepper Progress --}}
        <div class="flex items-center gap-2 mb-10 bg-white/80 backdrop-blur-md p-4 rounded-3xl border border-rose-100 shadow-[0_8px_25px_rgba(91,58,41,0.05)]">
            <template x-for="n in 4" :key="n">
                <div class="flex items-center flex-1">
                    <div
                        class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-all duration-300 shadow-sm"
                        :class="step >= n 
                            ? 'bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white shadow-[0_4px_15px_rgba(244,84,114,0.35)] scale-105' 
                            : 'bg-white text-[#5b3a29]/40 border border-[#5b3a29]/15'"
                        x-text="n"
                    ></div>
                    <div class="flex-1 h-1 mx-2 rounded-full transition-all duration-300" :class="step > n ? 'bg-gradient-to-r from-[#f45472] to-[#e03e5c]' : 'bg-gray-200'" x-show="n < 4"></div>
                </div>
            </template>
        </div>

        <template x-if="serverErrors.length">
            <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 p-5 text-red-700 text-sm shadow-sm">
                <p class="font-bold mb-1.5 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Mohon perbaiki bidang berikut:
                </p>
                <ul class="list-disc list-inside space-y-1 pl-1 text-red-600">
                    <template x-for="err in serverErrors" :key="err">
                        <li x-text="err"></li>
                    </template>
                </ul>
            </div>
        </template>

        <form method="POST" action="{{ route('user.bookings.store') }}" @submit="onSubmit">
            @csrf

            {{-- ============ STEP 1: TREATMENT SELECTION ============ --}}
            <div x-show="step === 1" x-cloak class="bg-white/90 backdrop-blur-md rounded-3xl border border-rose-100 shadow-[0_15px_45px_rgba(244,84,114,0.08)] p-6 md:p-10">
                <div class="flex items-center justify-between mb-6 border-b border-rose-100/60 pb-4">
                    <div>
                        <h2 class="font-display text-2xl font-bold text-[#5b3a29]">1. Treatment Pilihan</h2>
                        <p class="text-sm text-[#5b3a29]/70 mt-1">Perawatan terpilih dan Anda bisa menambah perawatan menarik lainnya.</p>
                    </div>
                    <span class="px-3 py-1 bg-rose-50 text-[#f45472] rounded-full text-xs font-bold" x-text="selectedTreatments.length + ' Treatment'"></span>
                </div>

                {{-- Selected Treatments List --}}
                <div class="space-y-4 mb-8">
                    <template x-for="(item, index) in selectedTreatments" :key="item.id">
                        <div class="flex items-center gap-4 rounded-2xl border border-rose-200/80 bg-gradient-to-r from-white to-[#fff9fa] p-4 shadow-sm hover:shadow-md transition-all">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#f45472]/15 to-[#ff8fa4]/20 flex items-center justify-center text-[#f45472] shrink-0 font-bold">
                                ✨
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-[#5b3a29] text-base" x-text="item.name"></p>
                                <p class="text-xs text-[#5b3a29]/70 mt-0.5 flex items-center gap-2">
                                    <span class="font-semibold text-[#f45472]" x-text="formatRupiah(item.price)"></span>
                                    <span>&bull;</span>
                                    <span>⏱️ <span x-text="item.duration_minutes"></span> menit</span>
                                </p>
                            </div>
                            <div class="flex items-center gap-2 bg-[#fdf5f6] border border-rose-200/60 rounded-full px-2 py-1">
                                <button type="button" class="w-7 h-7 rounded-full bg-white text-[#5b3a29] font-bold shadow-xs hover:bg-rose-100 transition" @click="changeQty(index, -1)">-</button>
                                <span class="w-6 text-center font-bold text-sm text-[#5b3a29]" x-text="item.quantity"></span>
                                <button type="button" class="w-7 h-7 rounded-full bg-white text-[#5b3a29] font-bold shadow-xs hover:bg-rose-100 transition" @click="changeQty(index, 1)">+</button>
                            </div>
                            <button type="button" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition" @click="removeTreatment(index)" x-show="selectedTreatments.length > 1">
                                &times;
                            </button>
                        </div>
                    </template>
                </div>

                {{-- Add Extra Treatment Section (Custom Luxury Grid) --}}
                <div class="rounded-2xl border border-rose-200/60 bg-[#fffdfd] p-5 mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-bold text-[#5b3a29] uppercase tracking-wider flex items-center gap-2">
                            <span>🌸 Tambah Perawatan Lainnya</span>
                        </label>
                    </div>

                    {{-- Custom Search / Select Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-60 overflow-y-auto pr-1 scrollbar-hide">
                        <template x-for="t in unselectedTreatments" :key="t.id">
                            <div class="p-3.5 rounded-xl border border-rose-100 bg-white hover:border-[#f45472]/40 hover:shadow-sm transition cursor-pointer flex justify-between items-center"
                                 @click="addTreatmentDirect(t)">
                                <div>
                                    <p class="text-sm font-bold text-[#5b3a29]" x-text="t.name"></p>
                                    <p class="text-xs text-[#f45472] font-semibold mt-0.5">
                                        <span x-text="formatRupiah(t.price)"></span> &bull; <span class="text-[#5b3a29]/60" x-text="t.duration_minutes + ' mnt'"></span>
                                    </p>
                                </div>
                                <button type="button" class="px-3 py-1.5 rounded-full bg-rose-50 text-[#f45472] font-semibold text-xs hover:bg-[#f45472] hover:text-white transition">
                                    + Tambah
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Subtotal Summary Bar --}}
                <div class="flex justify-between items-center bg-gradient-to-r from-[#fdf5f6] to-[#fff0f3] p-4 rounded-2xl border border-rose-100 mb-8">
                    <div>
                        <p class="text-xs uppercase font-bold text-[#5b3a29]/60 tracking-wider">Subtotal Treatment</p>
                        <p class="text-xs text-[#5b3a29]/70">Durasi: <span class="font-semibold text-[#5b3a29]" x-text="totalDurationMinutes"></span> menit</p>
                    </div>
                    <p class="font-display text-2xl font-extrabold text-[#f45472]" x-text="formatRupiah(subtotal)"></p>
                </div>

                <button type="button" class="w-full rounded-full bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-bold py-4 text-base shadow-[0_6px_20px_rgba(244,84,114,0.3)] hover:shadow-lg transition-all disabled:opacity-40"
                    :disabled="selectedTreatments.length === 0" @click="step = 2">
                    Lanjut ke Tipe Layanan &rarr;
                </button>
            </div>

            {{-- ============ STEP 2: TIPE LAYANAN ============ --}}
            <div x-show="step === 2" x-cloak class="bg-white/90 backdrop-blur-md rounded-3xl border border-rose-100 shadow-[0_15px_45px_rgba(244,84,114,0.08)] p-6 md:p-10">
                <h2 class="font-display text-2xl font-bold text-[#5b3a29] mb-1">2. Tipe Layanan</h2>
                <p class="text-sm text-[#5b3a29]/70 mb-6">Pilih apakah ingin perawatan langsung di Salon atau Home Service ke lokasi Anda.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    {{-- At Salon Option --}}
                    <button type="button" @click="bookingType = 'salon'"
                        class="rounded-2xl border-2 p-6 text-left transition relative overflow-hidden"
                        :class="bookingType === 'salon' 
                            ? 'border-[#f45472] bg-gradient-to-br from-[#fff5f7] to-white shadow-md' 
                            : 'border-gray-200 bg-white hover:border-rose-200'">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">🏛️</span>
                            <p class="font-display font-bold text-lg text-[#5b3a29]">At Salon</p>
                        </div>
                        <p class="text-xs text-[#5b3a29]/80 leading-relaxed font-medium">
                            Datang dan bersantai langsung di lokasi salon Yalia Beauty yang nyaman.
                        </p>
                        <div x-show="bookingType === 'salon'" class="absolute top-3 right-3 text-[#f45472] font-bold">✓</div>
                    </button>

                    {{-- Home Service Option --}}
                    <button type="button" @click="bookingType = 'home'"
                        class="rounded-2xl border-2 p-6 text-left transition relative overflow-hidden"
                        :class="bookingType === 'home' 
                            ? 'border-[#f45472] bg-gradient-to-br from-[#fff5f7] to-white shadow-md' 
                            : 'border-gray-200 bg-white hover:border-rose-200'">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-2xl">🏡</span>
                            <p class="font-display font-bold text-lg text-[#5b3a29]">Home Service</p>
                        </div>
                        <p class="text-xs text-[#5b3a29]/80 leading-relaxed font-medium">
                            Beautician profesional kami yang akan datang ke rumah/lokasi Anda (maks {{ $serviceRadiusKm }} km).
                        </p>
                        <div x-show="bookingType === 'home'" class="absolute top-3 right-3 text-[#f45472] font-bold">✓</div>
                    </button>
                </div>

                {{-- Home Service Location Form --}}
                <div x-show="bookingType === 'home'" x-cloak class="rounded-2xl border border-rose-200 bg-[#fff8f9] p-6 mb-8">
                    <h3 class="font-bold text-[#5b3a29] text-sm uppercase tracking-wider mb-3 flex items-center gap-2">
                        <span>📍 Lokasi Pengiriman Home Service</span>
                    </h3>

                    <button type="button" class="rounded-full bg-gradient-to-r from-[#5b3a29] to-[#3a2217] text-white text-xs font-bold px-6 py-3 shadow hover:shadow-md transition"
                        @click="shareLocation" :disabled="gps.loading">
                        <span x-show="!gps.loading">📍 Dapatkan Lokasi GPS Saya</span>
                        <span x-show="gps.loading">Mendeteksi lokasi...</span>
                    </button>

                    <template x-if="gps.error">
                        <p class="text-xs text-red-600 font-semibold mt-3" x-text="gps.error"></p>
                    </template>

                    <template x-if="gps.lat && !gps.error">
                        <div class="mt-4 space-y-3 pt-3 border-t border-rose-200/60">
                            <div class="flex justify-between text-xs text-[#5b3a29]">
                                <span class="font-semibold">Jarak dari Salon:</span>
                                <span class="font-bold text-[#f45472]" x-text="gps.distanceKm ? gps.distanceKm.toFixed(1) + ' km' : 'Menghitung...'"></span>
                            </div>
                            <div class="flex justify-between text-xs text-[#5b3a29]" x-show="gps.distanceKm !== null && gps.distanceKm <= {{ $serviceRadiusKm }}">
                                <span class="font-semibold">Estimasi Ongkir Transport:</span>
                                <span class="font-bold text-[#5b3a29]" x-text="formatRupiah(estimateTransportFee(gps.distanceKm))"></span>
                            </div>
                            <p class="text-xs text-red-600 font-bold" x-show="gps.distanceKm !== null && gps.distanceKm > {{ $serviceRadiusKm }}">
                                ⚠️ Lokasi di luar jangkauan (maks {{ $serviceRadiusKm }} km).
                            </p>
                            <div>
                                <label class="block text-xs font-bold text-[#5b3a29] mb-1">Alamat Lengkap (Dapat disunting):</label>
                                <textarea x-model="gps.address" rows="2"
                                    class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-xs text-[#333333] font-medium focus:outline-none focus:ring-2 focus:ring-[#f45472]"
                                    placeholder="Tuliskan detail patokan rumah/apartemen..."></textarea>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex gap-4">
                    <button type="button" class="rounded-full border border-gray-300 text-[#5b3a29] font-bold px-7 py-3 text-sm hover:bg-gray-50 transition" @click="step = 1">Kembali</button>
                    <button type="button" class="flex-1 rounded-full bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-bold py-3 text-sm shadow-[0_6px_20px_rgba(244,84,114,0.3)] hover:shadow-lg transition disabled:opacity-40"
                        :disabled="!canProceedFromStep2" @click="step = 3">
                        Lanjut ke Pilih Jadwal &rarr;
                    </button>
                </div>
            </div>

            {{-- ============ STEP 3: CUSTOM JADWAL ============ --}}
            <div x-show="step === 3" x-cloak class="bg-white/90 backdrop-blur-md rounded-3xl border border-rose-100 shadow-[0_15px_45px_rgba(244,84,114,0.08)] p-6 md:p-10">
                <h2 class="font-display text-2xl font-bold text-[#5b3a29] mb-1">3. Pilih Jadwal Perawatan</h2>
                <p class="text-sm text-[#5b3a29]/70 mb-6">Total estimasi durasi perawatan: <span class="font-bold text-[#f45472]" x-text="totalDurationMinutes"></span> menit.</p>

                {{-- Horizontal Date Picker --}}
                <label class="block text-xs font-bold text-[#5b3a29] uppercase tracking-wider mb-2">Pilih Tanggal</label>
                <div class="flex gap-3 overflow-x-auto scrollbar-hide pb-3 mb-6">
                    <template x-for="day in availableDays" :key="day.date">
                        <button type="button" @click="selectDate(day.date)"
                            class="shrink-0 w-16 rounded-2xl border-2 py-3 text-center transition shadow-xs"
                            :class="selectedDate === day.date 
                                ? 'border-[#f45472] bg-gradient-to-b from-[#f45472] to-[#e03e5c] text-white font-bold scale-105 shadow-md' 
                                : 'border-gray-200 bg-white text-[#5b3a29] hover:border-rose-300'">
                            <p class="text-[10px] uppercase font-semibold opacity-80" x-text="day.weekday"></p>
                            <p class="text-lg font-extrabold my-0.5" x-text="day.dateNum"></p>
                            <p class="text-[10px] opacity-80" x-text="day.month"></p>
                        </button>
                    </template>
                </div>

                {{-- Single Click Daily Time Slots Grid --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-[#5b3a29] uppercase tracking-wider">Pilih Jam Kedatangan (Langsung Klik)</label>
                        <span class="text-[10px] text-[#5b3a29]/60 font-semibold">Jam Operasional 08:00 - 20:00 WIB</span>
                    </div>

                    {{-- Skeleton Loading --}}
                    <div x-show="loadingSlots" class="py-8 text-center text-xs text-[#5b3a29]/70 bg-rose-50/50 rounded-2xl border border-rose-100 mb-4">
                        <span class="inline-block animate-spin text-[#f45472] font-bold text-lg mb-1">🌸</span>
                        <p class="font-semibold">Mengecek ketersediaan seluruh jam di tanggal ini...</p>
                    </div>

                    {{-- All Time Slots Grid --}}
                    <div x-show="!loadingSlots" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        <template x-for="slot in dailySlots" :key="slot.time">
                            <button type="button" 
                                    @click="selectTimeSlot(slot.time)"
                                    class="relative rounded-2xl py-3 px-3 border-2 transition-all flex flex-col items-center justify-center gap-1 group text-center"
                                    :class="{
                                        'border-[#f45472] bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-extrabold shadow-md scale-[1.02]': selectedTimeSlot === slot.time && slot.available,
                                        'border-rose-200 bg-white text-[#5b3a29] hover:border-[#f45472] hover:bg-rose-50 shadow-xs': selectedTimeSlot !== slot.time && slot.available,
                                        'border-rose-300/80 bg-rose-100/70 text-rose-800 opacity-80': !slot.available
                                    }">
                                <div class="flex items-center gap-1">
                                    <span class="text-sm font-extrabold" x-text="slot.formatted_time"></span>
                                    <span x-show="selectedTimeSlot === slot.time && slot.available" class="text-xs">✓</span>
                                </div>

                                <span x-show="slot.available" class="text-[9px] uppercase font-bold tracking-wider" 
                                      :class="selectedTimeSlot === slot.time ? 'text-white/90' : 'text-emerald-600'">Tersedia</span>

                                <span x-show="!slot.available" class="text-[9px] uppercase font-bold tracking-wider text-rose-700 bg-rose-200/70 px-2 py-0.5 rounded-md flex items-center gap-0.5">
                                    🌸 Terisi
                                </span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Status Ketersediaan Real-Time --}}
                <div class="rounded-2xl border transition-all p-4 mb-8"
                     :class="selectedTimeSlot && availability.available === false 
                         ? 'bg-rose-100/60 border-rose-300 text-rose-900 shadow-xs' 
                         : 'border-rose-200 bg-gradient-to-r from-[#fff7f8] to-[#fff0f3]'">
                    <p class="text-xs text-[#5b3a29]/80 font-semibold">
                        Status Slot Terpilih: <span class="font-bold text-[#5b3a29]" x-text="selectedDate && selectedTimeSlot ? formatSelectedDateTime() : 'Belum memilih jam'"></span>
                    </p>
                    <p class="mt-1 text-xs text-[#5b3a29]/70" x-show="loadingSlots">⏳ Mengecek ketersediaan beautician...</p>
                    <p class="mt-1 text-xs text-emerald-600 font-bold flex items-center gap-1.5" x-show="!loadingSlots && selectedTimeSlot && availability.available === true">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Beautician siap dan tersedia di jam ini!</span>
                    </p>

                    <div class="mt-2.5 p-3 rounded-xl bg-rose-200/60 border border-rose-300/80 text-rose-900 text-xs font-medium flex items-start gap-2.5" 
                         x-show="!loadingSlots && selectedTimeSlot && availability.available === false">
                        <span class="text-rose-600 font-bold text-sm shrink-0">🌸</span>
                        <div>
                            <span class="font-bold block text-rose-900 mb-0.5">Rentang Waktu Terisi (In-Progress)</span>
                            <span x-text="availability.message || 'Beautician sedang dalam pengerjaan perawatan lain pada jam ini.'"></span>
                        </div>
                    </div>
                </div>



                <div class="flex gap-4">
                    <button type="button" class="rounded-full border border-gray-300 text-[#5b3a29] font-bold px-7 py-3 text-sm hover:bg-gray-50 transition" @click="step = 2">Kembali</button>
                    <button type="button" class="flex-1 rounded-full bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-bold py-3 text-sm shadow-[0_6px_20px_rgba(244,84,114,0.3)] hover:shadow-lg transition disabled:opacity-40"
                        :disabled="!canProceedFromStep3" @click="step = 4">
                        Lanjut ke Ringkasan &rarr;
                    </button>
                </div>
            </div>

            {{-- ============ STEP 4: RINGKASAN & SUBMIT ============ --}}
            <div x-show="step === 4" x-cloak class="bg-white/90 backdrop-blur-md rounded-3xl border border-rose-100 shadow-[0_15px_45px_rgba(244,84,114,0.08)] p-6 md:p-10">
                <h2 class="font-display text-2xl font-bold text-[#5b3a29] mb-5">4. Konfirmasi Ringkasan Booking</h2>

                <div class="space-y-3 mb-6 bg-[#fff7f8] rounded-2xl p-4 border border-rose-100">
                    <template x-for="item in selectedTreatments" :key="item.id">
                        <div class="flex justify-between text-xs font-semibold text-[#5b3a29]">
                            <span x-text="item.name + ' (x' + item.quantity + ')'"></span>
                            <span x-text="formatRupiah(item.price * item.quantity)"></span>
                        </div>
                    </template>
                </div>

                <div class="border-t border-rose-100 pt-4 space-y-2.5 text-xs">
                    <div class="flex justify-between"><span class="text-[#5b3a29]/70 font-semibold">Tipe Layanan</span><span class="font-bold text-[#5b3a29]" x-text="bookingType === 'home' ? 'Home Service' : 'At Salon'"></span></div>
                    <template x-if="bookingType === 'home'">
                        <div class="flex justify-between"><span class="text-[#5b3a29]/70 font-semibold">Alamat Lokasi</span><span class="font-bold text-[#5b3a29] text-right max-w-[65%]" x-text="gps.address font-medium"></span></div>
                    </template>
                    <div class="flex justify-between"><span class="text-[#5b3a29]/70 font-semibold">Waktu Booking</span><span class="font-bold text-[#5b3a29]" x-text="formatSelectedDateTime()"></span></div>
                    <div class="flex justify-between"><span class="text-[#5b3a29]/70 font-semibold">Subtotal Treatment</span><span class="font-bold text-[#5b3a29]" x-text="formatRupiah(subtotal)"></span></div>
                    <div class="flex justify-between" x-show="bookingType === 'home'"><span class="text-[#5b3a29]/70 font-semibold">Ongkir Transport</span><span class="font-bold text-[#5b3a29]" x-text="formatRupiah(estimateTransportFee(gps.distanceKm))"></span></div>
                    
                    <div class="flex justify-between items-center text-base border-t border-rose-200/80 pt-3 mt-3">
                        <span class="font-display font-bold text-[#5b3a29]">Total Tagihan</span>
                        <span class="font-display font-extrabold text-[#f45472] text-xl" x-text="formatRupiah(estimatedTotal)"></span>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-xs font-bold text-[#5b3a29] mb-1.5">Catatan Tambahan (Opsional):</label>
                    <textarea x-model="notes" rows="2" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-xs text-[#333] focus:outline-none focus:ring-2 focus:ring-[#f45472]" placeholder="Tuliskan permintaan khusus untuk beautician..."></textarea>
                </div>

                {{-- Hidden Form Inputs --}}
                <template x-for="(item, index) in selectedTreatments" :key="'hid-'+item.id">
                    <div>
                        <input type="hidden" :name="'treatments['+index+'][treatment_id]'" :value="item.id">
                        <input type="hidden" :name="'treatments['+index+'][quantity]'" :value="item.quantity">
                    </div>
                </template>
                <input type="hidden" name="booking_type" :value="bookingType">
                <input type="hidden" name="booking_date" :value="selectedDate">
                <input type="hidden" name="time_start" :value="pad(selectedHour)+':'+pad(selectedMinute)">
                <input type="hidden" name="home_latitude" :value="gps.lat">
                <input type="hidden" name="home_longitude" :value="gps.lng">
                <input type="hidden" name="home_address" :value="gps.address">
                <input type="hidden" name="notes" :value="notes">

                <div class="flex gap-4 mt-8">
                    <button type="button" class="rounded-full border border-gray-300 text-[#5b3a29] font-bold px-7 py-3.5 text-sm hover:bg-gray-50 transition" @click="step = 3">Kembali</button>
                    <button type="submit" class="flex-1 rounded-full bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-bold py-3.5 text-base shadow-[0_6px_20px_rgba(244,84,114,0.3)] hover:shadow-lg transition disabled:opacity-40" :disabled="submitting">
                        <span x-show="!submitting">Konfirmasi & Lanjut Pembayaran &rarr;</span>
                        <span x-show="submitting">Memproses...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bookingWizard() {
    return {
        step: 1,
        submitting: false,
        serverErrors: @json($errors->all() ?? []),

        allTreatments: @json($treatmentsData),
        selectedTreatments: [],
        treatmentToAdd: '',

        bookingType: 'salon',
        gps: { loading: false, error: null, lat: null, lng: null, address: '', distanceKm: null },

        salonLat: {{ config('booking.salon.latitude') }},
        salonLng: {{ config('booking.salon.longitude') }},
        serviceRadiusKm: {{ $serviceRadiusKm }},
        googleMapsKey: @json($googleMapsKey),

        availableDays: [],
        selectedDate: null,
        selectedTimeSlot: null,
        selectedHour: 9,
        selectedMinute: 0,
        dailySlots: [],
        loadingSlots: false,
        availability: { checking: false, available: null, message: '' },
        availabilityTimer: null,

        notes: '',

        init() {
            const preselected = @json($preselectedData);

            if (preselected) {
                if (!this.selectedTreatments.some(t => t.id === preselected.id)) {
                    this.selectedTreatments.push({ ...preselected, quantity: 1 });
                }
            }

            this.buildAvailableDays();
        },

        get unselectedTreatments() {
            return this.allTreatments.filter(t => !this.selectedTreatments.some(s => s.id === t.id));
        },

        buildAvailableDays() {
            const days = [];
            const weekdayFmt = new Intl.DateTimeFormat('id-ID', { weekday: 'short' });
            const monthFmt = new Intl.DateTimeFormat('id-ID', { month: 'short' });

            for (let i = 0; i < 14; i++) {
                const d = new Date();
                d.setDate(d.getDate() + i);
                days.push({
                    date: d.toISOString().slice(0, 10),
                    weekday: weekdayFmt.format(d),
                    dateNum: d.getDate(),
                    month: monthFmt.format(d),
                });
            }
            this.availableDays = days;
            this.selectedDate = days[0].date;
            this.fetchDailySlots();
        },

        pad(n) { return n ? n.toString().padStart(2, '0') : '00'; },

        formatRupiah(value) {
            if (value === null || value === undefined || isNaN(value)) return 'Rp 0';
            return 'Rp ' + Math.round(value).toLocaleString('id-ID');
        },

        get subtotal() {
            return this.selectedTreatments.reduce((sum, t) => sum + (t.price * t.quantity), 0);
        },

        get totalDurationMinutes() {
            return this.selectedTreatments.reduce((sum, t) => sum + (t.duration_minutes * t.quantity), 0);
        },

        get estimatedTotal() {
            const fee = this.bookingType === 'home' ? this.estimateTransportFee(this.gps.distanceKm) : 0;
            return this.subtotal + fee;
        },

        get canProceedFromStep2() {
            if (this.bookingType === 'salon') return true;
            return this.gps.lat && this.gps.distanceKm !== null && this.gps.distanceKm <= this.serviceRadiusKm && this.gps.address;
        },

        get canProceedFromStep3() {
            return this.selectedDate && this.selectedTimeSlot && this.availability.available === true;
        },

        addTreatmentDirect(t) {
            if (!t) return;
            const existing = this.selectedTreatments.find(s => s.id === t.id);
            if (existing) {
                existing.quantity++;
            } else {
                this.selectedTreatments.push({ ...t, quantity: 1 });
            }
            this.fetchDailySlots();
        },

        changeQty(index, delta) {
            const item = this.selectedTreatments[index];
            item.quantity = Math.max(1, Math.min(10, item.quantity + delta));
            this.fetchDailySlots();
        },

        removeTreatment(index) {
            this.selectedTreatments.splice(index, 1);
            this.fetchDailySlots();
        },

        haversineKm(lat1, lng1, lat2, lng2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLng = (lng2 - lng1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        },

        estimateTransportFee(distanceKm) {
            if (distanceKm === null || distanceKm === undefined) return 0;
            const firstKmFlat = 5000, perKmAfter = 3000, step = 0.5;
            if (distanceKm <= 1) return firstKmFlat;
            const remaining = Math.ceil((distanceKm - 1) / step) * step;
            return firstKmFlat + Math.round(remaining * perKmAfter);
        },

        async shareLocation() {
            this.gps.error = null;
            if (!navigator.geolocation) {
                this.gps.error = 'Browser tidak mendukung deteksi lokasi.';
                return;
            }
            this.gps.loading = true;
            navigator.geolocation.getCurrentPosition(async (pos) => {
                this.gps.lat = pos.coords.latitude;
                this.gps.lng = pos.coords.longitude;
                this.gps.distanceKm = this.haversineKm(this.salonLat, this.salonLng, this.gps.lat, this.gps.lng);
                await this.reverseGeocode();
                this.gps.loading = false;
            }, (err) => {
                this.gps.loading = false;
                this.gps.error = 'Gagal mengambil lokasi: izinkan akses lokasi di browser.';
            }, { enableHighAccuracy: true, timeout: 10000 });
        },

        async reverseGeocode() {
            if (!this.googleMapsKey) {
                this.gps.address = '';
                return;
            }
            try {
                const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${this.gps.lat},${this.gps.lng}&key=${this.googleMapsKey}&language=id`;
                const res = await fetch(url);
                const data = await res.json();
                this.gps.address = data.results?.[0]?.formatted_address ?? '';
            } catch (e) {
                this.gps.address = '';
            }
        },

        selectDate(date) {
            this.selectedDate = date;
            this.fetchDailySlots();
        },

        async fetchDailySlots() {
            if (!this.selectedDate || this.totalDurationMinutes === 0) {
                this.dailySlots = [];
                return;
            }

            this.loadingSlots = true;
            this.availability = { checking: true, available: null, message: '' };

            try {
                const params = new URLSearchParams({
                    booking_date: this.selectedDate,
                    duration_minutes: this.totalDurationMinutes,
                });
                const res = await fetch(`{{ route('user.bookings.daily-slots') }}?${params}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.dailySlots = data.slots || [];

                // Pre-select first available slot if current slot is empty or unavailable
                const currentStillValid = this.dailySlots.find(s => s.time === this.selectedTimeSlot && s.available);
                if (currentStillValid) {
                    this.selectTimeSlot(this.selectedTimeSlot, false);
                } else {
                    const firstFree = this.dailySlots.find(s => s.available);
                    if (firstFree) {
                        this.selectTimeSlot(firstFree.time, false);
                    } else {
                        this.selectedTimeSlot = null;
                        this.selectedHour = null;
                        this.selectedMinute = null;
                        this.availability = { checking: false, available: false, message: 'Semua jam di tanggal ini sedang terisi (in-progress).' };
                    }
                }
            } catch (e) {
                console.error('Fetch daily slots error:', e);
                this.dailySlots = [];
                this.availability = { checking: false, available: null, message: 'Gagal memuat ketersediaan slot.' };
            } finally {
                this.loadingSlots = false;
            }
        },

        selectTimeSlot(timeStr, notifyIfBusy = true) {
            const slot = this.dailySlots.find(s => s.time === timeStr);
            if (!slot) return;

            if (!slot.available) {
                this.selectedTimeSlot = timeStr;
                const parts = timeStr.split(':').map(Number);
                this.selectedHour = parts[0];
                this.selectedMinute = parts[1];
                this.availability = { checking: false, available: false, message: slot.reason || 'Beautician sedang dalam pengerjaan perawatan lain pada jam ini.' };
                
                if (notifyIfBusy) {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: { type: 'warning', message: `Jam ${slot.formatted_time} terisi (in-progress). Silakan pilih jam lainnya.` }
                    }));
                }
                return;
            }

            this.selectedTimeSlot = timeStr;
            const parts = timeStr.split(':').map(Number);
            this.selectedHour = parts[0];
            this.selectedMinute = parts[1];
            this.availability = { checking: false, available: true, message: 'Beautician tersedia di jam ini!' };
        },

        formatSelectedDateTime() {
            if (!this.selectedDate || !this.selectedTimeSlot) return '-';
            const d = this.availableDays.find(d => d.date === this.selectedDate);
            const label = d ? `${d.weekday}, ${d.dateNum} ${d.month}` : this.selectedDate;
            return `${label} - ${this.pad(this.selectedHour)}:${this.pad(this.selectedMinute)} WIB`;
        },

        onSubmit() {
            this.submitting = true;
        },
    }
}
</script>

@endpush
