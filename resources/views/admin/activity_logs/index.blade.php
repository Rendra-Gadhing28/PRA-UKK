<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-4 h-8 bg-gradient-to-b from-[#b01f44] to-[#f45472] rounded-full inline-block"></span>
                    Audit Log Aktivitas
                </h2>
                <p class="text-sm text-gray-500 mt-1">Rekam jejak audit keamanan read-only, pengarsipan otomatis >30 hari, dan detail payload perubahan data.</p>
            </div>

            <div class="flex items-center gap-2.5 flex-wrap">
                {{-- Tombol Arsipkan Log >30 Hari --}}
                <form action="{{ route('admin.activity-logs.archive-expired') }}" method="POST"
                      onsubmit="return confirm('Arsipkan seluruh activity log aktif yang berumur lebih dari 30 hari?');">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2.5 bg-rose-50 text-[#b01f44] border border-rose-200 text-xs font-bold rounded-xl hover:bg-rose-100 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-box-archive text-[#b01f44]"></i>
                        Arsipkan Semua >30H ({{ $expiredCount }})
                    </button>
                </form>

                {{-- Tombol Simulasi Skip 30 Hari --}}
                <form action="{{ route('admin.activity-logs.simulate-skip-30d') }}" method="POST"
                      onsubmit="return confirm('Simulasikan majukan umur log 30 hari ke belakang? Sistem akan otomatis mengarsipkan log yang telah expired.');">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2.5 bg-amber-500 text-white text-xs font-bold rounded-xl hover:bg-amber-600 transition-all flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-flask-vial"></i>
                        Testing: Skip 30 Hari
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        selectedLogs: [],
        selectAll: false,
        activeModalLog: null,
        toggleAll() {
            if (this.selectAll) {
                this.selectedLogs = Array.from(document.querySelectorAll('.log-checkbox')).map(el => el.value);
            } else {
                this.selectedLogs = [];
            }
        },
        updateSelectAll() {
            const allCheckboxes = document.querySelectorAll('.log-checkbox');
            this.selectAll = allCheckboxes.length > 0 && this.selectedLogs.length === allCheckboxes.length;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- TABS & STATUS BAR --}}
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white rounded-3xl p-4 shadow-sm border border-rose-100">
                <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
                    <a href="{{ route('admin.activity-logs.index', array_merge(request()->except('tab', 'page'), ['tab' => 'active'])) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $tab === 'active' ? 'bg-[#b01f44] text-white shadow-md shadow-rose-900/10' : 'bg-gray-100 text-gray-700 hover:bg-rose-50 hover:text-[#b01f44]' }}">
                        <i class="fa-solid fa-bolt text-xs"></i>
                        <span>Log Aktif</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full {{ $tab === 'active' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $activeCount }}</span>
                    </a>

                    <a href="{{ route('admin.activity-logs.index', array_merge(request()->except('tab', 'page'), ['tab' => 'archived'])) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $tab === 'archived' ? 'bg-[#b01f44] text-white shadow-md shadow-rose-900/10' : 'bg-gray-100 text-gray-700 hover:bg-rose-50 hover:text-[#b01f44]' }}">
                        <i class="fa-solid fa-box-archive text-xs"></i>
                        <span>Terarsip (>30H)</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full {{ $tab === 'archived' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $archivedCount }}</span>
                    </a>

                    <a href="{{ route('admin.activity-logs.index', array_merge(request()->except('tab', 'page'), ['tab' => 'all'])) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $tab === 'all' ? 'bg-[#b01f44] text-white shadow-md shadow-rose-900/10' : 'bg-gray-100 text-gray-700 hover:bg-rose-50 hover:text-[#b01f44]' }}">
                        <i class="fa-solid fa-list-ul text-xs"></i>
                        <span>Semua Log</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full {{ $tab === 'all' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">{{ $activeCount + $archivedCount }}</span>
                    </a>
                </div>

                {{-- Bulk Action Bar --}}
                <div x-show="selectedLogs.length > 0" x-cloak class="w-full sm:w-auto flex items-center justify-between sm:justify-end gap-3 bg-rose-50 px-4 py-2 rounded-2xl border border-rose-200">
                    <span class="text-xs font-bold text-[#b01f44]">
                        <span x-text="selectedLogs.length"></span> log terpilih
                    </span>

                    <form action="{{ route('admin.activity-logs.bulk-archive') }}" method="POST">
                        @csrf
                        <template x-for="id in selectedLogs" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>
                        <button type="submit" 
                                onclick="return confirm('Arsipkan seluruh log terpilih?')"
                                class="px-3 py-1.5 bg-[#b01f44] text-white text-xs font-bold rounded-xl hover:bg-[#8e1735] transition-all flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-box-archive text-xs"></i>
                            Arsipkan Terpilih
                        </button>
                    </form>
                </div>
            </div>

            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-6 gap-3 items-end">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    {{-- Search Input --}}
                    <div class="sm:col-span-2 md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                            Pencarian Log
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari deskripsi, user, IP..." 
                               class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] text-gray-800 placeholder-gray-400 bg-gray-50/50">
                    </div>

                    {{-- Filter Modul --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-cubes text-rose-500 text-xs"></i>
                            Modul
                        </label>
                        <select name="subject_type" class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] bg-gray-50/50">
                            <option value="">Semua Modul</option>
                            @foreach($modules as $mod)
                                <option value="{{ $mod }}" {{ request('subject_type') === $mod ? 'selected' : '' }}>{{ $mod }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Aksi --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-tag text-rose-500 text-xs"></i>
                            Aksi
                        </label>
                        <select name="action" class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] bg-gray-50/50">
                            <option value="">Semua Aksi</option>
                            @foreach($actions as $act)
                                <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ strtoupper($act) }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rentang Tanggal --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar text-rose-500 text-xs"></i>
                            Dari Tanggal
                        </label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                               class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] bg-gray-50/50">
                    </div>

                    {{-- Submit & Reset Buttons --}}
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-all flex justify-center items-center gap-1.5">
                            <i class="fa-solid fa-filter text-xs"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.activity-logs.index', ['tab' => $tab]) }}" class="px-3 py-2.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-xl hover:bg-gray-200 transition-all flex items-center justify-center" title="Reset Filter">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABLE SECTION (READ-ONLY AUDIT TRAIL) --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap text-sm">
                        <thead class="bg-gray-50/70 border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-4 w-10 text-center">
                                    <input type="checkbox" x-model="selectAll" @change="toggleAll" class="rounded border-gray-300 text-[#b01f44] focus:ring-[#b01f44]">
                                </th>
                                <th class="px-4 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Aksi & Modul</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Deskripsi Aktivitas</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Pelaku (User)</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Waktu & IP</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold text-xs text-gray-500 uppercase tracking-wider text-right">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($logs as $log)
                                @php
                                    $actionColor = match(strtolower($log->action)) {
                                        'create', 'register', 'restore' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'update', 'reschedule', 'verify_payment' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'delete', 'cancel' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'force_delete' => 'bg-rose-50 text-[#b01f44] border-rose-200',
                                        'login', 'logout' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        default => 'bg-gray-50 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <tr class="hover:bg-rose-50/30 transition-colors {{ $log->is_archived ? 'opacity-70 bg-gray-50/40' : '' }}">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" value="{{ $log->id }}" x-model="selectedLogs" @change="updateSelectAll" class="log-checkbox rounded border-gray-300 text-[#b01f44] focus:ring-[#b01f44]">
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex flex-col gap-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider border {{ $actionColor }} w-fit">
                                                {{ $log->action }}
                                            </span>
                                            @if($log->subject_type)
                                                <span class="text-[11px] font-bold text-gray-600 flex items-center gap-1">
                                                    <i class="fa-solid fa-cube text-[9px] text-gray-400"></i>
                                                    {{ $log->subject_type }}
                                                    @if($log->subject_id)
                                                        <span class="text-gray-400">#{{ $log->subject_id }}</span>
                                                    @endif
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 max-w-xs sm:max-w-md truncate">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-900 text-xs truncate" title="{{ $log->description }}">
                                                {{ $log->description }}
                                            </span>
                                        </div>
                                        @if(!empty($log->properties))
                                            <button type="button" 
                                                    @click="activeModalLog = {{ json_encode($log) }}"
                                                    class="mt-1 text-[11px] font-bold text-[#b01f44] hover:underline flex items-center gap-1">
                                                <i class="fa-solid fa-code text-[10px]"></i>
                                                Lihat Perubahan Payload
                                            </button>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($log->user)
                                            <div class="flex items-center gap-2.5">
                                                <img src="{{ $log->user->avatar ? Storage::url($log->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($log->user->name).'&color=b01f44&background=ffe4e8' }}" 
                                                     alt="{{ $log->user->name }}" class="w-7 h-7 rounded-full object-cover border border-rose-100">
                                                <div>
                                                    <p class="font-bold text-xs text-gray-900">{{ $log->user->name }}</p>
                                                    <p class="text-[10px] text-gray-500">{{ $log->user->email }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs font-semibold text-gray-400 italic">Sistem / Tamu</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-xs font-bold text-gray-900">{{ $log->created_at->format('d/m/Y H:i:s') }}</p>
                                        <div class="flex items-center gap-1.5 text-[10px] text-gray-500">
                                            <i class="fa-solid fa-network-wired text-[9px] text-gray-400"></i>
                                            <span>{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($log->is_archived)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                <i class="fa-solid fa-box-archive text-[9px]"></i>
                                                Terarsip
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Aktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if(!empty($log->properties))
                                                <button type="button" 
                                                        @click="activeModalLog = {{ json_encode($log) }}"
                                                        class="p-2 text-gray-500 hover:text-[#b01f44] hover:bg-rose-50 rounded-xl transition-all"
                                                        title="Detail Payload">
                                                    <i class="fa-solid fa-eye text-xs"></i>
                                                </button>
                                            @endif

                                            @if($log->is_archived)
                                                <form action="{{ route('admin.activity-logs.unarchive', $log) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-2 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-xl transition-all"
                                                            title="Kembalikan ke Aktif">
                                                        <i class="fa-solid fa-arrow-rotate-left text-xs"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.activity-logs.archive', $log) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="p-2 text-gray-400 hover:text-[#b01f44] hover:bg-rose-50 rounded-xl transition-all"
                                                            title="Arsipkan Log">
                                                        <i class="fa-solid fa-box-archive text-xs"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 rounded-full bg-rose-50 flex items-center justify-center text-[#b01f44] mb-3">
                                                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
                                            </div>
                                            <p class="font-bold text-gray-800 text-sm">Tidak ada log aktivitas ditemukan</p>
                                            <p class="text-xs text-gray-500 mt-1">Coba sesuaikan filter pencarian atau tanggal Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($logs->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $logs->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- MODAL PREVIEW DETAIL PAYLOAD (DIFF OLD VS NEW) --}}
        <div x-show="activeModalLog" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95">
            
            <div @click.away="activeModalLog = null" class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-rose-100 overflow-hidden flex flex-col max-h-[85vh]">
                
                {{-- Modal Header --}}
                <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-rose-50/50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-rose-100 text-[#b01f44] flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-file-code text-base"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-gray-900">Detail Payload & Metadata Log</h3>
                            <p class="text-xs text-gray-500">Rekam snapshot perubahan data saat aksi dieksekusi.</p>
                        </div>
                    </div>
                    <button @click="activeModalLog = null" class="text-gray-400 hover:text-gray-700 p-2 rounded-xl hover:bg-gray-100">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6 overflow-y-auto space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-2xl text-xs">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Aksi</span>
                            <span class="font-bold text-[#b01f44]" x-text="activeModalLog ? activeModalLog.action.toUpperCase() : ''"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Modul / Subject</span>
                            <span class="font-bold text-gray-800" x-text="activeModalLog ? (activeModalLog.subject_type || '-') + (activeModalLog.subject_id ? ' #' + activeModalLog.subject_id : '') : ''"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">IP Address</span>
                            <span class="font-bold text-gray-800" x-text="activeModalLog ? (activeModalLog.ip_address || '127.0.0.1') : ''"></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold">Waktu</span>
                            <span class="font-bold text-gray-800" x-text="activeModalLog ? new Date(activeModalLog.created_at).toLocaleString('id-ID') : ''"></span>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label class="text-xs font-bold text-gray-600 block mb-1">Deskripsi:</label>
                        <p class="text-xs text-gray-800 bg-rose-50/50 p-3 rounded-xl border border-rose-100" x-text="activeModalLog ? activeModalLog.description : ''"></p>
                    </div>

                    {{-- Payload Properties Diff / JSON --}}
                    <div>
                        <label class="text-xs font-bold text-gray-600 block mb-1">Properties (JSON Payload):</label>
                        <pre class="bg-gray-900 text-emerald-400 p-4 rounded-2xl text-xs font-mono overflow-x-auto max-h-60" x-text="activeModalLog ? JSON.stringify(activeModalLog.properties, null, 2) : '{}'"></pre>
                    </div>

                    {{-- User Agent --}}
                    <div x-show="activeModalLog && activeModalLog.user_agent">
                        <label class="text-xs font-bold text-gray-600 block mb-1">User Agent:</label>
                        <p class="text-[11px] text-gray-500 bg-gray-50 p-2.5 rounded-xl break-all font-mono" x-text="activeModalLog ? activeModalLog.user_agent : ''"></p>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-end">
                    <button @click="activeModalLog = null" class="px-5 py-2 bg-gray-900 text-white text-xs font-bold rounded-xl hover:bg-gray-800 transition-all">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>
