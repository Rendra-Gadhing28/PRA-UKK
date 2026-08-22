<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Vouchers & Poin PTS
        </h2>
    </x-slot>

    {{-- Ornamen Ambient Background Blobs --}}
    <div class="blob-bg bg-accent-clear w-[500px] h-[500px] -top-24 -left-24 rounded-[40%_60%_70%_30%]" aria-hidden="true"></div>
    <div class="blob-bg bg-primary-fixed-dim w-[600px] h-[600px] top-1/2 -right-36 rounded-[60%_40%_30%_70%] opacity-20" aria-hidden="true"></div>

    {{--
        mt-36 (144px): Jarak aman kelipatan 4 agar tidak tabrakan dengan fixed navigation bar
    --}}
    <div x-data="{ 
        activeTab: 'all', 
        loading: false,
        switchTab(tab) {
            if (this.activeTab === tab) return;
            this.loading = true;
            this.activeTab = tab;
            setTimeout(() => { this.loading = false; }, 400);
        }
    }" class="relative z-10 min-h-screen mt-36 pb-24">
        <main class="max-w-[1280px] mx-auto px-4 sm:px-8 space-y-8">

            {{-- HEADER TITLE & REWARD PTS BANNER --}}
            <section class="bg-gradient-to-r from-[#f45472] via-[#e64262] to-[#d93856] rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute right-0 top-0 bottom-0 w-1/3 opacity-10 pointer-events-none flex items-center justify-end pr-8">
                    <i class="fas fa-ticket-alt text-[200px] text-white"></i>
                </div>

                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                    <div class="space-y-4 max-w-xl">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold border border-white/30">
                            <i class="fas fa-crown text-amber-300"></i>
                            <span>Yalia Beauty Loyalty & Promo Rewards</span>
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight font-headline leading-tight">
                            Klaim Voucher & Tukar Poin PTS Kamu ✨
                        </h1>
                        <p class="text-sm text-rose-100 font-medium leading-relaxed">
                            Nikmati berbagai potongan harga eksklusif, voucher event special, dan tukarkan poin PTS dari setiap booking perawatanmu!
                        </p>
                    </div>

                    {{-- USER PTS BALANCE CARD --}}
                    <div class="bg-white/15 backdrop-blur-lg p-6 rounded-2xl border border-white/30 flex items-center gap-6 shrink-0 shadow-inner">
                        <div class="w-16 h-16 rounded-2xl bg-amber-400 text-amber-950 flex items-center justify-center text-3xl shadow-lg shrink-0">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <span class="text-xs uppercase tracking-wider text-rose-100 font-bold block mb-1">
                                Saldo Poin PTS Anda
                            </span>
                            <div class="flex items-baseline gap-2">
                                <span class="text-4xl font-black font-mono tracking-tight text-white">
                                    {{ number_format($user->total_points ?? 0) }}
                                </span>
                                <span class="text-sm font-bold text-amber-300 uppercase tracking-widest">PTS</span>
                            </div>
                            <p class="text-[11px] text-rose-100 mt-1">
                                Level Membership: <strong class="uppercase text-white font-bold">{{ $user->membership_level ?? 'Regular' }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </section>


            {{-- NAVIGATION TABS & SEARCH BAR --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-rose-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                {{-- Tabs Button List --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 scrollbar-none">
                    <button type="button" @click="switchTab('all')" 
                            aria-label="Tampilkan semua promo voucher"
                            :class="activeTab === 'all' ? 'bg-[#f45472] text-white shadow-md' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
                            class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-2">
                        <i class="fas fa-ticket text-xs" aria-hidden="true"></i>
                        <span>Semua Promo ({{ $allVouchers->count() }})</span>
                    </button>

                    <button type="button" @click="switchTab('pts')" 
                            aria-label="Tampilkan voucher tukar poin PTS"
                            :class="activeTab === 'pts' ? 'bg-[#f45472] text-white shadow-md' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
                            class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-2">
                        <i class="fas fa-coins text-amber-500 text-xs" aria-hidden="true"></i>
                        <span>Tukar Poin PTS ({{ $pointVouchers->count() }})</span>
                    </button>

                    <button type="button" @click="switchTab('event')" 
                            aria-label="Tampilkan voucher event spesial"
                            :class="activeTab === 'event' ? 'bg-[#f45472] text-white shadow-md' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
                            class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-2">
                        <i class="fas fa-gift text-rose-500 text-xs" aria-hidden="true"></i>
                        <span>Event Special ({{ $eventVouchers->count() }})</span>
                    </button>

                    <button type="button" @click="switchTab('my_vouchers')" 
                            aria-label="Tampilkan voucher milik saya"
                            :class="activeTab === 'my_vouchers' ? 'bg-[#f45472] text-white shadow-md' : 'bg-rose-50 text-rose-700 hover:bg-rose-100'"
                            class="px-5 py-2.5 rounded-2xl text-xs font-bold transition-all shrink-0 flex items-center gap-2">
                        <i class="fas fa-user-check text-xs" aria-hidden="true"></i>
                        <span>Voucher Saya ({{ $myVouchers->count() }})</span>
                    </button>
                </div>

                {{-- Quick Filter Info --}}
                <div class="text-xs text-gray-500 font-medium flex items-center gap-2 shrink-0">
                    <i class="fas fa-circle-info text-rose-400"></i>
                    <span>Klik klaim / tukar poin untuk menyimpan voucher ke akun Anda.</span>
                </div>
            </div>

            {{-- Skeleton Loading Grid --}}
            <div x-show="loading" class="grid grid-cols-1 md:grid-cols-2 gap-8 py-4">
                <x-skeleton.card />
                <x-skeleton.card />
            </div>

            {{-- 1. TAB: SEMUA PROMO --}}
            <div x-show="!loading && activeTab === 'all'" class="space-y-4">

                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 font-headline">
                        <span class="w-2.5 h-6 bg-[#f45472] rounded-full inline-block"></span>
                        Semua Voucher Promo Tersedia
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($allVouchers as $v)
                        @include('user.vouchers.partials.voucher-card', ['v' => $v, 'claimedVoucherIds' => $claimedVoucherIds, 'user' => $user])
                    @empty
                        <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-rose-100">
                            <i class="fas fa-ticket-alt text-4xl text-rose-300 mb-4 block"></i>
                            <h4 class="font-bold text-gray-800 text-base mb-1">Belum Ada Voucher Promo</h4>
                            <p class="text-xs text-gray-500">Saat ini belum ada voucher promo aktif. Cek kembali nanti!</p>
                        </div>
                    @endforelse
                </div>
            </div>


            {{-- 2. TAB: TUKAR POIN PTS --}}
            <div x-show="!loading && activeTab === 'pts'" class="space-y-4">

                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-400 text-amber-950 flex items-center justify-center text-lg font-bold shrink-0">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-amber-950 uppercase tracking-wider">Tukarkan Poin PTS Kamu dengan Voucher Belanja</h4>
                            <p class="text-xs text-amber-800">Saldo poin saat ini: <strong class="font-bold text-amber-950 font-mono">{{ number_format($user->total_points ?? 0) }} PTS</strong></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($pointVouchers as $v)
                        @include('user.vouchers.partials.voucher-card', ['v' => $v, 'claimedVoucherIds' => $claimedVoucherIds, 'user' => $user])
                    @empty
                        <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-rose-100">
                            <i class="fas fa-coins text-4xl text-amber-300 mb-4 block"></i>
                            <h4 class="font-bold text-gray-800 text-base mb-1">Belum Ada Voucher Tukar Poin</h4>
                            <p class="text-xs text-gray-500">Voucher penukaran poin PTS akan segera tersedia.</p>
                        </div>
                    @endforelse
                </div>
            </div>


            {{-- 3. TAB: EVENT SPECIAL --}}
            <div x-show="!loading && activeTab === 'event'" class="space-y-4">
                <div class="bg-gradient-to-r from-purple-500 to-rose-500 text-white rounded-2xl p-4 flex items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center text-lg font-bold shrink-0">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider">Promo Event Special Yalia Beauty</h4>
                            <p class="text-xs text-purple-100">Klaim voucher khusus selama event berlangsung sebelum kuota habis!</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($eventVouchers as $v)
                        @include('user.vouchers.partials.voucher-card', ['v' => $v, 'claimedVoucherIds' => $claimedVoucherIds, 'user' => $user])
                    @empty
                        <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-rose-100">
                            <i class="fas fa-calendar-star text-4xl text-rose-300 mb-4 block"></i>
                            <h4 class="font-bold text-gray-800 text-base mb-1">Tidak Ada Event Voucher Berlangsung</h4>
                            <p class="text-xs text-gray-500">Nantikan promo event seru berikutnya dari Yalia Beauty Salon.</p>
                        </div>
                    @endforelse
                </div>
            </div>


            {{-- 4. TAB: VOUCHER SAYA (CLAIMED) --}}
            <div x-show="!loading && activeTab === 'my_vouchers'" class="space-y-4">



                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 font-headline">
                        <span class="w-2.5 h-6 bg-emerald-500 rounded-full inline-block"></span>
                        Voucher yang Sudah Anda Klaim
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($myVouchers as $uv)
                        @if($uv->voucher)
                            @include('user.vouchers.partials.user-claimed-card', ['uv' => $uv, 'v' => $uv->voucher])
                        @endif
                    @empty
                        <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-rose-100 space-y-4">
                            <div class="w-16 h-16 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto text-2xl">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-base mb-1">Belum Ada Voucher yang Diklaim</h4>
                                <p class="text-xs text-gray-500 max-w-sm mx-auto">Tukarkan poin PTS atau klaim voucher promo tersedia untuk menggunakannya saat melakukan booking perawatan.</p>
                            </div>
                            <button @click="activeTab = 'all'" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#f45472] text-white text-xs font-bold hover:bg-[#d93856] shadow-sm transition-all">
                                <i class="fas fa-magnifying-glass"></i> Jelajahi Voucher Promo
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

        </main>
    </div>
</x-app-layout>
