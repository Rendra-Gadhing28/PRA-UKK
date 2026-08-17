<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-4 font-headline">
                    <span class="w-4 h-8 bg-[#f45472] rounded-full inline-block"></span>
                    Track Keuangan & Pengeluaran
                </h2>
                <p class="text-sm text-gray-500 mt-1">Pencatatan pengeluaran salon, scan struk eksternal otomatis, dan rincian item barang</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.finances.create') }}" 
                   class="inline-flex items-center gap-4 px-8 py-4 rounded-full bg-[#f45472] text-white hover:bg-[#d93856] text-xs font-bold shadow-md transition-all">
                    <i class="fas fa-receipt text-xs"></i>
                    <span>Catat / Scan Struk Pengeluaran</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- SUMMARY STATS CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                {{-- Total Pengeluaran Bulan Ini --}}
                <div class="bg-white rounded-3xl p-8 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pengeluaran Bulan Ini</p>
                        <h3 class="text-2xl font-black text-rose-600 mt-1 font-headline">
                            Rp {{ number_format($monthlyExpense, 0, ',', '.') }}
                        </h3>
                        <span class="text-[10px] text-gray-400 font-medium">Bulan {{ now()->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>

                {{-- Total Transaksi Pengeluaran --}}
                <div class="bg-white rounded-3xl p-8 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Catatan Transaksi</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1 font-headline">
                            {{ $totalTransactionCount }} Transaksi
                        </h3>
                        <span class="text-[10px] text-gray-400 font-medium">Pengeluaran terdaftar</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-600 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-list-check"></i>
                    </div>
                </div>

                {{-- Total Struk Ter-scan --}}
                <div class="bg-white rounded-3xl p-8 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Struk Ter-Scan / Bukti</p>
                        <h3 class="text-2xl font-black text-emerald-600 mt-1 font-headline">
                            {{ $totalScanStruk }} Struk
                        </h3>
                        <span class="text-[10px] text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full font-bold">Bukti foto & OCR</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-qrcode"></i>
                    </div>
                </div>

                {{-- Pemasukan Booking Bulan Ini --}}
                <div class="bg-white rounded-3xl p-8 border border-rose-100 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Pemasukan Booking</p>
                        <h3 class="text-2xl font-black text-gray-900 mt-1 font-headline">
                            Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                        </h3>
                        <span class="text-[10px] text-gray-400 font-medium">Bulan {{ now()->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-400 flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>

            </div>

            {{-- FILTER SECTION --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100">
                <form method="GET" action="{{ route('admin.finances.index') }}" class="space-y-4">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                        
                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="fas fa-layer-group text-rose-500 text-xs"></i>
                                Kategori
                            </label>
                            <select name="category" class="w-full px-4 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800 bg-white">
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
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="far fa-calendar-alt text-rose-500 text-xs"></i>
                                Dari Tanggal
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                   class="w-full px-4 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Tanggal Sampai --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="far fa-calendar-alt text-rose-500 text-xs"></i>
                                Sampai Tanggal
                            </label>
                            <input type="date" name="date_until" value="{{ request('date_until') }}"
                                   class="w-full px-4 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Cari Kata Kunci --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-4 flex items-center gap-4">
                                <i class="fas fa-magnifying-glass text-rose-500 text-xs"></i>
                                Cari Transaksi
                            </label>
                            <input type="text" name="search" placeholder="Nama barang / judul..." value="{{ request('search') }}"
                                   class="w-full px-4 py-4 text-xs font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] text-gray-800">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-4">
                            <button type="submit" class="flex-1 py-4 px-4 rounded-2xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] transition-all shadow-sm flex items-center justify-center gap-4">
                                <i class="fas fa-filter text-xs"></i>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('admin.finances.index') }}" class="py-4 px-4 rounded-2xl bg-gray-100 text-gray-600 text-xs font-semibold hover:bg-rose-50 hover:text-rose-600 transition-all flex items-center justify-center" title="Reset Filter">
                                <i class="fas fa-rotate-left text-xs"></i>
                            </a>
                        </div>

                    </div>

                </form>
            </div>

            {{-- EXPENSES TABLE --}}
            <div class="bg-white rounded-3xl shadow-sm border border-rose-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-gray-500 border-b border-rose-100">
                                <th class="py-4 px-4">Tanggal</th>
                                <th class="py-4 px-4">Judul Pengeluaran</th>
                                <th class="py-4 px-4">Kategori</th>
                                <th class="py-4 px-4">Rincian Item</th>
                                <th class="py-4 px-4">Total Uang (Rp)</th>
                                <th class="py-4 px-4">Bukti / Struk</th>
                                <th class="py-4 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($expenses as $item)
                            @php
                                $itemsDetail = $item->metadata['items'] ?? [];
                                $itemCount = count($itemsDetail);
                                $isScanned = $item->metadata['scanned'] ?? false;
                            @endphp
                            <tr class="hover:bg-rose-50/30 transition-colors" x-data="{ showDetail: false, showStrukModal: false }">
                                
                                {{-- Tanggal --}}
                                <td class="py-4 px-4 text-xs font-bold text-gray-700 shrink-0">
                                    <div class="flex items-center gap-2">
                                        <i class="far fa-calendar-check text-rose-400"></i>
                                        <span>{{ \Carbon\Carbon::parse($item->transaction_date)->format('d M Y') }}</span>
                                    </div>
                                </td>

                                {{-- Judul Pengeluaran --}}
                                <td class="py-4 px-4">
                                    <div class="font-bold text-gray-900 block">{{ $item->title }}</div>
                                    @if($item->description)
                                        <span class="text-xs text-gray-400 line-clamp-1">{{ $item->description }}</span>
                                    @endif
                                </td>

                                {{-- Kategori --}}
                                <td class="py-4 px-4">
                                    <span class="px-4 py-2 rounded-full text-xs font-semibold bg-rose-50 text-rose-600 border border-rose-100 inline-flex items-center gap-2">
                                        <i class="fas fa-tag text-xs"></i>
                                        {{ $item->category }}
                                    </span>
                                </td>

                                {{-- Rincian Item (Barang, Qty, Harga/pc) --}}
                                <td class="py-4 px-4">
                                    @if($itemCount > 0)
                                        <button type="button" @click="showDetail = !showDetail" 
                                                class="px-4 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-[#f45472] text-xs font-bold border border-rose-200 flex items-center gap-2 transition-all">
                                            <i class="fas fa-boxes-packing text-xs"></i>
                                            <span>{{ $itemCount }} Item Barang</span>
                                            <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="showDetail ? 'rotate-180' : ''"></i>
                                        </button>

                                        {{-- Inline Item Breakdown Modal / Popover --}}
                                        <div x-show="showDetail" x-cloak @click.outside="showDetail = false"
                                             class="mt-2 bg-white rounded-2xl p-4 border border-rose-200 shadow-xl space-y-2 z-30">
                                            <div class="text-xs font-bold text-gray-900 border-b border-gray-100 pb-2 flex items-center justify-between">
                                                <span>Detail Rincian Item Struk</span>
                                                <span class="text-[10px] font-normal text-gray-400">Harga per PC & Total</span>
                                            </div>
                                            <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                                                @foreach($itemsDetail as $it)
                                                    <div class="flex items-center justify-between text-xs bg-rose-50/50 p-2 rounded-xl border border-rose-100/60">
                                                        <div>
                                                            <strong class="text-gray-900 block">{{ $it['name'] }}</strong>
                                                            <span class="text-[10px] text-gray-500">
                                                                {{ $it['qty'] }} pcs × Rp {{ number_format($it['unit_price'], 0, ',', '.') }}
                                                            </span>
                                                        </div>
                                                        <span class="font-bold text-rose-600">
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
                                <td class="py-4 px-4 font-black text-rose-600 text-base">
                                    Rp {{ number_format($item->amount, 0, ',', '.') }}
                                </td>

                                {{-- Bukti Struk / Scan Badge --}}
                                <td class="py-4 px-4">
                                    @if($item->receipt_image)
                                        <button type="button" @click="showStrukModal = true"
                                                class="px-4 py-2 rounded-2xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-bold flex items-center gap-2 transition-all">
                                            <i class="fas fa-file-invoice text-xs"></i>
                                            <span>Lihat Struk {{ $isScanned ? '(OCR)' : '' }}</span>
                                        </button>

                                        {{-- Struk Modal Preview --}}
                                        <div x-show="showStrukModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm">
                                            <div @click.outside="showStrukModal = false" class="bg-white rounded-3xl p-8 max-w-lg w-full border border-rose-100 shadow-2xl space-y-4 relative">
                                                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                                                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                                        <i class="fas fa-receipt text-[#f45472]"></i>
                                                        Foto Struk Pengeluaran
                                                    </h3>
                                                    <button @click="showStrukModal = false" class="text-gray-400 hover:text-rose-600">
                                                        <i class="fas fa-xmark text-base"></i>
                                                    </button>
                                                </div>

                                                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 flex items-center justify-center max-h-96">
                                                    <img src="{{ Storage::url($item->receipt_image) }}" alt="Foto Struk" class="max-h-96 w-full object-contain">
                                                </div>

                                                <div class="flex items-center justify-between text-xs pt-2">
                                                    <span class="text-gray-500 font-medium">Judul: <strong>{{ $item->title }}</strong></span>
                                                    <span class="text-rose-600 font-bold text-sm">Rp {{ number_format($item->amount, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada foto</span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="py-4 px-4 text-center">
                                    <form method="POST" action="{{ route('admin.finances.destroy', $item->id) }}" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pengeluaran {{ $item->title }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400 text-sm">
                                    Belum ada catatan pengeluaran keuangan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-gray-100">
                    {{ $expenses->links() }}
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>
