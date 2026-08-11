<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2 font-headline">
                    <span class="w-3 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Manajemen Beautician & Staf Salon
                </h2>
                <p class="text-sm text-gray-500 mt-1">Kelola profil beautician, jadwal penugasan, foto staf, & riwayat reservasi</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.beauticians.create') }}" 
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-sm font-semibold shadow-md transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Beautician Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.beauticians.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-6 gap-4 items-end">
                    
                    {{-- Filter Status --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Status Penugasan</label>
                        <select name="status" class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            <option value="all">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    {{-- Search Input --}}
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Cari Beautician</label>
                        <input type="text" name="search" placeholder="Nama, telepon, email, atau keahlian..." value="{{ request('search') }}" 
                               class="w-full px-3 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="submit" class="w-full py-2 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm">
                            Filter
                        </button>
                        <a href="{{ route('admin.beauticians.index') }}" class="py-2 px-3 rounded-xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-gray-200 transition-all">
                            Reset
                        </a>
                    </div>

                </form>
            </div>

            {{-- BEAUTICIANS GRID SECTION --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($beauticians as $b)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col justify-between hover:shadow-md transition-all">
                    <div>
                        {{-- Photo & Status Badge Header --}}
                        <div class="flex items-start justify-between mb-4">
                            <div class="relative">
                                <img src="{{ $b->photo_url }}" alt="{{ $b->name }}" 
                                     class="w-16 h-16 rounded-full object-cover border-2 border-rose-200 shadow-sm">
                                <span class="absolute bottom-0 right-0 w-4 h-4 rounded-full border-2 border-white {{ $b->is_active ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                            </div>

                            <form method="POST" action="{{ route('admin.beauticians.toggle-active', $b->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-2.5 py-1 rounded-full text-[10px] font-bold transition-all {{ $b->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    {{ $b->is_active ? '● Aktif' : '○ Off' }}
                                </button>
                            </form>
                        </div>

                        {{-- Staf Info --}}
                        <h3 class="font-headline font-bold text-gray-900 text-lg mb-0.5 truncate">{{ $b->name }}</h3>
                        <p class="text-xs text-[#f45472] font-semibold mb-3">Beautician Specialist</p>

                        <div class="space-y-1.5 text-xs text-gray-600 mb-4">
                            <div class="flex items-center gap-2 truncate">
                                <span>📱</span> <span>{{ $b->phone ?: 'Tidak ada no HP' }}</span>
                            </div>
                            <div class="flex items-center gap-2 truncate">
                                <span>✉️</span> <span>{{ $b->email ?: 'Tidak ada email' }}</span>
                            </div>
                            <div class="pt-2 text-xs text-gray-500 line-clamp-2">
                                "{{ $b->bio }}"
                            </div>
                        </div>
                    </div>

                    {{-- Footer Stats & Action Buttons --}}
                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs mb-4">
                            <span class="text-gray-400">Total Booking:</span>
                            <span class="font-black text-rose-600 bg-rose-50 px-2.5 py-0.5 rounded-full">
                                {{ number_format($b->bookings_count ?? $b->total_bookings) }} Layanan
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-1.5 text-center">
                            <a href="{{ route('admin.beauticians.show', $b->id) }}" 
                               class="py-1.5 rounded-xl bg-purple-50 text-purple-700 text-xs font-bold hover:bg-purple-100 transition-colors">
                                Detail
                            </a>
                            <a href="{{ route('admin.beauticians.edit', $b->id) }}" 
                               class="py-1.5 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.beauticians.destroy', $b->id) }}" 
                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus staf {{ $b->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full py-1.5 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full bg-white rounded-3xl p-12 text-center text-gray-400 border border-rose-100">
                    <p class="text-base font-semibold">Belum ada staf beautician yang terdaftar.</p>
                </div>
                @endforelse
            </div>

            {{-- Custom Numbered Pagination Links --}}
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-rose-100">
                {{ $beauticians->links() }}
            </div>

        </div>
    </div>
</x-admin-layout>
