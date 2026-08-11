<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.beauticians.index') }}" class="p-2 rounded-full bg-white border border-rose-200 text-gray-600 hover:text-rose-600 hover:bg-rose-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
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
