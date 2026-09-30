<x-admin-layout>
    <x-slot name="header">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-1.5 h-7 bg-gradient-to-b from-[#b01f44] to-[#f45472] rounded-full inline-block shadow-[0_2px_10px_rgba(244,84,114,0.45)]"></span>
                    Pusat Notifikasi & Reservasi Masuk
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau notifikasi reservasi real-time, konfirmasi langsung, & pengarsipan otomatis.</p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                {{-- Tandai Semua Dibaca --}}
                @if(($unreadCount ?? 0) > 0)
                <form action="{{ route('admin.notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-3.5 py-2 bg-white border border-rose-200 text-rose-700 hover:bg-rose-50 text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-envelope-open-text text-rose-500 text-xs"></i>
                        <span>Tandai Dibaca ({{ $unreadCount }})</span>
                    </button>
                </form>
                @endif

                {{-- Arsipkan Semua Notifikasi Yang Sudah Dibaca --}}
                <form action="{{ route('admin.notifications.archive-all-read') }}" method="POST"
                      onsubmit="return confirm('Arsipkan seluruh notifikasi aktif yang sudah dibaca ({{ $readActiveCount ?? 0 }} notifikasi)?');">
                    @csrf
                    <button type="submit" 
                            class="px-3.5 py-2 bg-rose-50 border border-rose-200 text-[#b01f44] hover:bg-rose-100 text-xs font-bold rounded-xl shadow-xs transition-all flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-box-archive text-[#b01f44] text-xs"></i>
                        <span>Arsipkan Dibaca ({{ $readActiveCount ?? 0 }})</span>
                    </button>
                </form>

                {{-- Tombol Hapus Notifikasi Secara Menyeluruh --}}
                <form action="{{ route('admin.notifications.clear-all') }}" method="POST"
                      onsubmit="return confirm('Peringatan: Anda akan memindahkan SELURUH notifikasi ({{ $tab === 'archived' ? 'arsip' : ($tab === 'active' ? 'aktif' : 'semua') }}) ke tong sampah secara menyeluruh. Lanjutkan?');">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <button type="submit" 
                            class="px-3.5 py-2 bg-[#b01f44] hover:bg-[#8e1735] text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-2 active:scale-95">
                        <i class="fa-solid fa-trash-can text-white text-xs"></i>
                        <span>Hapus Semua</span>
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        selectedIds: [],
        selectAll: false,
        cancelModalOpen: false,
        activeCancelNotifId: null,
        activeCancelBookingCode: '',
        cancelReason: '',
        openCancelModal(notifId, bookingCode) {
            this.activeCancelNotifId = notifId;
            this.activeCancelBookingCode = bookingCode;
            this.cancelReason = '';
            this.cancelModalOpen = true;
        },
        toggleAll() {
            if (this.selectAll) {
                this.selectedIds = Array.from(document.querySelectorAll('.notif-checkbox')).map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        },
        updateSelectAll() {
            const allCheckboxes = document.querySelectorAll('.notif-checkbox');
            this.selectAll = allCheckboxes.length > 0 && this.selectedIds.length === allCheckboxes.length;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- TAB NAVIGATION & BULK ACTIONS TOOLBAR --}}
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-rose-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
                <div class="flex items-center gap-2 overflow-x-auto pb-1 md:pb-0">
                    <a href="{{ route('admin.notifications.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'active'])) }}"
                       class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $tab === 'active' ? 'bg-[#b01f44] text-white shadow-md shadow-rose-900/10' : 'bg-gray-50 text-gray-700 hover:bg-rose-50 hover:text-[#b01f44]' }}">
                        <i class="fa-solid fa-bell text-xs"></i>
                        <span>Notifikasi Aktif</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full {{ $tab === 'active' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">
                            {{ $activeCount ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('admin.notifications.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'archived'])) }}"
                       class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $tab === 'archived' ? 'bg-[#b01f44] text-white shadow-md shadow-rose-900/10' : 'bg-gray-50 text-gray-700 hover:bg-rose-50 hover:text-[#b01f44]' }}">
                        <i class="fa-solid fa-box-archive text-xs"></i>
                        <span>Arsip Notifikasi</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full {{ $tab === 'archived' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">
                            {{ $archivedCount ?? 0 }}
                        </span>
                    </a>

                    <a href="{{ route('admin.notifications.index', array_merge(request()->except(['tab', 'page']), ['tab' => 'all'])) }}"
                       class="px-4 py-2.5 rounded-2xl text-xs font-bold transition-all flex items-center gap-2 shrink-0 {{ $tab === 'all' ? 'bg-[#b01f44] text-white shadow-md shadow-rose-900/10' : 'bg-gray-50 text-gray-700 hover:bg-rose-50 hover:text-[#b01f44]' }}">
                        <i class="fa-solid fa-list-ul text-xs"></i>
                        <span>Semua</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full {{ $tab === 'all' ? 'bg-white/25 text-white' : 'bg-gray-200 text-gray-700' }}">
                            {{ ($activeCount ?? 0) + ($archivedCount ?? 0) }}
                        </span>
                    </a>
                </div>

                {{-- Bulk Actions Bar & Trash --}}
                <div class="flex items-center gap-2 justify-between md:justify-end shrink-0">
                    <div x-show="selectedIds.length > 0" x-cloak class="flex items-center gap-2 bg-rose-50 px-3 py-1.5 rounded-2xl border border-rose-200">
                        <span class="text-xs font-bold text-[#b01f44] whitespace-nowrap">
                            <span x-text="selectedIds.length"></span> dipilih
                        </span>

                        {{-- Bulk Archive --}}
                        <form action="{{ route('admin.notifications.bulk-archive') }}" method="POST">
                            @csrf
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                    onclick="return confirm('Arsipkan notifikasi terpilih?')"
                                    class="px-2.5 py-1 bg-white hover:bg-rose-100 text-[#b01f44] border border-rose-200 text-[11px] font-bold rounded-xl transition-all">
                                Arsipkan
                            </button>
                        </form>

                        {{-- Bulk Delete --}}
                        <form action="{{ route('admin.notifications.bulk-delete') }}" method="POST">
                            @csrf
                            <template x-for="id in selectedIds" :key="id">
                                <input type="hidden" name="ids[]" :value="id">
                            </template>
                            <button type="submit" 
                                    onclick="return confirm('Pindahkan notifikasi terpilih ke tong sampah?')"
                                    class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold rounded-xl transition-all">
                                Hapus
                            </button>
                        </form>
                    </div>

                    <a href="{{ route('admin.trash.index', ['type' => 'notifications']) }}"
                       class="px-4 py-2.5 rounded-2xl text-xs font-bold text-gray-600 bg-gray-50 border border-gray-200 hover:bg-rose-50 hover:text-[#b01f44] hover:border-rose-200 transition-all flex items-center gap-2 shrink-0 ml-auto md:ml-0">
                        <i class="fa-solid fa-trash-can text-xs text-rose-500"></i>
                        <span>Tong Sampah</span>
                    </a>
                </div>
            </div>

            {{-- FILTER BAR SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.notifications.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
                    <input type="hidden" name="tab" value="{{ $tab }}">

                    {{-- Search Input --}}
                    <div class="sm:col-span-2 lg:col-span-5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                            Pencarian Notifikasi
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari kode booking, nama customer, judul..." 
                               class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] text-gray-800 placeholder-gray-400 bg-gray-50/50">
                    </div>

                    {{-- Start Date --}}
                    <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-day text-rose-500 text-xs"></i>
                            Dari Tanggal
                        </label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] text-gray-800 bg-gray-50/50">
                    </div>

                    {{-- End Date --}}
                    <div class="col-span-1 sm:col-span-1 lg:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-calendar-days text-rose-500 text-xs"></i>
                            Sampai Tanggal
                        </label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] text-gray-800 bg-gray-50/50">
                    </div>

                    {{-- Actions (Filter & Reset) --}}
                    <div class="col-span-1 sm:col-span-2 lg:col-span-3 flex items-center gap-2">
                        <button type="submit" 
                                class="flex-1 py-2.5 px-4 bg-[#b01f44] text-white text-xs font-bold rounded-xl hover:bg-[#8e1735] transition-all shadow-sm flex items-center justify-center gap-2 active:scale-95">
                            <i class="fa-solid fa-filter text-xs"></i>
                            <span>Filter</span>
                        </button>
                        @if(request()->hasAny(['search', 'start_date', 'end_date']))
                        <a href="{{ route('admin.notifications.index', ['tab' => $tab]) }}" 
                           class="py-2.5 px-4 bg-gray-100 text-gray-600 hover:bg-gray-200 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                            <span>Reset</span>
                        </a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- MASTER CHECKBOX BAR --}}
            @if($notifications->isNotEmpty())
            <div class="flex items-center justify-between px-4 py-2.5 bg-white rounded-2xl border border-rose-100 text-xs text-gray-600 shadow-2xs">
                <label class="flex items-center gap-2.5 font-bold cursor-pointer select-none">
                    <input type="checkbox" x-model="selectAll" @change="toggleAll()" 
                           class="rounded border-gray-300 text-[#b01f44] focus:ring-[#b01f44]">
                    <span>Pilih Semua di Halaman Ini</span>
                </label>
                <span class="text-gray-400 tabular-nums font-medium">Menampilkan {{ $notifications->count() }} notifikasi</span>
            </div>
            @endif

            {{-- NOTIFICATIONS CARDS GRID (DASHBOARD STYLE) --}}
            @if($notifications->isEmpty())
                <div class="bg-white rounded-3xl p-12 text-center border border-dashed border-rose-200">
                    <div class="w-16 h-16 mx-auto rounded-full bg-rose-50 flex items-center justify-center text-[#b01f44] mb-4">
                        <i class="fa-solid fa-bell-slash text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 font-headline">Tidak Ada Notifikasi</h3>
                    <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                        {{ $tab === 'archived' ? 'Belum ada notifikasi di arsip.' : ($tab === 'active' ? 'Semua notifikasi telah ditindaklanjuti atau diarsipkan.' : 'Belum ada data notifikasi.') }}
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($notifications as $notif)
                    @php
                        $bk = $notif->booking;
                        $bkStatus = $bk ? (is_object($bk->status) ? $bk->status->value : (string)$bk->status) : ($notif->data['status'] ?? 'pending');
                        $isPending = in_array($bkStatus, ['pending']);
                        $custName = $notif->data['customer_name'] ?? ($bk?->user?->name ?? 'Pelanggan');
                        $custPhone = $notif->data['customer_phone'] ?? ($bk?->user?->phone ?? '-');
                        $bkCode = $notif->data['booking_code'] ?? ($bk?->booking_code ?? 'BK-'.$notif->id);
                        $treatments = $notif->data['treatments'] ?? ($bk?->treatments->pluck('name')->join(', ') ?? '-');
                        $totalAmt = $notif->data['total_amount'] ?? ($bk?->total_amount ?? 0);
                        $bkDate = $notif->data['booking_date'] ?? ($bk?->booking_date ? $bk->booking_date->format('d M Y') : '-');
                        $bkTime = $notif->data['time_start'] ?? ($bk?->time_start ?? '-');
                        $bkType = $notif->data['booking_type'] ?? ($bk?->booking_type ?? 'salon');
                        $isArchived = $notif->is_archived;
                        $isUnread = empty($notif->read_at);
                    @endphp
                    <div class="bg-gradient-to-br from-white to-rose-50/30 p-5 rounded-2xl border {{ $isUnread ? 'border-rose-300 shadow-md shadow-rose-900/5' : 'border-rose-100' }} shadow-xs hover:shadow-md transition-all flex flex-col justify-between relative overflow-hidden group">
                        <div class="space-y-3">
                            {{-- Top Header Row: Checkbox, Code, Badges, Utility Actions --}}
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2 min-w-0 flex-wrap">
                                    <input type="checkbox" value="{{ $notif->id }}" x-model="selectedIds" @change="updateSelectAll()"
                                           class="notif-checkbox rounded border-gray-300 text-[#b01f44] focus:ring-[#b01f44] shrink-0">

                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-[#b01f44] uppercase tracking-wider font-mono shrink-0">
                                        #{{ $bkCode }}
                                    </span>

                                    @if($isArchived)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-gray-100 text-gray-700 border border-gray-200 shrink-0">
                                        <i class="fa-solid fa-box-archive text-[8px]"></i>
                                        Arsip
                                    </span>
                                    @endif

                                    @if($isUnread)
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-rose-100 text-rose-700 border border-rose-200 shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                        Baru
                                    </span>
                                    @endif
                                </div>

                                {{-- Quick Utility Buttons (Top Right) --}}
                                <div class="flex items-center gap-1 shrink-0">
                                    @if(!$isArchived)
                                    <form action="{{ route('admin.notifications.archive', $notif) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Arsipkan Notifikasi"
                                                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-[#b01f44] hover:bg-rose-100/60 rounded-lg transition-all">
                                            <i class="fa-solid fa-box-archive text-[11px]"></i>
                                        </button>
                                    </form>
                                    @else
                                    <form action="{{ route('admin.notifications.unarchive', $notif) }}" method="POST">
                                        @csrf
                                        <button type="submit" title="Kembalikan ke Aktif"
                                                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-all">
                                            <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                        </button>
                                    </form>
                                    @endif

                                    <form action="{{ route('admin.notifications.destroy', $notif) }}" method="POST"
                                          onsubmit="return confirm('Pindahkan notifikasi ini ke tong sampah?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Hapus Notifikasi"
                                                class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-rose-600 hover:bg-rose-100/60 rounded-lg transition-all">
                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                        </button>
                                    </form>

                                    @if($bk)
                                    <a href="{{ route('admin.bookings.show', $bk) }}" title="Lihat Detail Reservasi"
                                       class="w-7 h-7 flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all">
                                        <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Customer Title & Time Row --}}
                            <div class="flex items-center justify-between gap-2 pt-0.5">
                                <h4 class="text-sm font-bold text-gray-900 truncate">{{ $custName }}</h4>
                                <span class="text-[11px] text-gray-400 shrink-0 tabular-nums">
                                    {{ $notif->created_at->diffForHumans(null, true, true) }}
                                </span>
                            </div>

                            {{-- Detail Meta Container --}}
                            <div class="text-xs text-gray-600 space-y-1 bg-white/70 p-3 rounded-xl border border-rose-100/60">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-gray-400 shrink-0">Treatment:</span>
                                    <span class="font-medium text-gray-800 truncate text-right max-w-[170px]">{{ $treatments ?: 'Layanan Salon' }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-gray-400 shrink-0">Jadwal:</span>
                                    <span class="font-medium text-gray-800 text-right">{{ $bkDate }} • {{ substr($bkTime, 0, 5) }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-gray-400 shrink-0">Total:</span>
                                    <span class="font-bold text-rose-600 tabular-nums text-right">Rp {{ number_format((float)$totalAmt, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-gray-400 shrink-0">Layanan:</span>
                                    <span class="inline-flex items-center gap-1 font-semibold text-gray-700 text-right">
                                        <i class="fa-solid {{ $bkType === 'home_service' ? 'fa-house-user text-purple-500' : 'fa-store text-rose-500' }} text-[10px]"></i>
                                        {{ $bkType === 'home_service' ? 'Home Service' : 'Salon Visit' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons Bottom Rail --}}
                        <div class="mt-4 pt-3 border-t border-rose-100/80 flex items-center justify-between gap-2">
                            @if(!$isArchived)
                                @if($bkStatus === 'confirmed')
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-xs"></i>
                                            <span>Confirmed</span>
                                        </span>
                                    </div>

                                    {{-- Cancelled Button --}}
                                    <button type="button" 
                                            @click="openCancelModal('{{ $notif->id }}', '{{ $bkCode }}')"
                                            class="py-1.5 px-3 bg-rose-50 hover:bg-rose-100 text-[#b01f44] border border-rose-200 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-2xs">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                        <span>Cancelled</span>
                                    </button>
                                @elseif($bkStatus === 'pending')
                                    {{-- Confirmed Button --}}
                                    <form action="{{ route('admin.notifications.confirm', $notif) }}" method="POST" class="flex-1"
                                          onsubmit="return confirm('Konfirmasi reservasi #{{ $bkCode }}? Status booking akan otomatis menjadi Confirmed.');">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5 shadow-sm hover:shadow">
                                            <i class="fa-solid fa-check text-xs"></i>
                                            <span>Confirmed</span>
                                        </button>
                                    </form>

                                    {{-- Cancelled Button --}}
                                    <button type="button" 
                                            @click="openCancelModal('{{ $notif->id }}', '{{ $bkCode }}')"
                                            class="flex-1 py-2 px-3 bg-rose-50 hover:bg-rose-100 text-[#b01f44] border border-rose-200 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                        <span>Cancelled</span>
                                    </button>
                                @else
                                    {{-- Status Selesai / Dibatalkan --}}
                                    <div class="w-full flex items-center justify-between text-xs">
                                        <span class="text-gray-400">Status:</span>
                                        @php
                                            $stBadge = match($bkStatus) {
                                                'completed' => ['bg' => 'bg-blue-100 text-blue-900 border-blue-200', 'icon' => 'fa-check-double', 'label' => 'Completed'],
                                                'canceled', 'cancelled' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fa-circle-xmark', 'label' => 'Cancelled'],
                                                'in_progress' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fa-rotate fa-spin-pulse', 'label' => 'In Progress'],
                                                default => ['bg' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-clock', 'label' => ucfirst($bkStatus)],
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border {{ $stBadge['bg'] }}">
                                            <i class="fa-solid {{ $stBadge['icon'] }} text-xs"></i>
                                            <span>{{ $stBadge['label'] }}</span>
                                        </span>
                                    </div>
                                @endif
                            @else
                                {{-- TAB ARSIP: Status Final --}}
                                <div class="w-full flex items-center justify-between text-xs">
                                    <span class="text-gray-400">Status:</span>
                                    @php
                                        $stBadge = match($bkStatus) {
                                            'confirmed' => ['bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200', 'icon' => 'fa-circle-check', 'label' => 'Confirmed'],
                                            'completed' => ['bg' => 'bg-blue-100 text-blue-900 border-blue-200', 'icon' => 'fa-check-double', 'label' => 'Completed'],
                                            'canceled', 'cancelled' => ['bg' => 'bg-rose-100 text-rose-800 border-rose-200', 'icon' => 'fa-circle-xmark', 'label' => 'Cancelled'],
                                            'in_progress' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-200', 'icon' => 'fa-rotate fa-spin-pulse', 'label' => 'In Progress'],
                                            default => ['bg' => 'bg-gray-100 text-gray-800 border-gray-200', 'icon' => 'fa-clock', 'label' => ucfirst($bkStatus)],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border {{ $stBadge['bg'] }}">
                                        <i class="fa-solid {{ $stBadge['icon'] }} text-xs"></i>
                                        <span>{{ $stBadge['label'] }}</span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            {{-- Pagination Links --}}
            @if($notifications->hasPages())
            <div class="pt-4">
                {{ $notifications->links() }}
            </div>
            @endif

        </div>

        {{-- CANCEL RESERVATION MODAL --}}
        <div x-show="cancelModalOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border border-rose-100 relative"
                 @click.outside="cancelModalOpen = false">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-[#b01f44]">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 font-headline">Batalkan Reservasi</h3>
                            <p class="text-xs text-gray-500 font-mono" x-text="'#' + activeCancelBookingCode"></p>
                        </div>
                    </div>
                    <button @click="cancelModalOpen = false" class="text-gray-400 hover:text-gray-600 p-1">
                        <i class="fa-solid fa-xmark text-base"></i>
                    </button>
                </div>

                <form :action="'/admin/notifications/' + activeCancelNotifId + '/cancel'" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Alasan Pembatalan (Opsional)
                        </label>
                        <textarea name="cancel_reason" x-model="cancelReason" rows="3"
                                  placeholder="Contoh: Jadwal bertabrakan, permintaan pembatalan pelanggan, dll."
                                  class="w-full px-3 py-2 text-xs font-medium rounded-xl border-gray-200 focus:border-[#b01f44] focus:ring-[#b01f44] text-gray-800"></textarea>
                    </div>

                    <p class="text-xs text-rose-600 bg-rose-50 p-3 rounded-xl border border-rose-200">
                        Status reservasi akan otomatis diubah menjadi <strong>Cancelled</strong> dan notifikasi ini akan langsung dipindahkan ke <strong>Arsip Notifikasi</strong>.
                    </p>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="cancelModalOpen = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-all">
                            Tutup
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition-all shadow-sm">
                            Konfirmasi Pembatalan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-admin-layout>
