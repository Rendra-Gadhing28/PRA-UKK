<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Track Keuangan & Pengeluaran
                </h2>
                <p class="text-sm text-gray-500 mt-1">Pencatatan pengeluaran salon, scan struk eksternal otomatis, dan rincian item barang</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.finances.create') }}" 
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-xs font-bold shadow-md transition-all">
                    <i class="fa-solid fa-receipt text-xs"></i>
                    <span>Catat / Scan Struk Pengeluaran</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- SUMMARY STATS CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Total Pengeluaran Bulan Ini --}}
                <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pengeluaran Bulan Ini</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1 font-headline tabular-nums">
                            Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                        </h3>
                        <span class="text-xs text-gray-400 font-medium mt-0.5 block">Bulan {{ now()->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>

                {{-- Total Transaksi Pengeluaran --}}
                <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Catatan Transaksi</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1 font-headline tabular-nums">
                            {{ number_format($totalTransactionCount) }} Transaksi
                        </h3>
                        <span class="text-xs text-gray-400 font-medium mt-0.5 block">Pengeluaran terdaftar</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-600 flex items-center justify-center text-xl shrink-0 border border-gray-200">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                </div>

                {{-- Total Struk Ter-scan --}}
                <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Struk Ter-Scan / Bukti</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1 font-headline tabular-nums">
                            {{ number_format($totalScanStruk) }} Struk
                        </h3>
                        <span class="text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full font-bold border border-emerald-200 inline-block mt-0.5">Bukti foto & OCR</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shrink-0 border border-emerald-200">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                </div>

                {{-- Pemasukan Booking Bulan Ini --}}
                <div class="bg-white rounded-3xl p-6 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pemasukan Booking</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1 font-headline tabular-nums">
                            Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                        </h3>
                        <span class="text-xs text-gray-400 font-medium mt-0.5 block">Bulan {{ now()->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-400 flex items-center justify-center text-xl shrink-0 border border-rose-100">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                </div>

            </div>

            {{-- FILTER SECTION --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 space-y-4">
                <form method="GET" action="{{ route('admin.finances.index') }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                        
                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-layer-group text-rose-500 text-xs"></i>
                                Kategori
                            </label>
                            <select name="category" class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
                                <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}" {{ request('category') === $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal Mulai --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-rose-500 text-xs"></i>
                                Dari Tanggal
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Tanggal Sampai --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar-check text-rose-500 text-xs"></i>
                                Sampai Tanggal
                            </label>
                            <input type="date" name="date_until" value="{{ request('date_until') }}"
                                   class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Cari Kata Kunci --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-1.5">
                                <i class="fa-solid fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Transaksi
                            </label>
                            <input type="text" name="search" placeholder="Nama barang / judul..." value="{{ request('search') }}"
                                   class="w-full px-3 py-2.5 text-xs font-semibold rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-xs flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-sliders text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.finances.index') }}" class="py-2.5 px-3 rounded-xl bg-rose-100/70 text-rose-950 text-xs font-semibold hover:bg-rose-200 transition-all flex items-center justify-center" title="Reset Filter">
                                <i class="fa-solid fa-rotate-left text-xs"></i>
                            </a>
                        </div>

                    </div>

                    {{-- Quick Category Filter Pills --}}
                    <div class="pt-3 border-t border-rose-50 flex items-center gap-2 overflow-x-auto pb-1 scrollbar-none">
                        <span class="text-xs font-bold text-gray-400 shrink-0 uppercase tracking-wider">Quick Filter:</span>
                        
                        @php
                            $catCurrent = request('category', 'all');
                        @endphp

                        <a href="{{ route('admin.finances.index', array_filter(array_merge(request()->query(), ['category' => 'all']))) }}"
                           class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $catCurrent === 'all' ? 'bg-rose-500 text-white border-rose-500 shadow-xs' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50' }}">
                            <i class="fa-solid fa-receipt text-xs"></i>
                            <span>Semua Kategori</span>
                        </a>

                        @foreach($categories as $cat)
                            <a href="{{ route('admin.finances.index', array_filter(array_merge(request()->query(), ['category' => $cat->name]))) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-bold transition-all shrink-0 flex items-center gap-1.5 border {{ $catCurrent === $cat->name ? 'bg-rose-500 text-white border-rose-500 shadow-xs' : 'bg-white text-rose-950 border-gray-200 hover:bg-rose-50' }}">
                                <i class="fa-solid fa-tag text-xs"></i>
                                <span>{{ $cat->name }}</span>
                            </a>
                        @endforeach
                    </div>

                </form>
            </div>

            {{-- EXPENSES TABLE --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-rose-950 border-b border-rose-100">
                                <th class="py-3.5 px-5">Tanggal</th>
                                <th class="py-3.5 px-5">Judul Pengeluaran</th>
                                <th class="py-3.5 px-5">Kategori</th>
                                <th class="py-3.5 px-5">Rincian Item</th>
                                <th class="py-3.5 px-5">Total Uang (Rp)</th>
                                <th class="py-3.5 px-5">Bukti / Struk</th>
                                <th class="py-3.5 px-5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-rose-50 text-sm">
                            @forelse($expenses as $item)
                            @php
                                $itemsDetail = $item->metadata['items'] ?? [];
                                $itemCount = count($itemsDetail);
                                $isScanned = $item->metadata['scanned'] ?? false;
                            @endphp
                            <tr class="hover:bg-rose-50/30 transition-colors" x-data="{ showDetail: false, showStrukModal: false }">
                                
                                {{-- Tanggal --}}
                                <td class="py-3.5 px-5 text-xs font-bold text-gray-700 shrink-0">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-regular fa-calendar-check text-rose-400 text-xs"></i>
                                        <span>{{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}</span>
                                    </div>
                                </td>

                                {{-- Judul Pengeluaran --}}
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-gray-900 block leading-snug">{{ $item->title }}</div>
                                    @if($item->description)
                                        <span class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $item->description }}</span>
                                    @endif
                                </td>

                                {{-- Kategori --}}
                                <td class="py-3.5 px-5">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-[#f45472] border border-rose-100 inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-tag text-xs"></i>
                                        {{ $item->category }}
                                    </span>
                                </td>

                                {{-- Rincian Item (Barang, Qty, Harga/pc) --}}
                                <td class="py-3.5 px-5">
                                    @if($itemCount > 0)
                                        <button type="button" @click="showDetail = !showDetail" 
                                                class="px-3 py-1.5 rounded-full bg-rose-50 hover:bg-rose-100 text-[#f45472] text-xs font-bold border border-rose-200 flex items-center gap-1.5 transition-all shadow-2xs"
                                                aria-label="Lihat rincian item pengeluaran {{ $item->title }}">
                                            <i class="fa-solid fa-boxes-packing text-xs"></i>
                                            <span>{{ $itemCount }} Item Barang</span>
                                            <i class="fa-solid fa-chevron-down text-xs transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''"></i>
                                        </button>

                                        {{-- Inline Item Breakdown Modal / Popover --}}
                                        <div x-show="showDetail" x-cloak @click.outside="showDetail = false"
                                             class="mt-2 bg-white rounded-2xl p-4 border border-rose-200 shadow-xl space-y-2 z-30">
                                            <div class="text-xs font-bold text-gray-900 border-b border-gray-100 pb-2 flex items-center justify-between">
                                                <span>Detail Rincian Item Struk</span>
                                                <span class="text-xs font-normal text-gray-400">Harga per PC & Total</span>
                                            </div>
                                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                                @foreach($itemsDetail as $it)
                                                    <div class="flex items-center justify-between text-xs bg-rose-50/50 p-2 rounded-xl border border-rose-100/60">
                                                        <div>
                                                            <strong class="text-gray-900 block">{{ $it['name'] }}</strong>
                                                            <span class="text-xs text-gray-500 tabular-nums">
                                                                {{ $it['qty'] }} pcs &times; Rp {{ number_format($it['unit_price'], 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <span class="font-bold text-rose-600 tabular-nums">
                                                            Rp {{ number_format($it['subtotal'], 0, ',', '.') }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tanpa rincian item</span>
                                    @endif
                                </td>

                                {{-- Total Uang --}}
                                <td class="py-3.5 px-5 font-extrabold text-rose-600 text-sm tabular-nums">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>

                                {{-- Bukti Struk / Scan Badge --}}
                                <td class="py-3.5 px-5">
                                    @if($item->receipt_image)
                                        <button type="button" @click="showStrukModal = true"
                                                class="px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-200 text-xs font-bold flex items-center gap-1.5 transition-all shadow-2xs"
                                                aria-label="Lihat foto struk {{ $item->title }}">
                                            <i class="fa-solid fa-file-invoice text-emerald-600 text-xs"></i>
                                            <span>Lihat Struk {{ $isScanned ? '(OCR)' : '' }}</span>
                                        </button>

                                        {{-- Struk Modal Preview --}}
                                        <div x-show="showStrukModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
                                            <div @click.outside="showStrukModal = false" class="bg-white rounded-3xl p-6 max-w-lg w-full border border-rose-100 shadow-2xl space-y-4 relative">
                                                <div class="flex items-center justify-between border-b border-rose-100 pb-3">
                                                    <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2 font-headline">
                                                        <i class="fa-solid fa-receipt text-[#f45472] text-sm"></i>
                                                        Foto Struk Pengeluaran
                                                    </h3>
                                                    <button @click="showStrukModal = false" class="text-gray-400 hover:text-rose-600 transition-colors">
                                                        <i class="fa-solid fa-xmark text-sm"></i>
                                                    </button>
                                                </div>

                                                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 flex items-center justify-center max-h-96">
                                                    <img src="{{ Storage::url($item->receipt_image) }}" alt="Foto Struk" class="max-h-96 w-full object-contain">
                                                </div>

                                                <div class="flex items-center justify-between text-xs pt-2">
                                                    <span class="text-gray-500 font-medium">Judul: <strong class="text-gray-900">{{ $item->title }}</strong></span>
                                                    <span class="text-rose-600 font-extrabold text-sm tabular-nums">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada foto</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 px-5 text-center">
                                    <form method="POST" action="{{ route('admin.finances.destroy', $item->id) }}" 
                                          x-data="{ showConfirm: false }"
                                          @submit.prevent="showConfirm = true">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 rounded-full bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100 transition-all border border-rose-200" 
                                                title="Hapus Pengeluaran"
                                                aria-label="Hapus pengeluaran {{ $item->title }}">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>

                                        {{-- Alpine.js Confirmation Modal --}}
                                        <template x-if="showConfirm">
                                            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                                                 @click.self="showConfirm = false"
                                                 @keydown.escape.window="showConfirm = false">
                                                <div class="bg-white rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 border border-rose-100 text-left"
                                                     @click.stop>
                                                    <div class="flex items-center gap-3 mb-4">
                                                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                                                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="text-sm font-bold text-gray-900">Hapus Catatan Pengeluaran?</h4>
                                                            <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[200px]">{{ $item->title }}</p>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs text-gray-600 mb-5 leading-relaxed">
                                                        Data pengeluaran sebesar <strong class="text-rose-600 tabular-nums">Rp {{ number_format($item->amount, 0, ',', '.') }}</strong> akan dihapus permanen.
                                                    </p>
                                                    <div class="flex items-center gap-2">
                                                        <button type="button" @click="showConfirm = false"
                                                                class="flex-1 py-2.5 px-4 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold hover:bg-gray-200 transition-colors">
                                                            Batal
                                                        </button>
                                                        <button type="button" 
                                                                @click="$el.closest('form').removeEventListener('submit', arguments.callee); $el.closest('form').submit()"
                                                                class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 text-white text-xs font-bold hover:bg-rose-700 transition-colors">
                                                            Ya, Hapus
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </form>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-rose-50 flex items-center justify-center">
                                            <i class="fa-solid fa-receipt text-rose-300 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-500">Belum ada catatan pengeluaran keuangan</p>
                                            <p class="text-xs text-gray-400 mt-1">Coba ubah tanggal filter atau catat pengeluaran baru</p>
                                        </div>
                                        <a href="{{ route('admin.finances.create') }}" 
                                           class="mt-2 inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-colors shadow-xs">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                            <span>Catat Pengeluaran Baru</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-rose-100">
                    {{ $expenses->links() }}
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
