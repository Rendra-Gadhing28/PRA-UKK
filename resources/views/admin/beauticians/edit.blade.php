<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('admin.beauticians.index') }}" 
                   class="p-2.5 rounded-full bg-white border border-rose-200/80 text-rose-900 hover:text-[#b01f44] hover:bg-rose-50/80 hover:border-rose-300 transition-all duration-200 shadow-xs"
                   title="Kembali ke Daftar Beautician">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-stone-900 tracking-tight flex items-center gap-3 font-headline">
                        <span class="w-1.5 h-7 bg-gradient-to-b from-[#b01f44] to-[#f45472] rounded-full inline-block shadow-[0_2px_10px_rgba(244,84,114,0.45)]"></span>
                        Edit Profil Beautician — {{ $beautician->name }}
                    </h2>
                    <p class="text-xs text-stone-500 mt-0.5">Konfigurasi data staf, spesialisasi layanan, foto profil, dan jam tugas operasional.</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.beauticians.show', $beautician->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-rose-200 text-xs font-bold text-rose-900 hover:bg-rose-50 hover:text-[#b01f44] hover:border-rose-300 transition-all shadow-xs">
                    <svg class="w-4 h-4 text-[#b01f44]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Lihat Kinerja & Riwayat</span>
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $initialDays = [];
        foreach([0,1,2,3,4,5,6] as $d) {
            $sched = $schedules->get($d);
            $initialDays[$d] = [
                'active' => $sched ? (bool) $sched->is_working : false,
                'start' => $sched && $sched->start_time ? substr((string)$sched->start_time, 0, 5) : '09:00',
                'end' => $sched && $sched->end_time ? substr((string)$sched->end_time, 0, 5) : '18:00',
            ];
        }
    @endphp

    {{-- Dedicated Script Definition for Beautician Edit Alpine Data --}}
    <script>
        window.beauticianEditForm = function(initialDays, initialActive, fallbackPhotoUrl) {
            return {
                days: initialDays || {},
                isActive: Boolean(initialActive),
                photoPreview: null,
                fallbackPhotoUrl: fallbackPhotoUrl,

                previewImage: function(event) {
                    const file = event.target.files && event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        const self = this;
                        reader.onload = function(e) {
                            self.photoPreview = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },

                removePhotoPreview: function() {
                    this.photoPreview = null;
                    const fileInput = document.getElementById('beautician_photo_input');
                    if (fileInput) fileInput.value = '';
                },

                setPreset: function(preset) {
                    const presets = {
                        mon_sat: [1, 2, 3, 4, 5, 6],
                        mon_fri: [1, 2, 3, 4, 5],
                        weekend: [6, 0],
                        all: [0, 1, 2, 3, 4, 5, 6],
                        clear: []
                    };
                    const activeList = presets[preset] || [];
                    const next = {};
                    for (let d = 0; d <= 6; d++) {
                        const isDayActive = activeList.indexOf(d) !== -1;
                        const cur = (this.days && this.days[d]) ? this.days[d] : {};
                        next[d] = {
                            active: isDayActive,
                            start: cur.start || '09:00',
                            end: cur.end || '18:00'
                        };
                    }
                    this.days = next;
                },

                toggleDay: function(d, isChecked) {
                    if (!this.days[d]) {
                        this.days[d] = { active: false, start: '09:00', end: '18:00' };
                    }
                    const current = this.days[d];
                    this.days[d] = {
                        active: Boolean(isChecked),
                        start: current.start || '09:00',
                        end: current.end || '18:00'
                    };
                    this.days = Object.assign({}, this.days);
                },

                activeDaysCount: function() {
                    let count = 0;
                    if (!this.days) return 0;
                    for (const key in this.days) {
                        if (this.days[key] && this.days[key].active) {
                            count++;
                        }
                    }
                    return count;
                },

                totalWeeklyHours: function() {
                    let total = 0;
                    if (!this.days) return 0;
                    for (const key in this.days) {
                        const d = this.days[key];
                        if (d && d.active && d.start && d.end) {
                            const sParts = d.start.split(':');
                            const eParts = d.end.split(':');
                            const sH = Number(sParts[0]) || 0;
                            const sM = Number(sParts[1]) || 0;
                            const eH = Number(eParts[0]) || 0;
                            const eM = Number(eParts[1]) || 0;
                            const diff = (eH + (eM / 60)) - (sH + (sM / 60));
                            if (diff > 0) total += diff;
                        }
                    }
                    return Math.round(total * 10) / 10;
                }
            };
        };
    </script>

    <div class="py-8" x-data="window.beauticianEditForm(@js($initialDays), {{ old('is_active', $beautician->is_active) ? 'true' : 'false' }}, '{{ $beautician->photo_url }}')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" 
                  action="{{ route('admin.beauticians.update', $beautician->id) }}" 
                  enctype="multipart/form-data" 
                  class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    {{-- ========================================== --}}
                    {{-- KOLOM KIRI: Form Utama (Identitas + Jadwal) --}}
                    {{-- ========================================== --}}
                    <div class="lg:col-span-8 space-y-8">
                        
                        {{-- CARD 1: Identitas & Keahlian Staf --}}
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-100/90 shadow-[0_4px_24px_-4px_rgba(176,31,68,0.06)] space-y-6">
                            <div class="flex items-center gap-3.5 pb-4 border-b border-rose-100/70">
                                <div class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-[#b01f44] shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold font-headline text-stone-900">Informasi & Kontak Staf</h3>
                                    <p class="text-xs text-stone-500">Data identitas resmi terapis yang terdaftar di sistem salon</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                {{-- Nama Beautician --}}
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">
                                        Nama Lengkap Beautician <span class="text-[#b01f44]">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <input type="text" 
                                               name="name" 
                                               value="{{ old('name', $beautician->name) }}" 
                                               required 
                                               placeholder="Contoh: Sarah Angelina, S.Kmk"
                                               class="w-full pl-10 pr-4 py-3 text-sm rounded-2xl border-stone-200 bg-stone-50/40 text-stone-900 focus:bg-white focus:border-[#b01f44] focus:ring-2 focus:ring-[#b01f44]/15 transition-all">
                                    </div>
                                    @error('name') 
                                        <p class="text-xs text-rose-600 mt-1.5 font-medium flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $message }}
                                        </p> 
                                    @enderror
                                </div>

                                {{-- Nomor Telepon --}}
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">
                                        Nomor Telepon / WhatsApp
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input type="text" 
                                               name="phone" 
                                               value="{{ old('phone', $beautician->phone) }}" 
                                               placeholder="081234567890"
                                               class="w-full pl-10 pr-4 py-3 text-sm rounded-2xl border-stone-200 bg-stone-50/40 text-stone-900 focus:bg-white focus:border-[#b01f44] focus:ring-2 focus:ring-[#b01f44]/15 transition-all">
                                    </div>
                                    @error('phone') 
                                        <p class="text-xs text-rose-600 mt-1.5 font-medium flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $message }}
                                        </p> 
                                    @enderror
                                </div>

                                {{-- Alamat Email --}}
                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-stone-700 mb-2">
                                        Alamat Email Staf
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-stone-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </div>
                                        <input type="email" 
                                               name="email" 
                                               value="{{ old('email', $beautician->email) }}" 
                                               placeholder="sarah@yaliabeauty.com"
                                               class="w-full pl-10 pr-4 py-3 text-sm rounded-2xl border-stone-200 bg-stone-50/40 text-stone-900 focus:bg-white focus:border-[#b01f44] focus:ring-2 focus:ring-[#b01f44]/15 transition-all">
                                    </div>
                                    @error('email') 
                                        <p class="text-xs text-rose-600 mt-1.5 font-medium flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $message }}
                                        </p> 
                                    @enderror
                                </div>

                                {{-- Bio / Spesialisasi --}}
                                <div class="md:col-span-2">
                                    <div class="flex items-center justify-between mb-2">
                                        <label class="block text-xs font-bold uppercase tracking-wider text-stone-700">
                                            Bio & Catatan Keahlian <span class="text-[#b01f44]">*</span>
                                        </label>
                                        <span class="text-xs text-stone-400">Ditampilkan kepada pelanggan</span>
                                    </div>
                                    <textarea name="bio" 
                                              rows="4" 
                                              required 
                                              placeholder="Tuliskan pengalaman, keahlian khusus (contoh: Facial Treatment, Lash Lift, Hair Spa), atau sertifikasi beautician..."
                                              class="w-full px-4 py-3 text-sm rounded-2xl border-stone-200 bg-stone-50/40 text-stone-900 focus:bg-white focus:border-[#b01f44] focus:ring-2 focus:ring-[#b01f44]/15 transition-all leading-relaxed">{{ old('bio', $beautician->bio) }}</textarea>
                                    @error('bio') 
                                        <p class="text-xs text-rose-600 mt-1.5 font-medium flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $message }}
                                        </p> 
                                    @enderror
                                </div>

                            </div>
                        </div>

                        {{-- CARD 2: Jadwal Operasional & Jam Kerja --}}
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-100/90 shadow-[0_4px_24px_-4px_rgba(176,31,68,0.06)] space-y-6">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-rose-100/70">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-10 h-10 rounded-2xl bg-rose-50 flex items-center justify-center text-[#b01f44] shadow-xs">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold font-headline text-stone-900">Jadwal Tugas & Jam Operasional</h3>
                                        <p class="text-xs text-stone-500">Slot booking pelanggan hanya akan tersedia pada hari & jam aktif staf bertugas.</p>
                                    </div>
                                </div>

                                {{-- Presets Pill Bar --}}
                                <div class="flex flex-wrap items-center gap-1.5 bg-rose-50/50 p-1.5 rounded-2xl border border-rose-100">
                                    <span class="text-xs font-bold uppercase tracking-wider text-rose-900/70 px-2">Preset:</span>
                                    <button type="button" 
                                            @click="setPreset('mon_sat')" 
                                            class="px-3 py-1.5 text-xs rounded-xl font-bold bg-white text-[#b01f44] hover:bg-rose-100/80 shadow-xs border border-rose-200/60 transition-all">
                                        Senin–Sabtu
                                    </button>
                                    <button type="button" 
                                            @click="setPreset('mon_fri')" 
                                            class="px-3 py-1.5 text-xs rounded-xl font-bold bg-white text-stone-700 hover:bg-stone-100 shadow-xs border border-stone-200/60 transition-all">
                                        Senin–Jumat
                                    </button>
                                    <button type="button" 
                                            @click="setPreset('weekend')" 
                                            class="px-3 py-1.5 text-xs rounded-xl font-bold bg-white text-amber-700 hover:bg-amber-50 shadow-xs border border-amber-200/60 transition-all">
                                        Weekend
                                    </button>
                                    <button type="button" 
                                            @click="setPreset('all')" 
                                            class="px-3 py-1.5 text-xs rounded-xl font-bold bg-white text-emerald-800 hover:bg-emerald-50 shadow-xs border border-emerald-200/60 transition-all">
                                        Semua Hari
                                    </button>
                                    <button type="button" 
                                            @click="setPreset('clear')" 
                                            class="px-2.5 py-1.5 text-xs rounded-xl font-medium text-rose-800/70 hover:text-[#b01f44] hover:bg-rose-100/70 transition-all"
                                            title="Reset semua hari menjadi libur">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            {{-- 7 Days Interactive Schedule Grid --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($days as $dayNum => $dayName)
                                <div class="p-4 rounded-2xl border transition-all duration-200"
                                     :class="days[{{ $dayNum }}] && days[{{ $dayNum }}].active 
                                        ? 'bg-white border-rose-300 shadow-[0_2px_12px_-2px_rgba(176,31,68,0.08)]' 
                                        : 'bg-stone-50/70 border-stone-200/80 opacity-65'">
                                    
                                    <div class="flex items-center justify-between gap-3 mb-3">
                                        <label class="flex items-center gap-3 cursor-pointer select-none">
                                            <input type="checkbox" 
                                                   name="schedules[{{ $dayNum }}][is_working]" 
                                                   value="1" 
                                                   :checked="days[{{ $dayNum }}] && days[{{ $dayNum }}].active"
                                                   @change="toggleDay({{ $dayNum }}, $event.target.checked)"
                                                   class="w-4 h-4 rounded-md text-[#b01f44] border-stone-300 focus:ring-[#b01f44]">
                                            <span class="text-sm font-bold {{ $dayNum === 0 ? 'text-[#b01f44]' : 'text-stone-900' }}">
                                                {{ $dayName }}
                                            </span>
                                        </label>

                                        <span class="text-xs font-bold px-2.5 py-1 rounded-full uppercase tracking-wider"
                                              :class="days[{{ $dayNum }}] && days[{{ $dayNum }}].active 
                                                ? 'bg-emerald-100 text-emerald-800' 
                                                : 'bg-stone-200 text-stone-600'"
                                              x-text="days[{{ $dayNum }}] && days[{{ $dayNum }}].active ? 'Masuk Kerja' : 'Libur / Off'">
                                        </span>
                                    </div>

                                    <div class="pt-3 border-t border-rose-100/80" x-show="days[{{ $dayNum }}] && days[{{ $dayNum }}].active">
                                        <div class="grid grid-cols-2 gap-3 items-center">
                                            <div>
                                                <label class="block text-xs font-semibold text-stone-500 mb-1">Jam Masuk</label>
                                                <div class="relative">
                                                    <input type="time" 
                                                           name="schedules[{{ $dayNum }}][start_time]" 
                                                           x-model="days[{{ $dayNum }}].start"
                                                           class="w-full text-xs py-2 px-3 rounded-xl border-stone-200 bg-stone-50/50 focus:bg-white focus:border-[#b01f44] focus:ring-2 focus:ring-[#b01f44]/15">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-semibold text-stone-500 mb-1">Jam Pulang</label>
                                                <div class="relative">
                                                    <input type="time" 
                                                           name="schedules[{{ $dayNum }}][end_time]" 
                                                           x-model="days[{{ $dayNum }}].end"
                                                           class="w-full text-xs py-2 px-3 rounded-xl border-stone-200 bg-stone-50/50 focus:bg-white focus:border-[#b01f44] focus:ring-2 focus:ring-[#b01f44]/15">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Summary Bar Akumulasi Jadwal --}}
                            <div class="p-4 rounded-2xl bg-rose-50/50 border border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-2 text-xs font-semibold text-stone-700">
                                    <svg class="w-4 h-4 text-[#b01f44]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Ringkasan Beban Tugas:</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs">
                                    <span class="px-3 py-1 rounded-full bg-white font-bold text-stone-900 border border-rose-200/70 shadow-2xs">
                                        <span class="text-[#b01f44]" x-text="activeDaysCount()"></span> Hari Bertugas
                                    </span>
                                    <span class="px-3 py-1 rounded-full bg-white font-bold text-stone-900 border border-rose-200/70 shadow-2xs">
                                        ~<span class="text-[#b01f44]" x-text="totalWeeklyHours()"></span> Jam / Minggu
                                    </span>
                                </div>
                            </div>

                        </div>

                        {{-- Action Buttons --}}
                        <div class="p-5 rounded-3xl bg-white border border-rose-100/90 shadow-[0_4px_24px_-4px_rgba(176,31,68,0.06)] flex items-center justify-between gap-4">
                            <a href="{{ route('admin.beauticians.index') }}" 
                               class="px-6 py-3 rounded-full bg-stone-100 text-stone-700 font-bold text-xs hover:bg-stone-200 transition-all">
                                Batal & Kembali
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-8 py-3.5 rounded-full bg-[#b01f44] hover:bg-[#8f1735] text-white font-bold text-xs shadow-md hover:shadow-lg active:scale-[0.98] transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simpan Perubahan Data</span>
                            </button>
                        </div>

                    </div>

                    {{-- ========================================== --}}
                    {{-- KOLOM KANAN: Sidebar (Foto, Status, Stats)  --}}
                    {{-- ========================================== --}}
                    <div class="lg:col-span-4 space-y-6">

                        {{-- CARD 1: Foto Profil & Avatar Studio --}}
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-rose-100/90 shadow-[0_4px_24px_-4px_rgba(176,31,68,0.06)] space-y-5 text-center">
                            <div class="flex items-center justify-between pb-3 border-b border-rose-100/70 text-left">
                                <div>
                                    <h3 class="text-base font-bold font-headline text-stone-900">Foto Profil Staf</h3>
                                    <p class="text-xs text-stone-500">Avatar resmi pada kartu booking</p>
                                </div>
                                <span class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-[#b01f44]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </span>
                            </div>

                            {{-- Avatar Container --}}
                            <div class="relative mx-auto w-32 h-32">
                                <div class="w-full h-full rounded-full overflow-hidden border-4 border-rose-100 shadow-md bg-rose-50 flex items-center justify-center">
                                    <img :src="photoPreview ? photoPreview : fallbackPhotoUrl" 
                                         alt="{{ $beautician->name }}" 
                                         class="w-full h-full object-cover">
                                </div>

                                {{-- Status Dot Badge on Avatar --}}
                                <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full border-2 border-white shadow-xs transition-colors duration-200"
                                      :class="isActive ? 'bg-emerald-500' : 'bg-rose-400'"
                                      :title="isActive ? 'Staf Aktif' : 'Staf Nonaktif'">
                                </span>
                            </div>

                            <div class="space-y-3">
                                <label for="beautician_photo_input" 
                                       class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-full bg-rose-50 text-[#b01f44] border border-rose-200 hover:bg-rose-100/80 font-bold text-xs cursor-pointer transition-all shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    <span>Pilih / Ganti Foto</span>
                                </label>
                                <input type="file" 
                                       id="beautician_photo_input" 
                                       name="photo" 
                                       accept="image/jpeg,image/png,image/webp,image/jpg" 
                                       @change="previewImage($event)"
                                       class="sr-only">

                                <button type="button" 
                                        x-show="photoPreview" 
                                        style="display: none;"
                                        @click="removePhotoPreview()" 
                                        class="inline-flex items-center gap-1.5 text-xs text-rose-600 hover:text-rose-800 font-semibold transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Batal Ganti Foto</span>
                                </button>

                                <p class="text-xs text-stone-400 leading-normal">
                                    Format disarankan: JPG, PNG, atau WebP persegi. Ukuran berkas maksimal 2MB.
                                </p>
                                @error('photo') 
                                    <p class="text-xs text-rose-600 font-medium">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>

                        {{-- CARD 2: Status Ketersediaan & Penugasan --}}
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-rose-100/90 shadow-[0_4px_24px_-4px_rgba(176,31,68,0.06)] space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-rose-100/70">
                                <div>
                                    <h3 class="text-base font-bold font-headline text-stone-900">Status Penugasan</h3>
                                    <p class="text-xs text-stone-500">Izin alokasi pesanan & jadwal</p>
                                </div>
                                <span class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center text-[#b01f44]">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                            </div>

                            <input type="hidden" name="is_active" :value="isActive ? 1 : 0" value="{{ old('is_active', $beautician->is_active) ? 1 : 0 }}">

                            <div @click="isActive = !isActive" 
                                 class="p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200 select-none flex items-start gap-3.5"
                                 :class="isActive 
                                    ? 'bg-emerald-50/50 border-emerald-300/80 shadow-xs' 
                                    : 'bg-rose-50/50 border-rose-200 shadow-xs'">
                                
                                <div class="mt-0.5">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                         :class="isActive ? 'border-emerald-600 bg-emerald-600' : 'border-stone-400 bg-white'">
                                        <svg x-show="isActive" class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </div>

                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold" :class="isActive ? 'text-emerald-900' : 'text-rose-900'" x-text="isActive ? 'Staf Aktif Bertugas' : 'Staf Sedang Off / Cuti'"></span>
                                        <span class="w-2 h-2 rounded-full" :class="isActive ? 'bg-emerald-500 animate-pulse' : 'bg-rose-400'"></span>
                                    </div>
                                    <p class="text-xs text-stone-600 mt-1 leading-relaxed" x-text="isActive ? 'Beautician ini siap menerima penugasan booking online & salon visit.' : 'Beautician disembunyikan sementara dari pilihan reservasi pelanggan.'">
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 3: Ringkasan Kinerja & Quick Link --}}
                        <div class="bg-white rounded-3xl p-6 border border-rose-100/90 shadow-[0_4px_24px_-4px_rgba(176,31,68,0.06)] space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-rose-900/80">Kinerja Saat Ini</h4>
                            
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between p-3 rounded-2xl bg-white border border-rose-100 shadow-2xs">
                                    <span class="text-xs text-stone-600">Total Reservasi Selesai</span>
                                    <span class="text-xs font-bold text-stone-900">{{ number_format($beautician->total_bookings) }} Layanan</span>
                                </div>
                                <div class="flex items-center justify-between p-3 rounded-2xl bg-white border border-rose-100 shadow-2xs">
                                    <span class="text-xs text-stone-600">Terdaftar Sejak</span>
                                    <span class="text-xs font-bold text-stone-900">{{ $beautician->created_at ? $beautician->created_at->format('d M Y') : '-' }}</span>
                                </div>
                            </div>

                            <a href="{{ route('admin.beauticians.show', $beautician->id) }}" 
                               class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-full bg-white border border-rose-200 text-xs font-bold text-rose-900 hover:bg-rose-50 hover:text-[#b01f44] transition-all shadow-2xs">
                                <span>Lihat Profil Lengkap</span>
                                <svg class="w-3.5 h-3.5 text-[#b01f44]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
