<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.treatments.index') }}" class="p-2 rounded-full bg-white border border-rose-200 text-rose-950 hover:text-rose-600 hover:bg-rose-50 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    Tambah Treatment Baru
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Tambahkan layanan perawatan salon baru ke katalog Yalia Beauty</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100">
                <form method="POST" action="{{ route('admin.treatments.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Nama Treatment --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Nama Treatment *</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Creambath Deep Nourishing" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kategori (Dari Database) --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Kategori *</label>
                            <select name="category_id" required class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    @php
                                        $catId = is_object($cat) ? $cat->id : (is_array($cat) ? ($cat['id'] ?? '') : $cat);
                                        $catName = is_object($cat) ? $cat->name : (is_array($cat) ? ($cat['name'] ?? '') : $cat);
                                        $catIcon = is_object($cat) ? ($cat->icon ?? '🌸') : (is_array($cat) ? ($cat['icon'] ?? '🌸') : '🌸');
                                    @endphp
                                    <option value="{{ $catId }}" {{ old('category_id') == $catId ? 'selected' : '' }}>
                                        {{ $catIcon }} {{ $catName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Badge --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Badge Promo *</label>
                            <select name="badge" required class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                                <option value="none" {{ old('badge') === 'none' ? 'selected' : '' }}>None (Tidak Ada)</option>
                                <option value="best_seller" {{ old('badge') === 'best_seller' ? 'selected' : '' }}>Best Seller</option>
                                <option value="new" {{ old('badge') === 'new' ? 'selected' : '' }}>New (Terbaru)</option>
                                <option value="promo" {{ old('badge') === 'promo' ? 'selected' : '' }}>Promo</option>
                            </select>
                            @error('badge') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Harga (Rp) --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Harga (Rp) *</label>
                            <input type="number" name="price" value="{{ old('price') }}" required min="0" step="1000" placeholder="150000" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('price') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Durasi (Menit) --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Durasi (Menit) *</label>
                            <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" required min="5" step="5" placeholder="60" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('duration_minutes') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Upload Gambar --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Gambar Treatment (Format WebP/JPG/PNG)</label>
                            <input type="file" name="image" accept="image/*" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border border-gray-200 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#f45472]">
                            @error('image') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Deskripsi --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Deskripsi Layanan *</label>
                            <textarea name="description" rows="3" required placeholder="Jelaskan detail perawatan ini..." 
                                      class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">{{ old('description') }}</textarea>
                            @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Manfaat / Benefits --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Manfaat Treatment (Opsional)</label>
                            <textarea name="benefits" rows="2" placeholder="Contoh: Menutrisi akar rambut, merelaksasi kulit kepala" 
                                      class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">{{ old('benefits') }}</textarea>
                        </div>

                        {{-- Status Aktif --}}
                        <div class="md:col-span-2 flex items-center gap-3 pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded-md border-gray-300 text-[#f45472] focus:ring-[#f45472]">
                            <label for="is_active" class="text-sm font-bold text-gray-800">Aktifkan treatment ini agar langsung tampil untuk pelanggan</label>
                        </div>

                    </div>

                    {{-- Form Actions --}}
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.treatments.index') }}" class="px-6 py-3 rounded-full bg-gray-100 text-gray-700 font-bold text-xs hover:bg-gray-200 transition-all">
                            Batal
                        </a>
                        <button type="submit" class="px-8 py-3 rounded-full bg-[#f45472] text-white font-bold text-xs hover:bg-[#d93856] shadow-md hover:shadow-lg transition-all">
                            Simpan Treatment Baru
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-admin-layout>
