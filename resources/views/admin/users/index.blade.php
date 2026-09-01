<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Kelola Akun User
                </h2>
                <p class="text-sm text-gray-500 mt-1">Daftar pengguna aplikasi, termasuk status aktif, dan pengelolaan hapus akun.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 space-y-4">
                <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                    
                    {{-- Search Input --}}
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                            Cari User (Nama / Email / Telp)
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Ketik untuk mencari user..." 
                               class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 placeholder-gray-400 bg-gray-50/50">
                    </div>

                    {{-- Submit Button --}}
                    <div class="sm:col-span-1">
                        <button type="submit" class="w-full px-4 py-2.5 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-all flex justify-center items-center gap-2">
                            Terapkan Pencarian
                        </button>
                    </div>
                </form>
            </div>

            {{-- TABLE SECTION --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Tgl Daftar</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="hover:bg-rose-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&color=f45472&background=ffe4e8' }}" 
                                                 alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover shadow-sm border border-rose-100">
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                <p class="text-xs text-gray-400">{{ $user->phone ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-6 py-4 text-xs text-gray-500 font-medium">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    @if(auth()->id() === $user->id) disabled @endif
                                                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-[#f45472] focus:ring-offset-2 {{ auth()->id() === $user->id ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    role="switch" aria-checked="{{ $user->is_active ? 'true' : 'false' }}">
                                                <span class="sr-only">Toggle active status</span>
                                                <span aria-hidden="true" class="pointer-events-none absolute mx-auto h-4 w-8 rounded-full transition-colors duration-200 ease-in-out {{ $user->is_active ? 'bg-[#f45472]' : 'bg-gray-200' }}"></span>
                                                <span aria-hidden="true" class="pointer-events-none absolute left-0 inline-block h-5 w-5 transform rounded-full border border-gray-200 bg-white shadow ring-0 transition-transform duration-200 ease-in-out {{ $user->is_active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                            </button>
                                        </form>
                                    </td>

                                    <td class="px-6 py-4 text-right space-x-2">
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini secara permanen? Data yang berkaitan dengan user ini mungkin akan hilang atau bermasalah jika ada foreign key yang tidak cascade.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus User">
                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Akun Anda</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                            <i class="fa-solid fa-user-xmark text-2xl text-gray-400"></i>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">Tidak Ada Data</h3>
                                        <p class="text-xs text-gray-500 mt-1">Belum ada user yang terdaftar atau ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
