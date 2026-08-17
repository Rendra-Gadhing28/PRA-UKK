<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.vouchers.index') }}" class="p-2 rounded-full bg-white border border-rose-200 text-gray-600 hover:text-rose-600 hover:bg-rose-50 transition-all">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    Edit Voucher: {{ $voucher->code }}
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui detail diskon, kuota, atau masa berlaku voucher</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100">
                <form method="POST" action="{{ route('admin.vouchers.update', $voucher->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Kode Voucher --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Kode Voucher *</label>
                            <input type="text" name="code" value="{{ old('code', $voucher->code) }}" required 
                                   class="w-full px-4 py-3 text-sm font-mono font-bold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] uppercase">
                            @error('code') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nama Voucher --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Nama Voucher *</label>
                            <input type="text" name="name" value="{{ old('name', $voucher->name) }}" required 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tipe Voucher --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Tipe Diskon *</label>
                            <select name="type" required class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                                <option value="percentage" {{ old('type', $voucher->type) === 'percentage' ? 'selected' : '' }}>Percentage (%) — Diskon Persentase</option>
                                <option value="fixed" {{ old('type', $voucher->type) === 'fixed' ? 'selected' : '' }}>Fixed (Rp) — Potongan Nominal Langsung</option>
                            </select>
                            @error('type') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Nilai Diskon --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Nilai Diskon (% atau Rp) *</label>
                            <input type="number" name="value" value="{{ old('value', $voucher->value) }}" required min="0" step="any" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('value') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Minimal Belanja (Rp) --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Minimal Transaksi (Rp)</label>
                            <input type="number" name="min_purchase" value="{{ old('min_purchase', $voucher->min_purchase) }}" min="0" step="1000" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('min_purchase') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Maksimal Diskon (Rp) --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Maksimal Diskon (Rp)</label>
                            <input type="number" name="max_discount" value="{{ old('max_discount', $voucher->max_discount) }}" min="0" step="1000" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('max_discount') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Total Kuota --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Total Kuota Penggunaan *</label>
                            <input type="number" name="quota" value="{{ old('quota', $voucher->quota) }}" required min="1" step="1" 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            <span class="text-[10px] text-gray-400">Sudah terpakai: {{ $voucher->used_count }} kuota</span>
                            @error('quota') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Berlaku --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Tanggal Mulai Berlaku *</label>
                            <input type="date" name="valid_from" value="{{ old('valid_from', \Carbon\Carbon::parse($voucher->valid_from)->format('Y-m-d')) }}" required 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('valid_from') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Tanggal Kadaluarsa *</label>
                            <input type="date" name="valid_until" value="{{ old('valid_until', \Carbon\Carbon::parse($voucher->valid_until)->format('Y-m-d')) }}" required 
                                   class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('valid_until') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Deskripsi Syarat & Ketentuan --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Deskripsi / Syarat & Ketentuan</label>
                            <textarea name="description" rows="3" 
                                      class="w-full px-4 py-3 text-sm rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">{{ old('description', $voucher->description) }}</textarea>
                            @error('description') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Status Aktif --}}
                        <div class="md:col-span-2 flex items-center gap-3 pt-2">
                            <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded-md border-gray-300 text-[#f45472] focus:ring-[#f45472]">
                            <label for="is_active" class="text-sm font-bold text-gray-800">Aktifkan voucher ini agar dapat digunakan pelanggan</label>
                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('admin.vouchers.index') }}" class="px-6 py-3 rounded-full bg-gray-100 text-gray-700 font-bold text-xs hover:bg-gray-200 transition-all">
                            Batal
                        </a>
                        <button type="submit" class="px-8 py-3 rounded-full bg-[#f45472] text-white font-bold text-xs hover:bg-[#d93856] shadow-md hover:shadow-lg transition-all">
                            Perbarui Voucher
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</x-admin-layout>
