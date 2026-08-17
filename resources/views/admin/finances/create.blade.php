<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.finances.index') }}" class="p-4 rounded-full bg-white border border-rose-200 text-gray-600 hover:text-rose-600 hover:bg-rose-50 transition-all">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-4 font-headline">
                    Catat / Scan Struk Pengeluaran
                </h2>
                <p class="text-xs text-gray-500 mt-1">Input manual atau scan langsung pakai kamera untuk mendeteksi barang, qty & harga</p>
            </div>
        </div>
    </x-slot>

    {{-- html5-qrcode: Simple QR/Barcode Scanner with native browser camera permission --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <div class="py-8" x-data="expenseForm()">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            {{-- TAB SELECTION (MANUAL VS SCAN KAMERA) --}}
            <div class="bg-white rounded-3xl p-4 shadow-sm border border-rose-100 flex items-center gap-4">
                <button type="button" @click="switchMode('manual')" 
                        :class="mode === 'manual' ? 'bg-[#f45472] text-white shadow-md' : 'bg-gray-50 text-gray-600 hover:bg-rose-50 hover:text-rose-600'"
                        class="flex-1 py-4 px-4 rounded-2xl text-xs font-bold transition-all flex items-center justify-center gap-4">
                    <i class="fas fa-pen-to-square text-xs"></i>
                    <span>Mode 1: Input Manual</span>
                </button>

                <button type="button" @click="switchMode('scan')" 
                        :class="mode === 'scan' ? 'bg-[#f45472] text-white shadow-md' : 'bg-gray-50 text-gray-600 hover:bg-rose-50 hover:text-rose-600'"
                        class="flex-1 py-4 px-4 rounded-2xl text-xs font-bold transition-all flex items-center justify-center gap-4">
                    <i class="fas fa-camera text-xs"></i>
                    <span>Mode 2: Live Camera Scan Struk</span>
                </button>
            </div>

            {{-- SCAN BARCODE / QR CODE STRUK -- SIMPLE BROWSER CAMERA --}}
            <div x-show="mode === 'scan'" x-cloak class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100 space-y-6">
                <div class="border-b border-rose-100 pb-4">
                    <h3 class="font-bold text-gray-900 text-base flex items-center gap-4 font-headline">
                        <i class="fas fa-qrcode text-[#f45472]"></i>
                        Scan Barcode / QR Code Struk
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">
                        Arahkan kamera ke barcode atau QR code yang ada di struk belanja. Browser akan meminta izin akses kamera — klik <strong>Allow / Izinkan</strong>.
                    </p>
                </div>

                {{-- Scanner Viewfinder (html5-qrcode renders here) --}}
                <div id="qr-scanner-box" class="rounded-2xl overflow-hidden border-2 border-rose-200 bg-gray-900" style="min-height: 300px;"></div>

                {{-- Scan Status Result --}}
                <div id="qr-scan-result" class="hidden bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start gap-4">
                    <div class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center shrink-0">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider mb-1">Kode Berhasil Terbaca!</p>
                        <p class="text-sm font-mono font-bold text-emerald-900 break-all" id="qr-scan-text"></p>
                        <p class="text-[10px] text-emerald-600 mt-1">Kode ini otomatis diisi ke kolom kode voucher / referensi nota di bawah.</p>
                    </div>
                </div>

                {{-- Scanner Controls --}}
                <div class="flex items-center gap-4">
                    <button type="button" id="btn-start-scanner"
                            onclick="startQrScanner()"
                            class="px-8 py-4 rounded-2xl bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] shadow-md hover:shadow-lg transition-all flex items-center gap-4">
                        <i class="fas fa-camera text-sm"></i>
                        <span>Buka Kamera & Scan</span>
                    </button>

                    <button type="button" id="btn-stop-scanner"
                            onclick="stopQrScanner()"
                            style="display:none;"
                            class="px-8 py-4 rounded-2xl bg-gray-800 text-white text-xs font-bold hover:bg-gray-900 shadow-md transition-all flex items-center gap-4">
                        <i class="fas fa-stop text-xs"></i>
                        <span>Hentikan Kamera</span>
                    </button>
                </div>
            </div>


            {{-- FORM CORE (STORE EXPENSE) --}}
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-rose-100">
                <form method="POST" action="{{ route('admin.finances.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    <input type="hidden" name="is_scanned" :value="isScanned ? '1' : '0'">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        {{-- Judul Transaksi --}}
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Judul Pengeluaran *</label>
                            <input type="text" name="title" x-model="title" required placeholder="Contoh: Pembelian Bahan Baku Salon Supplier X" 
                                   class="w-full px-4 py-4 text-sm font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Kategori Pengeluaran *</label>
                            <select name="category" x-model="category" required class="w-full px-4 py-4 text-sm font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] bg-white">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        {{-- Tanggal Transaksi --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Tanggal Transaksi *</label>
                            <input type="date" name="transaction_date" x-model="transactionDate" required 
                                   class="w-full px-4 py-4 text-sm font-semibold rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                            @error('transaction_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    {{-- ITEM TABLE (RINCIAN BARANG, QTY, HARGA PER PC & TOTAL) --}}
                    <div class="space-y-4 pt-4 border-t border-rose-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-gray-900 text-sm flex items-center gap-4 uppercase tracking-wider">
                                    <i class="fas fa-boxes-stacked text-[#f45472]"></i>
                                    Rincian Item Barang (Nama, Qty, Harga / PC)
                                </h3>
                                <p class="text-[11px] text-gray-400 mt-1">Hasil scan kamera otomatis mengisi baris di bawah. Anda dapat menyesuaikan jika diperlukan.</p>
                            </div>

                            <button type="button" @click="addItemRow()" 
                                    class="px-4 py-2 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-600 text-xs font-bold transition-all flex items-center gap-2 border border-rose-200">
                                <i class="fas fa-plus text-xs"></i>
                                <span>Tambah Item</span>
                            </button>
                        </div>

                        {{-- Table Rows --}}
                        <div class="overflow-x-auto border border-rose-100 rounded-2xl">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-rose-50/60 font-bold uppercase text-gray-600 border-b border-rose-100">
                                        <th class="py-4 px-4 w-5/12">Nama Barang / Layanan</th>
                                        <th class="py-4 px-4 w-2/12">Jumlah (Qty)</th>
                                        <th class="py-4 px-4 w-3/12">Harga per PC (Rp)</th>
                                        <th class="py-4 px-4 w-2/12">Subtotal (Rp)</th>
                                        <th class="py-4 px-4 text-center w-1/12">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="(item, index) in items" :key="index">
                                        <tr class="hover:bg-rose-50/20">
                                            {{-- Nama Barang --}}
                                            <td class="py-4 px-4">
                                                <input type="text" :name="`items[${index}][name]`" x-model="item.name" placeholder="Contoh: Sampo Creambath 500ml" 
                                                       class="w-full px-4 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                                            </td>

                                            {{-- Qty --}}
                                            <td class="py-4 px-4">
                                                <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" @input="calcSubtotal(item)" min="1" step="1" 
                                                       class="w-full px-4 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472] font-bold text-center">
                                            </td>

                                            {{-- Harga per PC --}}
                                            <td class="py-4 px-4">
                                                <input type="number" :name="`items[${index}][unit_price]`" x-model.number="item.unit_price" @input="calcSubtotal(item)" min="0" step="500" placeholder="45000" 
                                                       class="w-full px-4 py-2 text-xs rounded-xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                                            </td>

                                            {{-- Subtotal --}}
                                            <td class="py-4 px-4 font-bold text-gray-900">
                                                <input type="number" :name="`items[${index}][subtotal]`" x-model.number="item.subtotal" readonly 
                                                       class="w-full px-4 py-2 text-xs rounded-xl bg-gray-50 border-gray-200 font-black text-rose-600">
                                            </td>

                                            {{-- Action Remove --}}
                                            <td class="py-4 px-4 text-center">
                                                <button type="button" @click="removeItemRow(index)" class="p-2 text-rose-400 hover:text-rose-600 transition-colors">
                                                    <i class="fas fa-trash-can text-sm"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        {{-- TOTAL DISPLAY --}}
                        <div class="bg-rose-50/70 rounded-2xl p-4 border border-rose-100 flex items-center justify-between">
                            <div class="text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Total Pengeluaran (Grand Total)
                            </div>
                            <div class="flex items-center gap-4">
                                <input type="hidden" name="amount" :value="grandTotal">
                                <span class="text-2xl font-black text-rose-600 font-headline" x-text="formatRupiah(grandTotal)"></span>
                            </div>
                        </div>
                    </div>

                    {{-- UPLOAD STRUK FOTO & CATATAN --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                        {{-- Foto Bukti Struk --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Upload File Foto Bukti Struk</label>
                            <input type="file" name="receipt_image" id="form_receipt_image" accept="image/*" 
                                   class="w-full px-4 py-3 text-xs rounded-2xl border border-gray-200 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-[#f45472]">
                        </div>

                        {{-- Catatan tambahan --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-2">Catatan Tambahan (Opsional)</label>
                            <input type="text" name="description" placeholder="Catatan syarat, nomor nota, atau nama toko supplier..." 
                                   class="w-full px-4 py-3 text-xs rounded-2xl border-gray-200 focus:border-[#f45472] focus:ring-[#f45472]">
                        </div>
                    </div>

                    {{-- FORM ACTIONS --}}
                    <div class="pt-8 border-t border-gray-100 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.finances.index') }}" class="px-8 py-4 rounded-full bg-gray-100 text-gray-700 font-bold text-xs hover:bg-gray-200 transition-all">
                            Batal
                        </a>
                        <button type="submit" class="px-8 py-4 rounded-full bg-[#f45472] text-white font-bold text-xs hover:bg-[#d93856] shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fas fa-check text-xs"></i>
                            <span>Simpan Pengeluaran</span>
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    {{-- Alpine JS + html5-qrcode Scanner --}}
    <script>
        // --- Alpine Form Data ---
        function expenseForm() {
            return {
                mode: 'manual',
                title: '',
                category: '',
                transactionDate: new Date().toISOString().split('T')[0],
                items: [
                    { name: '', qty: 1, unit_price: 0, subtotal: 0 }
                ],

                get grandTotal() {
                    return this.items.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0);
                },

                switchMode(newMode) {
                    if (this.mode !== 'scan' && newMode === 'scan') {
                        // Stop scanner when leaving scan mode
                    }
                    if (this.mode === 'scan' && newMode !== 'scan') {
                        stopQrScanner();
                    }
                    this.mode = newMode;
                },

                addItemRow() {
                    this.items.push({ name: '', qty: 1, unit_price: 0, subtotal: 0 });
                },

                removeItemRow(index) {
                    if (this.items.length > 1) {
                        this.items.splice(index, 1);
                    } else {
                        this.items[0] = { name: '', qty: 1, unit_price: 0, subtotal: 0 };
                    }
                },

                calcSubtotal(item) {
                    item.subtotal = (parseFloat(item.qty) || 0) * (parseFloat(item.unit_price) || 0);
                },

                formatRupiah(num) {
                    return 'Rp ' + (num || 0).toLocaleString('id-ID');
                },
            }
        }

        // --- html5-qrcode Scanner ---
        let qrScanner = null;

        function startQrScanner() {
            const resultBox = document.getElementById('qr-scan-result');
            const startBtn  = document.getElementById('btn-start-scanner');
            const stopBtn   = document.getElementById('btn-stop-scanner');

            resultBox.classList.add('hidden');
            startBtn.style.display = 'none';
            stopBtn.style.display  = 'flex';

            qrScanner = new Html5Qrcode("qr-scanner-box");

            const config = {
                fps: 10,
                qrbox: { width: 280, height: 200 },
                aspectRatio: 1.5,
                supportedScanTypes: [
                    Html5QrcodeScanType.SCAN_TYPE_CAMERA
                ]
            };

            qrScanner.start(
                { facingMode: "environment" },
                config,
                (decodedText, decodedResult) => {
                    // Success: code scanned
                    document.getElementById('qr-scan-text').textContent = decodedText;
                    resultBox.classList.remove('hidden');

                    // Auto-fill into description/title field if empty
                    const titleInput = document.querySelector('input[name="title"]');
                    if (titleInput && !titleInput.value) {
                        titleInput.value = 'Struk: ' + decodedText;
                    }

                    // Stop scanner after successful scan
                    stopQrScanner();
                },
                (errorMessage) => {
                    // Scanning ongoing - silent
                }
            ).catch((err) => {
                console.error('Camera start error:', err);
                alert('Gagal membuka kamera. Pastikan browser mendapat izin kamera dan coba lagi.\n\nError: ' + err);
                startBtn.style.display = 'flex';
                stopBtn.style.display  = 'none';
            });
        }

        function stopQrScanner() {
            const startBtn = document.getElementById('btn-start-scanner');
            const stopBtn  = document.getElementById('btn-stop-scanner');

            if (qrScanner && qrScanner.isScanning) {
                qrScanner.stop().then(() => {
                    qrScanner.clear();
                    qrScanner = null;
                }).catch(() => {});
            }

            startBtn.style.display = 'flex';
            stopBtn.style.display  = 'none';
        }
    </script>
</x-admin-layout>

