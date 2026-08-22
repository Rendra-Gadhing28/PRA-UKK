<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tracker Pengeluaran Struk AI
        </h2>
    </x-slot>

    {{-- Ornamen Ambient Background Blobs --}}
    <div class="blob-bg bg-accent-clear w-[500px] h-[500px] -top-24 -left-24 rounded-[40%_60%_70%_30%]" aria-hidden="true"></div>
    <div class="blob-bg bg-primary-fixed-dim w-[600px] h-[600px] top-1/2 -right-36 rounded-[60%_40%_30%_70%] opacity-20" aria-hidden="true"></div>

    <div x-data="expensePage()" class="relative z-10 min-h-screen pt-28 pb-24">
        <main class="max-w-[1280px] mx-auto px-4 md:px-8 space-y-8">

            {{-- Header & CTA --}}
            <header class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight flex items-center gap-2">
                        <span>Tracker Pengeluaran Struk</span>
                        <span class="px-3 py-1 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-full text-xs font-semibold shadow-sm">
                            Gemini 2.5 Flash AI ✨
                        </span>
                    </h1>
                    <p class="text-gray-500 text-sm mt-1">
                        Pindai foto struk belanja secara otomatis dan kelola pengeluaran harian Anda dengan cerdas.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <label for="receipt-file-input" class="cursor-pointer bg-gradient-brand text-white font-bold px-5 py-3 rounded-xl shadow-lg hover:shadow-accent/40 hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center gap-2 text-sm uppercase tracking-wider">
                        <span class="material-symbols-outlined">document_scanner</span>
                        <span>Scan Struk Baru</span>
                        <input type="file" id="receipt-file-input" accept="image/jpeg,image/png,image/webp" capture="environment" class="hidden" @change="handleFileSelect($event)">
                    </label>
                </div>
            </header>

            {{-- Summary Metric Cards --}}
            <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="glass-card rounded-2xl p-5 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Pengeluaran Bulan Ini</p>
                        <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-accent shrink-0">
                            <span class="material-symbols-outlined">payments</span>
                        </div>
                    </div>
                    <p class="text-2xl font-extrabold text-gray-900">
                        Rp {{ number_format($totalSpendingThisMonth, 0, ',', '.') }}
                    </p>
                </div>

                <div class="glass-card rounded-2xl p-5 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Struk Tersimpan</p>
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 shrink-0">
                            <span class="material-symbols-outlined">receipt_long</span>
                        </div>
                    </div>
                    <p class="text-2xl font-extrabold text-gray-900">
                        {{ number_format($totalTransactionsCount) }} Transaksi
                    </p>
                </div>

                <div class="glass-card rounded-2xl p-5 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500">Model OCR AI</p>
                        <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                            <span class="material-symbols-outlined">auto_awesome</span>
                        </div>
                    </div>
                    <p class="text-base font-bold text-emerald-600 flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Gemini 2.5 Flash Ready</span>
                    </p>
                </div>
            </section>

            {{-- Table / List Transaksi --}}
            <section class="glass-card rounded-2xl p-6 bg-white/80 border border-gray-200/60 shadow-xl space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-extrabold text-gray-900 flex items-center gap-2">
                        <span class="material-symbols-outlined text-accent">history</span>
                        <span>Riwayat Pengeluaran</span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-xs font-bold uppercase tracking-wider text-gray-400">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Merchant & Cabang</th>
                                <th class="py-3 px-4">Metode Bayar</th>
                                <th class="py-3 px-4">Jumlah Item</th>
                                <th class="py-3 px-4 text-right">Total Transaksi</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($expenses as $expense)
                                <tr class="hover:bg-rose-50/40 transition-colors">
                                    <td class="py-4 px-4 font-semibold text-gray-800">
                                        {{ $expense->transaction_date ? $expense->transaction_date->format('d M Y, H:i') : '-' }}
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-gray-900">{{ $expense->merchant }}</div>
                                        @if($expense->branch)
                                            <div class="text-xs text-gray-400">{{ $expense->branch }}</div>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4">
                                        <span class="px-2.5 py-1 rounded-md bg-gray-100 text-gray-700 text-xs font-semibold uppercase">
                                            {{ $expense->payment_method ?? 'Cash' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 font-medium text-gray-600">
                                        {{ $expense->items->count() }} Item
                                    </td>
                                    <td class="py-4 px-4 text-right font-extrabold text-rose-600">
                                        Rp {{ number_format($expense->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <button type="button" @click="openDetailModal({{ json_encode($expense) }})" class="inline-flex items-center gap-1 text-xs font-bold text-accent hover:underline">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                            <span>Detail</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-400">
                                        <span class="material-symbols-outlined text-4xl mb-2 text-gray-300">receipt</span>
                                        <p>Belum ada data pengeluaran tersimpan. Silakan upload struk belanja baru.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $expenses->links() }}
                </div>
            </section>
        </main>

        {{-- Modal Loading Scan AI --}}
        <div x-show="isScanning" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
            <div class="bg-white rounded-2xl p-8 max-w-sm w-full text-center space-y-4 shadow-2xl">
                <div class="relative w-16 h-16 mx-auto">
                    <div class="w-16 h-16 rounded-full border-4 border-rose-200 border-t-accent animate-spin"></div>
                    <span class="material-symbols-outlined absolute inset-0 m-auto text-accent text-2xl animate-pulse">auto_awesome</span>
                </div>
                <div>
                    <h4 class="font-extrabold text-gray-900 text-lg">Menganalisis Struk...</h4>
                    <p class="text-xs text-gray-500 mt-1">Gemini 2.5 Flash AI sedang membaca detail toko, harga, dan item belanjaan Anda.</p>
                </div>
            </div>
        </div>

        {{-- Modal Verifikasi & Edit Data Struk --}}
        <div x-show="showVerifyModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-4xl w-full p-6 md:p-8 shadow-2xl space-y-6 relative my-8">
                
                <button type="button" @click="showVerifyModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>

                <div class="flex items-center gap-3 border-b pb-4">
                    <div class="w-10 h-10 rounded-full bg-rose-100 flex items-center justify-center text-accent font-bold">
                        <span class="material-symbols-outlined">fact_check</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900">Verifikasi Data Struk AI</h3>
                        <p class="text-xs text-gray-500">Periksa dan koreksi data transaksi sebelum disimpan ke database.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    {{-- Left Column: Image Preview --}}
                    <div class="lg:col-span-5 bg-gray-50 rounded-xl p-3 border border-gray-200 text-center">
                        <p class="text-xs font-bold uppercase text-gray-400 mb-2">Foto Struk</p>
                        <img :src="scanResult.image_url" alt="Struk Preview" class="max-h-[380px] w-auto mx-auto rounded-lg object-contain shadow-md">
                    </div>

                    {{-- Right Column: Form Header Transaksi & Items --}}
                    <div class="lg:col-span-7 space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Merchant / Toko</label>
                                <input type="text" x-model="formData.merchant" class="w-full rounded-xl border-gray-300 text-sm px-3 py-2 focus:ring-accent focus:border-accent">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Cabang</label>
                                <input type="text" x-model="formData.branch" placeholder="Contoh: Cabang Simpang Lima" class="w-full rounded-xl border-gray-300 text-sm px-3 py-2 focus:ring-accent focus:border-accent">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Tanggal Transaksi</label>
                                <input type="datetime-local" x-model="formData.transaction_date" class="w-full rounded-xl border-gray-300 text-sm px-3 py-2 focus:ring-accent focus:border-accent">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Metode Pembayaran</label>
                                <input type="text" x-model="formData.payment_method" placeholder="Cash / QRIS / Debit" class="w-full rounded-xl border-gray-300 text-sm px-3 py-2 focus:ring-accent focus:border-accent">
                            </div>
                        </div>

                        {{-- Item Table Form --}}
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase">Daftar Barang Belanja</label>
                                <button type="button" @click="addItemRow()" class="text-xs font-bold text-accent hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                    <span>Tambah Item</span>
                                </button>
                            </div>

                            <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-xl divide-y">
                                <template x-for="(item, index) in formData.items" :key="index">
                                    <div class="p-3 bg-gray-50/50 space-y-2 text-xs">
                                        <div class="flex items-center gap-2">
                                            <input type="text" x-model="item.item_name" placeholder="Nama barang" class="flex-1 rounded-lg border-gray-300 text-xs px-2.5 py-1.5 focus:ring-accent focus:border-accent">
                                            <button type="button" @click="removeItemRow(index)" class="text-red-500 hover:text-red-700 p-1">
                                                <span class="material-symbols-outlined text-base">delete</span>
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-4 gap-2">
                                            <div>
                                                <span class="text-[10px] text-gray-400">Qty</span>
                                                <input type="number" min="1" x-model.number="item.qty" @input="recalculateItemSubtotal(item)" class="w-full rounded-lg border-gray-300 text-xs px-2 py-1">
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-gray-400">Harga Satuan</span>
                                                <input type="number" step="0.01" x-model.number="item.unit_price" @input="recalculateItemSubtotal(item)" class="w-full rounded-lg border-gray-300 text-xs px-2 py-1">
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-gray-400">Subtotal</span>
                                                <input type="number" step="0.01" x-model.number="item.subtotal" @input="recalculateGrandTotal()" class="w-full rounded-lg border-gray-300 text-xs px-2 py-1 font-bold text-gray-800">
                                            </div>
                                            <div>
                                                <span class="text-[10px] text-gray-400">Kategori</span>
                                                <select x-model="item.category" class="w-full rounded-lg border-gray-300 text-xs px-1 py-1">
                                                    <option value="Kebutuhan">Kebutuhan</option>
                                                    <option value="Makanan">Makanan</option>
                                                    <option value="Operasional">Operasional</option>
                                                    <option value="Kecantikan">Kecantikan</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Total Summary & Submit --}}
                        <div class="bg-rose-50 p-4 rounded-xl flex items-center justify-between border border-rose-200">
                            <div>
                                <span class="text-xs font-bold uppercase text-gray-500">Total Akhir Belanja</span>
                                <div class="text-xl font-extrabold text-accent">
                                    Rp <span x-text="formatRupiah(formData.total_amount)"></span>
                                </div>
                            </div>
                            <button type="button" @click="saveExpenseData()" :disabled="isSaving" class="bg-gradient-brand text-white font-bold px-6 py-3 rounded-xl shadow-lg hover:shadow-accent/40 disabled:opacity-50 transition-all flex items-center gap-2 text-xs uppercase tracking-wider">
                                <span x-show="!isSaving" class="material-symbols-outlined text-base">save</span>
                                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Transaksi'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Detail Transaksi --}}
        <div x-show="showDetailModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-md">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 relative">
                <button type="button" @click="showDetailModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-outlined text-2xl">close</span>
                </button>

                <h3 class="text-lg font-extrabold text-gray-900 border-b pb-2">Detail Transaksi Struk</h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between"><span class="text-gray-500">Merchant:</span><strong x-text="selectedExpense.merchant"></strong></div>
                    <div class="flex justify-between"><span class="text-gray-500">Cabang:</span><span x-text="selectedExpense.branch || '-'"></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Tanggal:</span><span x-text="selectedExpense.transaction_date"></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Metode Pembayaran:</span><span x-text="selectedExpense.payment_method || 'Cash'"></span></div>
                </div>

                <div class="border-t pt-3">
                    <p class="text-xs font-bold text-gray-700 mb-2">Item Belanja:</p>
                    <div class="max-h-48 overflow-y-auto divide-y text-xs">
                        <template x-for="item in selectedExpense.items" :key="item.id">
                            <div class="py-2 flex justify-between">
                                <div>
                                    <div class="font-bold text-gray-800" x-text="item.item_name"></div>
                                    <div class="text-[10px] text-gray-400" x-text="item.qty + ' x Rp ' + formatRupiah(item.unit_price) + ' (' + item.category + ')'"></div>
                                </div>
                                <div class="font-bold text-gray-900">
                                    Rp <span x-text="formatRupiah(item.subtotal)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="border-t pt-3 flex justify-between items-center text-sm font-extrabold text-accent">
                    <span>Total Pengeluaran:</span>
                    <span>Rp <span x-text="formatRupiah(selectedExpense.total_amount)"></span></span>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('expensePage', () => ({
                isScanning: false,
                showVerifyModal: false,
                showDetailModal: false,
                isSaving: false,
                
                scanResult: {},
                selectedExpense: { items: [] },
                
                formData: {
                    merchant: '',
                    branch: '',
                    transaction_date: '',
                    payment_method: 'Cash',
                    total_amount: 0,
                    temp_path: '',
                    items: []
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('receipt', file);

                    this.isScanning = true;

                    fetch('{{ route("expenses.scan") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.isScanning = false;
                        if (res.success) {
                            this.scanResult = res;
                            
                            // Format tanggal ke input datetime-local
                            let formattedDate = new Date().toISOString().slice(0, 16);
                            if (res.data.transaction_date) {
                                formattedDate = res.data.transaction_date.replace(' ', 'T').slice(0, 16);
                            }

                            this.formData = {
                                merchant: res.data.merchant || '',
                                branch: res.data.branch || '',
                                transaction_date: formattedDate,
                                payment_method: res.data.payment_method || 'Cash',
                                total_amount: res.data.total_amount || 0,
                                temp_path: res.temp_path || '',
                                items: res.data.items || []
                            };

                            this.showVerifyModal = true;
                        } else {
                            alert(res.message || 'Gagal memproses struk.');
                        }
                    })
                    .catch(err => {
                        this.isScanning = false;
                        alert('Terjadi kesalahan jaringan atau server saat memproses struk.');
                    });

                    event.target.value = '';
                },

                addItemRow() {
                    this.formData.items.push({
                        item_name: 'Item Baru',
                        qty: 1,
                        unit_price: 0,
                        subtotal: 0,
                        category: 'Kebutuhan'
                    });
                },

                removeItemRow(index) {
                    this.formData.items.splice(index, 1);
                    this.recalculateGrandTotal();
                },

                recalculateItemSubtotal(item) {
                    item.subtotal = (item.qty || 0) * (item.unit_price || 0);
                    this.recalculateGrandTotal();
                },

                recalculateGrandTotal() {
                    this.formData.total_amount = this.formData.items.reduce((sum, i) => sum + (parseFloat(i.subtotal) || 0), 0);
                },

                saveExpenseData() {
                    if (this.isSaving) return;
                    this.isSaving = true;

                    fetch('{{ route("expenses.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(res => res.json())
                    .then(res => {
                        this.isSaving = false;
                        if (res.success) {
                            this.showVerifyModal = false;
                            window.location.reload();
                        } else {
                            alert(res.message || 'Gagal menyimpan transaksi.');
                        }
                    })
                    .catch(err => {
                        this.isSaving = false;
                        alert('Terjadi kesalahan server saat menyimpan transaksi.');
                    });
                },

                openDetailModal(expense) {
                    this.selectedExpense = expense;
                    this.showDetailModal = true;
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number || 0);
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
