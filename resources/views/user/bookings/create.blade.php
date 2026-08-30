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
<div class="min-h-screen bg-transparent font-body py-32 px-4 relative overflow-hidden" x-data="bookingWizard()" x-init="init()">

    {{-- Luxury Ambient Glowing Particles & Orbs --}}
    <div class="absolute top-10 left-1/4 w-96 h-96 bg-[#f45472]/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 right-10 w-80 h-80 bg-[#ffd1dc]/40 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-10 left-10 w-72 h-72 bg-[#f9c5cf]/30 rounded-full blur-3xl pointer-events-none"></div>

    {{-- Floating Beauty Sparkles --}}
    <div class="absolute top-24 left-12 text-[#f45472]/30 text-xl animate-particle-1 pointer-events-none"><i class="fa-solid fa-sparkles"></i></div>
    <div class="absolute top-40 right-20 text-[#d94060]/25 text-2xl animate-particle-2 pointer-events-none"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
    <div class="absolute top-2/3 left-20 text-[#f45472]/30 text-xl animate-particle-3 pointer-events-none"><i class="fa-solid fa-spa"></i></div>
    <div class="absolute bottom-32 right-16 text-[#d94060]/30 text-xl animate-particle-1 pointer-events-none"><i class="fa-solid fa-star"></i></div>

    <div class="max-w-3xl mx-auto relative z-10">

        {{-- Header Section --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-[#f45472]/20 shadow-sm text-xs font-semibold text-[#f45472] uppercase tracking-wider mb-3">
                <i class="fa-solid fa-[#f45472] fa-wand-magic-sparkles text-xs"></i>
                <span>Reservasi Online Yalia Beauty</span>
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
            <div x-show="step === 1" x-cloak class="min-h-[480px] bg-white/90 backdrop-blur-md rounded-3xl border border-rose-100 shadow-[0_15px_45px_rgba(244,84,114,0.08)] overflow-hidden">

                {{-- Step Header --}}
                <div class="px-6 md:px-10 pt-8 pb-6 border-b border-rose-100/60">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="font-display text-2xl font-bold text-[#5b3a29]">1. Treatment Pilihan</h2>
                            <p class="text-sm text-[#5b3a29]/70 mt-1">Perawatan terpilih — tambah perawatan lain di bawah.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-8 h-8 rounded-full bg-gradient-to-br from-[#f45472] to-[#e03e5c] text-white text-xs font-extrabold flex items-center justify-center shadow-sm" x-text="selectedTreatments.length"></span>
                            <span class="text-[#5b3a29]/60 text-xs font-semibold">Treatment</span>
                        </div>
                    </div>
                </div>

                <div class="px-6 md:px-10 py-7">

                {{-- ── Selected Treatments ── --}}
                <div class="space-y-3 mb-8">
                    <template x-for="(item, index) in selectedTreatments" :key="item.id">
                        <div class="flex items-center gap-4 rounded-2xl border border-rose-200/70 bg-gradient-to-r from-[#fff9fa] to-white p-3.5 shadow-sm hover:shadow-md transition-all group">
                            {{-- Treatment Photo --}}
                            <div class="w-14 h-14 rounded-xl overflow-hidden shrink-0 bg-rose-50 relative">
                                <img :src="item.image_url || '/images/treatment-placeholder.webp'"
                                     :alt="item.name"
                                     class="w-full h-full object-cover"
                                     x-on:error="$event.target.src='/images/treatment-placeholder.webp'">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-[#5b3a29] text-sm leading-snug truncate" x-text="item.name"></p>
                                <p class="text-xs text-[#5b3a29]/60 mt-0.5" x-text="item.category || ''"></p>
                                <p class="text-xs mt-1 flex items-center gap-1.5">
                                    <span class="font-bold text-[#f45472]" x-text="formatRupiah(item.price * item.quantity)"></span>
                                    <span class="text-[#5b3a29]/40">•</span>
                                    <span class="text-[#5b3a29]/60">⏱ <span x-text="item.duration_minutes * item.quantity"></span> mnt</span>
                                </p>
                            </div>
                            {{-- Qty Controls --}}
                            <div class="flex items-center gap-1.5 bg-[#fdf5f6] border border-rose-200/60 rounded-full px-2 py-1 shrink-0">
                                <button type="button" class="w-6 h-6 rounded-full bg-white text-[#5b3a29] font-bold text-sm shadow-xs hover:bg-rose-100 transition flex items-center justify-center" @click="changeQty(index, -1)">−</button>
                                <span class="w-5 text-center font-extrabold text-sm text-[#5b3a29]" x-text="item.quantity"></span>
                                <button type="button" class="w-6 h-6 rounded-full bg-white text-[#5b3a29] font-bold text-sm shadow-xs hover:bg-rose-100 transition flex items-center justify-center" @click="changeQty(index, 1)">+</button>
                            </div>
                            <button type="button"
                                class="w-8 h-8 rounded-full flex items-center justify-center text-rose-950 hover:text-rose-600 hover:bg-rose-100 transition shrink-0"
                                @click="removeTreatment(index)" x-show="selectedTreatments.length > 1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                {{-- ── Add Extra Treatment — 2-Column Compact Grid ── --}}
                <div class="mb-8" x-show="unselectedTreatments.length > 0">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs font-extrabold text-[#5b3a29] uppercase tracking-widest flex items-center gap-2">
                            <i class="fa-solid fa-square-plus text-[#f45472] text-xs"></i>
                            <span>Tambah Perawatan Lainnya</span>
                        </p>
                        <span class="text-xs text-[#5b3a29]/60 font-semibold" x-text="unselectedTreatments.length + ' pilihan'"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[380px] overflow-y-auto pr-1 scrollbar-hide">
                        <template x-for="t in unselectedTreatments" :key="t.id">
                            <div class="p-3.5 rounded-lg border border-rose-100 bg-white hover:bg-rose-50/50 hover:border-rose-200 transition-all duration-200 cursor-pointer flex items-center justify-between gap-3 shadow-2xs group"
                                 @click="addTreatmentDirect(t)">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap mb-1">
                                        <span class="font-bold text-[#5b3a29] text-sm group-hover:text-[#f45472] transition-colors truncate" x-text="t.name"></span>
                                        <template x-if="t.badge && t.badge.toLowerCase() !== 'none'">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-100 text-[#f45472]"
                                                  x-text="t.badge.replace(/_/g, ' ')"></span>
                                        </template>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-[#5b3a29]/70 flex-wrap">
                                        <span class="font-bold text-[#f45472]" x-text="formatRupiah(t.price)"></span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-clock text-xs text-[#ff8fa4]"></i>
                                            <span x-text="t.duration_minutes + ' mnt'"></span>
                                        </span>
                                    </div>
                                </div>

                                {{-- Add Button Icon --}}
                                <div class="shrink-0">
                                    <button type="button" aria-label="Tambah perawatan" class="w-8 h-8 rounded-lg bg-rose-50 text-[#f45472] group-hover:bg-[#f45472] group-hover:text-white font-bold text-xs transition-all flex items-center justify-center shadow-2xs">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── Subtotal Summary Bar ── --}}
                <div class="flex justify-between items-center bg-gradient-to-r from-[#fdf5f6] to-[#fff0f3] p-5 rounded-2xl border border-rose-100 mb-8">
                    <div>
                        <p class="text-xs uppercase font-extrabold text-[#5b3a29]/50 tracking-widest mb-0.5">Subtotal Treatment</p>
                        <p class="text-xs text-[#5b3a29]/70">Estimasi durasi: <span class="font-bold text-[#5b3a29]" x-text="totalDurationMinutes"></span> menit</p>
                    </div>
                    <p class="font-display text-2xl font-extrabold text-[#f45472]" x-text="formatRupiah(subtotal)"></p>
                </div>

                <button type="button"
                    class="w-full rounded-full bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-bold py-4 text-sm shadow-[0_6px_20px_rgba(244,84,114,0.3)] hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 disabled:opacity-40 disabled:transform-none"
                    :disabled="selectedTreatments.length === 0" @click="step = 2">
                    Lanjut ke Tipe Layanan &rarr;
                </button>

                </div>
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
                            <p class="text-xs uppercase font-semibold opacity-80" x-text="day.weekday"></p>
                            <p class="text-lg font-extrabold my-0.5" x-text="day.dateNum"></p>
                            <p class="text-xs opacity-80" x-text="day.month"></p>
                        </button>
                    </template>
                </div>

                {{-- Single Click Daily Time Slots Grid --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-[#5b3a29] uppercase tracking-wider">Pilih Jam Kedatangan (Langsung Klik)</label>
                        <span class="text-xs text-[#5b3a29]/60 font-semibold">Jam Operasional 08:00 - 20:00 WIB</span>
                    </div>

                    {{-- Skeleton Loading Slots Grid --}}
                    <div x-show="loadingSlots" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2.5 mb-4">
                        @for ($i = 0; $i < 12; $i++)
                            <x-skeleton class="aspect-[4/3] w-full rounded-lg" />
                        @endfor
                    </div>

                    {{-- All Time Slots Grid --}}
                    <div x-show="!loadingSlots" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2.5">
                        <template x-for="slot in dailySlots" :key="slot.time">
                            <button type="button" 
                                    @click="selectTimeSlot(slot.time)"
                                    class="relative rounded-xl p-2 min-h-[64px] border-2 transition-all flex flex-col items-center justify-center gap-1 group text-center overflow-hidden w-full"
                                    :class="{
                                        'border-[#f45472] bg-gradient-to-r from-[#f45472] to-[#e03e5c] text-white font-extrabold shadow-md scale-[1.02]': selectedTimeSlot === slot.time && slot.available,
                                        'border-rose-200 bg-white text-[#5b3a29] hover:border-[#f45472] hover:bg-rose-50 shadow-xs': selectedTimeSlot !== slot.time && slot.available,
                                        'border-rose-200/80 bg-rose-50/80 text-rose-800 opacity-80': !slot.available
                                    }">
                                <div class="flex items-center justify-center gap-1 w-full px-1">
                                    <span class="text-xs sm:text-sm font-extrabold truncate" x-text="slot.formatted_time"></span>
                                    <i x-show="selectedTimeSlot === slot.time && slot.available" class="fa-solid fa-check text-xs shrink-0"></i>
                                </div>

                                <span x-show="slot.available" class="text-xs uppercase font-bold tracking-wider truncate w-full" 
                                      :class="selectedTimeSlot === slot.time ? 'text-white/90' : 'text-emerald-600'">Tersedia</span>

                                <span x-show="!slot.available" class="w-full text-xs uppercase font-bold tracking-wider text-rose-800 bg-rose-200/70 px-1 py-0.5 rounded flex items-center justify-center gap-1 truncate">
                                    <i class="fa-solid fa-lock text-xs shrink-0"></i>
                                    <span class="truncate">TUTUP</span>
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
                    <p class="mt-1 text-xs text-[#5b3a29]/70 flex items-center gap-1.5" x-show="loadingSlots">
                        <i class="fa-solid fa-spinner fa-spin text-xs"></i>
                        <span>Mengecek ketersediaan beautician...</span>
                    </p>
                    <p class="mt-1 text-xs text-emerald-600 font-bold flex items-center gap-1.5" x-show="!loadingSlots && selectedTimeSlot && availability.available === true">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Beautician siap dan tersedia di jam ini!</span>
                    </p>

                    <div class="mt-2.5 p-3 rounded-xl bg-rose-200/60 border border-rose-300/80 text-rose-900 text-xs font-medium flex items-start gap-2.5" 
                         x-show="!loadingSlots && selectedTimeSlot && availability.available === false">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm shrink-0 mt-0.5"></i>
                        <div>
                            <span class="font-bold block text-rose-900 mb-0.5">Slot Tidak Tersedia</span>
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
                    
                    {{-- ── CUSTOM VOUCHER DROPDOWN SELECTOR ── --}}
                    <div class="py-3 px-4 my-2 rounded-2xl bg-gradient-to-r from-rose-50/80 to-amber-50/60 border border-rose-200/80 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-extrabold text-[#5b3a29] flex items-center gap-1.5">
                                <i class="fas fa-[#f45472] fa-ticket-alt"></i>
                                <span>Voucher Promo / Gratis Ongkir:</span>
                            </label>
                            <a href="{{ route('user.vouchers.index') }}" target="_blank" class="text-xs font-bold text-[#f45472] hover:underline flex items-center gap-1">
                                <span>Klaim Voucher</span>
                                <i class="fas fa-external-link-alt text-xs"></i>
                            </a>
                        </div>

                        <select x-model="selectedUserVoucherId"
                                class="w-full rounded-xl border border-rose-300 bg-white px-3.5 py-2.5 text-xs text-[#333] font-semibold focus:outline-none focus:ring-2 focus:ring-[#f45472] shadow-sm">
                            <option value="">-- Tanpa Voucher (Gunakan Penuh) --</option>
                            <template x-for="v in userVouchers" :key="v.id">
                                <option :value="v.id"
                                        x-text="v.code + ' - ' + v.name + (v.is_free_shipping ? ' (Gratis Ongkir)' : (v.type === 'percentage' ? ' (Diskon ' + v.value + '%)' : ' (Potongan Rp ' + v.value.toLocaleString('id-ID') + ')'))"></option>
                            </template>
                        </select>

                        {{-- Active Voucher Alert / Validation Banner --}}
                        <template x-if="selectedVoucher">
                            <div>
                                <div x-show="isVoucherValidForSubtotal" class="flex items-center justify-between text-xs font-bold bg-emerald-50 text-emerald-700 p-2.5 rounded-xl border border-emerald-200">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <i class="fas fa-circle-check text-emerald-500"></i>
                                        <span x-text="'Voucher applied: ' + selectedVoucher.code"></span>
                                    </div>
                                    <span class="font-mono text-emerald-800" x-text="'- ' + formatRupiah(discountAmount)"></span>
                                </div>
                                <div x-show="!isVoucherValidForSubtotal" class="flex items-center gap-1.5 text-xs font-bold bg-amber-50 text-amber-800 p-2.5 rounded-xl border border-amber-200">
                                    <i class="fas fa-triangle-exclamation text-amber-500"></i>
                                    <span x-text="'Min. transaksi ' + formatRupiah(selectedVoucher.min_purchase) + ' belum terpenuhi'"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Discount Line in Summary --}}
                    <div class="flex justify-between text-emerald-600 font-bold" x-show="discountAmount > 0">
                        <span>Diskon Voucher Applied</span>
                        <span x-text="'- ' + formatRupiah(discountAmount)"></span>
                    </div>

                    <div class="flex justify-between items-center text-base border-t border-rose-200/80 pt-3 mt-3">
                        <span class="font-display font-bold text-[#5b3a29]">Total Tagihan</span>
                        <span class="font-display font-extrabold text-[#f45472] text-xl" x-text="formatRupiah(estimatedTotal)"></span>
                    </div>
                </div>

                {{-- ── OPSI METODE PEMBAYARAN (CASH DP 35% VS CASHLESS) ── --}}
                <div class="mt-6 pt-4 border-t border-rose-100 space-y-3">
                    <label class="block text-xs font-extrabold text-[#5b3a29] flex items-center gap-1.5">
                        <i class="fas fa-wallet text-[#f45472]"></i>
                        <span>Pilih Skema Pembayaran:</span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Opsi Cashless (Full Payment 100%) --}}
                        <label class="relative flex flex-col p-3.5 rounded-2xl border-2 cursor-pointer transition-all duration-200"
                               :class="paymentType === 'cashless' ? 'border-[#f45472] bg-rose-50/50 shadow-sm' : 'border-gray-200 bg-white hover:border-rose-200'">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-[#5b3a29]">Cashless (Full 100%)</span>
                                <input type="radio" name="payment_type_radio" value="cashless" x-model="paymentType" class="text-[#f45472] focus:ring-[#f45472]">
                            </div>
                            <span class="text-xs text-gray-500">Bayar lunas 100% di awal via QRIS Midtrans</span>
                        </label>

                        {{-- Opsi Cash (DP 35%) --}}
                        <label class="relative flex flex-col p-3.5 rounded-2xl border-2 transition-all duration-200"
                               :class="bookingType === 'home' ? 'opacity-50 cursor-not-allowed bg-gray-50 border-gray-200' : (paymentType === 'cash' ? 'border-[#f45472] bg-rose-50/50 shadow-sm cursor-pointer' : 'border-gray-200 bg-white hover:border-rose-200 cursor-pointer')">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold text-[#5b3a29]">Cash di Salon (DP 35%)</span>
                                <input type="radio" name="payment_type_radio" value="cash" x-model="paymentType" :disabled="bookingType === 'home'" class="text-[#f45472] focus:ring-[#f45472]">
                            </div>
                            <span class="text-xs text-gray-500" x-text="bookingType === 'home' ? 'Hanya untuk kunjungan salon' : 'DP 35% via QRIS, sisa 65% tunai di tempat'"></span>
                        </label>
                    </div>

                    {{-- Breakdown Rincian DP jika memilih Cash --}}
                    <template x-if="bookingType === 'salon' && paymentType === 'cash'">
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs space-y-1.5">
                            <div class="flex justify-between font-extrabold text-amber-900">
                                <span>DP 35% (Dibayar Sekarang via QRIS):</span>
                                <span x-text="formatRupiah(Math.round(estimatedTotal * 0.35))"></span>
                            </div>
                            <div class="flex justify-between text-amber-800 font-semibold">
                                <span>Sisa 65% (Pelunasan Tunai di Salon):</span>
                                <span x-text="formatRupiah(estimatedTotal - Math.round(estimatedTotal * 0.35))"></span>
                            </div>
                            <p class="text-xs text-amber-700 italic pt-1 border-t border-amber-200/60">* Uang DP tidak dapat dikembalikan (hangus) jika pesanan dibatalkan.</p>
                        </div>
                    </template>
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
                <input type="hidden" name="payment_type" :value="paymentType">
                <input type="hidden" name="booking_date" :value="selectedDate">
                <input type="hidden" name="time_start" :value="pad(selectedHour)+':'+pad(selectedMinute)">
                <input type="hidden" name="home_latitude" :value="gps.lat">
                <input type="hidden" name="home_longitude" :value="gps.lng">
                <input type="hidden" name="home_address" :value="gps.address">
                <input type="hidden" name="user_voucher_id" :value="selectedUserVoucherId">
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
        paymentType: 'cashless',
        gps: { loading: false, error: null, lat: null, lng: null, address: '', distanceKm: null },

        salonLat: {{ config('booking.salon.latitude') }},
        salonLng: {{ config('booking.salon.longitude') }},
        serviceRadiusKm: {{ $serviceRadiusKm }},
        googleMapsKey: @json($googleMapsKey),
        transportFeeConfig: @json(config('booking.transport_fee')),

        availableDays: [],
        selectedDate: null,
        selectedTimeSlot: null,
        selectedHour: 9,
        selectedMinute: 0,
        dailySlots: [],
        loadingSlots: false,
        availability: { checking: false, available: null, message: '' },
        availabilityTimer: null,

        userVouchers: @json($userVouchersData),
        selectedUserVoucherId: null,

        notes: '',

        get selectedVoucher() {
            if (!this.selectedUserVoucherId) return null;
            return this.userVouchers.find(v => v.id == this.selectedUserVoucherId) || null;
        },

        get isVoucherValidForSubtotal() {
            if (!this.selectedVoucher) return false;
            if (this.selectedVoucher.min_purchase && this.subtotal < this.selectedVoucher.min_purchase) {
                return false;
            }
            return true;
        },

        get discountAmount() {
            const v = this.selectedVoucher;
            if (!v || !this.isVoucherValidForSubtotal) return 0;

            const transportFee = this.bookingType === 'home' ? this.estimateTransportFee(this.gps.distanceKm) : 0;

            if (v.is_free_shipping) {
                const rawDiscount = transportFee * (v.value / 100);
                return v.max_discount ? Math.min(rawDiscount, v.max_discount) : rawDiscount;
            } else if (v.type === 'percentage') {
                const rawDiscount = this.subtotal * (v.value / 100);
                return v.max_discount ? Math.min(rawDiscount, v.max_discount) : rawDiscount;
            } else { // fixed
                return Math.min(v.value, this.subtotal);
            }
        },

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
            return Math.max(0, this.subtotal + fee - this.discountAmount);
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
            const firstKmFlat = Number(this.transportFeeConfig?.first_km_flat ?? 5000);
            const perKmAfter = Number(this.transportFeeConfig?.per_km_after ?? 3000);
            const step = Number(this.transportFeeConfig?.round_up_step_km ?? 0.5);
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

        _fetchSlotsTimer: null,
        fetchDailySlots() {
            if (this._fetchSlotsTimer) clearTimeout(this._fetchSlotsTimer);
            this._fetchSlotsTimer = setTimeout(() => this._doFetchDailySlots(), 150);
        },

        async _doFetchDailySlots() {
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
                        this.availability = { checking: false, available: false, message: 'Semua slot di tanggal ini sudah penuh atau tidak tersedia.' };
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
                        detail: { type: 'warning', message: `Jam ${slot.formatted_time} tidak tersedia. Silakan pilih jam lainnya.` }
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
