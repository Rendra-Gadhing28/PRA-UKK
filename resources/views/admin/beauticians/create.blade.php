<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.beauticians.index') }}" class="p-2 rounded-full bg-white border border-rose-200 text-gray-600 hover:text-rose-600 hover:bg-rose-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-1.5 h-7 bg-gradient-to-b from-[#b01f44] to-[#f45472] rounded-full inline-block shadow-[0_2px_10px_rgba(244,84,114,0.45)]"></span>
                    Tambah Staf Beautician Baru
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Daftarkan beautician / terapis baru untuk penugasan reservasi salon</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100">
                <form method="POST" action="{{ route('admin.beauticians.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Nama Beautician --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Nama Lengkap *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Mawar Indah, S.Kmk" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nomor Telepon --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Nomor Telepon / WhatsApp</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('phone') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="mawar@yalia.com" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Upload Foto Profil --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Foto Profil Staf (WebP/JPG/PNG)</label>
                            <input type="file" name="photo" accept="image/*" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border border-gray-200 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#f45472]">
                            @error('photo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Bio / Spesialisasi --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Bio / Catatan Spesialisasi *</label>
                            <textarea name="bio" rows="3" required placeholder="Jelaskan keahlian beautician ini (misal: Spesialis Facial Glow & Hair Spa)..." 
                                      class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">{{ old('bio') }}</textarea>
                            @error('bio') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status Aktif --}}
                        <div class="md:col-span-2 flex items-center gap-3 pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded-md border-gray-300 text-[#f45472] focus:ring-[#f45472]">
                            <label for="is_active" class="text-sm font-bold text-gray-800">Aktifkan beautician ini untuk dapat menerima penugasan booking</label>
                        </div>

                    </div>

                    {{-- Jadwal Hari Kerja & Jam Operasional --}}
                    @php
                        $defaultDays = [
                            1 => ['active' => true, 'start' => '09:00', 'end' => '18:00'],
                            2 => ['active' => true, 'start' => '09:00', 'end' => '18:00'],
                            3 => ['active' => true, 'start' => '09:00', 'end' => '18:00'],
                            4 => ['active' => true, 'start' => '09:00', 'end' => '18:00'],
                            5 => ['active' => true, 'start' => '09:00', 'end' => '18:00'],
                            6 => ['active' => true, 'start' => '09:00', 'end' => '18:00'],
                            0 => ['active' => false, 'start' => '09:00', 'end' => '18:00'],
                        ];
                    @endphp
                    <div class="pt-6 border-t border-rose-100/80" x-data="{
                        days: @json($defaultDays),
                        setPreset(preset) {
                            const presets = {
                                mon_sat: [1, 2, 3, 4, 5, 6],
                                mon_fri: [1, 2, 3, 4, 5],
                                weekend: [6, 0],
                                all: [0, 1, 2, 3, 4, 5, 6],
                                clear: []
                            };
                            const activeList = presets[preset] || [];
                            const next = {};
                            [0, 1, 2, 3, 4, 5, 6].forEach(function(d) {
                                const isActive = activeList.includes(d);
                                const cur = (this.days && this.days[d]) ? this.days[d] : {};
                                next[d] = {
                                    active: isActive,
                                    start: cur.start || '09:00',
                                    end: cur.end || '18:00'
                                };
                            }.bind(this));
                            this.days = next;
                        },
                        toggleDay(d, isChecked) {
                            if (!this.days[d]) {
                                this.days[d] = { active: false, start: '09:00', end: '18:00' };
                            }
                            this.days[d].active = Boolean(isChecked);
                            if (isChecked) {
                                if (!this.days[d].start) this.days[d].start = '09:00';
                                if (!this.days[d].end) this.days[d].end = '18:00';
                            }
                        }
                    }">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                            <div>
                                <h3 class="text-base font-bold font-headline text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-[#f45472]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Jadwal Hari Kerja & Jam Tugas Staf
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">Tentukan hari dan jam kerja. Slot booking pelanggan hanya akan terbuka di hari staf bertugas.</p>
                            </div>

                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="text-[10px] font-bold uppercase text-gray-400 mr-1">Preset Cepat:</span>
                                <button type="button" @click="setPreset('mon_sat')" class="px-2.5 py-1 text-xs rounded-lg font-bold bg-rose-50 text-[#f45472] hover:bg-rose-100 transition-colors">Senin–Sabtu</button>
                                <button type="button" @click="setPreset('mon_fri')" class="px-2.5 py-1 text-xs rounded-lg font-bold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">Senin–Jumat</button>
                                <button type="button" @click="setPreset('weekend')" class="px-2.5 py-1 text-xs rounded-lg font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 transition-colors">Sabtu–Minggu</button>
                                <button type="button" @click="setPreset('all')" class="px-2.5 py-1 text-xs rounded-lg font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-colors">Semua Hari</button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 mt-4">
                            @foreach($days as $dayNum => $dayName)
                            <div class="p-3.5 rounded-2xl border transition-all duration-200"
                                 :class="days[{{ $dayNum }}] && days[{{ $dayNum }}].active ? 'bg-white border-rose-200 shadow-xs' : 'bg-gray-50/70 border-gray-200 opacity-60'">
                                <div class="flex items-center justify-between gap-2 mb-2.5">
                                    <label class="flex items-center gap-2.5 cursor-pointer">
                                        <input type="checkbox" name="schedules[{{ $dayNum }}][is_working]" value="1" 
                                               :checked="days[{{ $dayNum }}] && days[{{ $dayNum }}].active"
                                               @change="toggleDay({{ $dayNum }}, $event.target.checked)"
                                               class="w-4 h-4 rounded text-[#f45472] border-gray-300 focus:ring-[#f45472]">
                                        <span class="text-xs font-bold text-gray-900 {{ $dayNum === 0 ? 'text-rose-600' : '' }}">{{ $dayName }}</span>
                                    </label>

                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider"
                                          :class="days[{{ $dayNum }}] && days[{{ $dayNum }}].active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600'"
                                          x-text="days[{{ $dayNum }}] && days[{{ $dayNum }}].active ? 'Masuk Kerja' : 'Libur / Off'">
                                    </span>
                                </div>

                                <div class="flex items-center gap-2 pt-2 border-t border-gray-100" x-show="days[{{ $dayNum }}] && days[{{ $dayNum }}].active">
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Jam Masuk</label>
                                        <input type="time" name="schedules[{{ $dayNum }}][start_time]" 
                                               x-model="days[{{ $dayNum }}].start"
                                               class="w-full text-xs py-1.5 px-2.5 rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                                    </div>
                                    <span class="text-xs text-gray-400 self-end mb-1.5">—</span>
                                    <div class="flex-1">
                                        <label class="block text-[10px] font-semibold text-gray-500 mb-0.5">Jam Pulang</label>
                                        <input type="time" name="schedules[{{ $dayNum }}][end_time]" 
                                               x-model="days[{{ $dayNum }}].end"
                                               class="w-full text-xs py-1.5 px-2.5 rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.beauticians.index') }}" class="px-6 py-3 rounded-full bg-gray-100 text-gray-700 font-bold text-xs hover:bg-gray-200 transition-all">
                            Batal
                        </a>
                        <button type="submit" class="px-8 py-3 rounded-full bg-[#f45472] text-white font-bold text-xs hover:bg-[#d93856] shadow-md hover:shadow-lg transition-all">
                            Simpan Staf Beautician
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-admin-layout>
