<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    {{-- Ornamen Ambient Background Blobs --}}
    <div class="blob-bg bg-accent-clear w-[500px] h-[500px] -top-24 -left-24 rounded-[40%_60%_70%_30%]" aria-hidden="true"></div>
    <div class="blob-bg bg-primary-fixed-dim w-[600px] h-[600px] top-1/2 -right-36 rounded-[60%_40%_30%_70%] opacity-20" aria-hidden="true"></div>

    {{--
        pt-28 (112px): jarak aman dari navbar fixed (top-4, tinggi ~68px) supaya
        konten tidak ketutup. Sesuaikan lagi kalau tinggi navbar berubah.
    --}}
    <div x-data="dashboardPage()" class="relative z-10 min-h-screen pt-28 pb-24">
        <main class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin-desktop space-y-12">

            {{-- Welcome Section --}}
            <header class="mb-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="font-headline-xl text-3xl md:text-headline-xl text-text-heading mb-2">
                            Hello, <span class="text-primary italic">{{ explode(' ', $user->name)[0] }}</span>
                        </h1>
                        <p class="font-body-lg text-body-lg text-on-surface-variant">
                            Ready for your glow up today? ✨
                        </p>
                    </div>
                    <div class="flex items-center gap-3 bg-surface-light px-4 py-2 rounded-full border border-border-subtle shadow-sm">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-label-md font-label-md text-on-surface-variant">Status Akun: <strong class="text-primary uppercase">{{ $membership['current'] }}</strong></span>
                    </div>
                </div>
            </header>

            {{-- Stats Row --}}
            <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-4 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-label-lg text-label-lg text-on-surface-variant uppercase tracking-wider">Total Bookings</p>
                        <div class="w-8 h-8 rounded-full bg-accent-clear flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-lg">event_available</span>
                        </div>
                    </div>
                    <p class="font-headline-md text-headline-md text-primary">{{ number_format($stats['total_bookings']) }} Bookings</p>
                </div>

                <div class="glass-card rounded-2xl p-4 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-label-lg text-label-lg text-on-surface-variant uppercase tracking-wider">Total Spending</p>
                        <div class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center text-secondary shrink-0">
                            <span class="material-symbols-outlined text-lg">payments</span>
                        </div>
                    </div>
                    <p class="font-headline-md text-headline-md text-primary">Rp {{ number_format($stats['total_spending'], 0, ',', '.') }}</p>
                </div>

                <div class="glass-card rounded-2xl p-4 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-label-lg text-label-lg text-on-surface-variant uppercase tracking-wider">Total Points</p>
                        <div class="w-8 h-8 rounded-full bg-tertiary-fixed flex items-center justify-center text-tertiary shrink-0">
                            <span class="material-symbols-outlined text-lg">military_tech</span>
                        </div>
                    </div>
                    <p class="font-headline-md text-headline-md text-primary flex items-center gap-2">
                        <span x-text="userPoints">{{ number_format($user->total_points) }}</span> pts
                        <span class="material-symbols-outlined text-secondary" style="font-variation-settings: 'FILL' 1;">stars</span>
                    </p>
                </div>
            </section>

            {{-- Membership Status Card --}}
            <section class="glass-card rounded-2xl p-4 md:p-6 relative overflow-hidden bg-surface-light border-t-4 border-t-primary">
                <div class="absolute -right-8 -top-8 text-accent-clear/30 transform rotate-12 pointer-events-none">
                    <span class="material-symbols-outlined text-[120px]" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                </div>
                <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div class="flex-1 w-full">
                        <div class="flex items-center gap-3 mb-1">
                            <h3 class="font-headline-lg text-xl md:text-headline-lg text-text-heading">{{ ucfirst($membership['current']) }} Tier</h3>
                            <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-bold uppercase tracking-widest">Active Status</span>
                        </div>
                        <p class="font-body-md text-body-md text-on-surface-variant mb-4">
                            @if($membership['next'])
                                {{ number_format($membership['points_needed']) }} points to <strong class="text-text-heading">{{ ucfirst($membership['next']) }}</strong>
                            @else
                                You have unlocked the highest VIP Tier! 🎉
                            @endif
                        </p>
                        <div class="w-full bg-surface-container-highest rounded-full h-3 mb-2 overflow-hidden shadow-inner">
                            <div class="bg-primary h-3 rounded-full transition-all duration-1000 shadow-md" style="width: {{ $membership['percent'] }}%"></div>
                        </div>
                        <div class="flex justify-between font-label-md text-label-md text-on-surface-variant">
                            <span>{{ ucfirst($membership['current']) }}</span>
                            <span>{{ $membership['next'] ? ucfirst($membership['next']) : 'Platinum VIP' }}</span>
                        </div>
                    </div>

                    <button @click="showQrModal = true" type="button" class="flex flex-col items-center justify-center bg-primary hover:bg-primary-container text-white w-24 h-24 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 group shrink-0 transform hover:-translate-y-1">
                        <span class="material-symbols-outlined text-2xl mb-1 group-hover:scale-110 transition-transform">qr_code_scanner</span>
                        <span class="font-label-md text-label-md text-center font-semibold text-xs">Scan QR</span>
                    </button>
                </div>
            </section>

            {{-- Main Content Grid: Catalog (70%) & Missions (30%) --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

                {{-- Left Column: Treatment Catalog - Top 3 Rated (70% / 8 Cols) --}}
                <div class="xl:col-span-8 space-y-4">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-headline-md text-headline-md text-text-heading">Treatment Catalog</h2>
                                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold border border-amber-300 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span> Top 3 Rating
                                </span>
                            </div>
                            <p class="text-body-sm text-on-surface-variant mt-1">Layanan favorit pilihan pelanggan Yalia Beauty dengan rating tertinggi.</p>
                        </div>

                        {{-- Search Bar --}}
                        <div class="relative w-full md:w-64">
                            <input aria-label="Cari treatment" x-model="searchQuery" class="w-full pl-10 pr-4 py-2 rounded-full border-border-subtle bg-surface focus:ring-primary focus:border-primary text-body-md font-body-md text-on-surface transition-all" placeholder="Cari treatment..." type="text"/>
                            <span class="material-symbols-outlined absolute left-3 top-2 text-on-surface-variant" aria-hidden="true">search</span>
                        </div>
                    </div>

                    {{-- Filter Chips --}}
                    <div class="flex gap-2 overflow-x-auto pb-2 no-scrollbar">
                        <button type="button" @click="selectedCategory = 'all'" :class="selectedCategory === 'all' ? 'bg-primary text-white' : 'border border-border-subtle bg-surface-light text-on-surface-variant hover:bg-surface-container'" class="px-4 py-2 rounded-full font-label-md text-label-md whitespace-nowrap transition-colors">Semua</button>
                        <button type="button" @click="selectedCategory = 'facial'" :class="selectedCategory === 'facial' ? 'bg-primary text-white' : 'border border-border-subtle bg-surface-light text-on-surface-variant hover:bg-surface-container'" class="px-4 py-2 rounded-full font-label-md text-label-md whitespace-nowrap transition-colors">Facial</button>
                        <button type="button" @click="selectedCategory = 'hair'" :class="selectedCategory === 'hair' ? 'bg-primary text-white' : 'border border-border-subtle bg-surface-light text-on-surface-variant hover:bg-surface-container'" class="px-4 py-2 rounded-full font-label-md text-label-md whitespace-nowrap transition-colors">Hair</button>
                        <button type="button" @click="selectedCategory = 'nails'" :class="selectedCategory === 'nails' ? 'bg-primary text-white' : 'border border-border-subtle bg-surface-light text-on-surface-variant hover:bg-surface-container'" class="px-4 py-2 rounded-full font-label-md text-label-md whitespace-nowrap transition-colors">Nails</button>
                        <button type="button" @click="selectedCategory = 'massage'" :class="selectedCategory === 'massage' ? 'bg-primary text-white' : 'border border-border-subtle bg-surface-light text-on-surface-variant hover:bg-surface-container'" class="px-4 py-2 rounded-full font-label-md text-label-md whitespace-nowrap transition-colors">Massage</button>
                    </div>

                    {{-- Treatment Grid (Only Top 3 Rated) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 min-h-[320px]">
                        @forelse($topTreatments as $treatment)
                            @php
                                $tObj = $treatment;
                                if (is_string($treatment)) {
                                    $tObj = json_decode($treatment) ?: (object)['name' => $treatment];
                                }
                                $tName = is_object($tObj) ? ($tObj->name ?? '') : ($tObj['name'] ?? '');
                                $tDesc = is_object($tObj) ? ($tObj->description ?? '') : ($tObj['description'] ?? '');
                                $tPrice = is_object($tObj) ? ($tObj->price ?? 0) : ($tObj['price'] ?? 0);
                                $tRating = is_object($tObj) ? ($tObj->rating ?? 4.9) : ($tObj['rating'] ?? 4.9);
                                $tDuration = is_object($tObj) ? ($tObj->duration_minutes ?? 60) : ($tObj['duration_minutes'] ?? 60);
                                $tId = is_object($tObj) ? ($tObj->id ?? null) : ($tObj['id'] ?? null);

                                if ($tObj instanceof \App\Models\Treatments) {
                                    $tImage = $tObj->image_url;
                                } else {
                                    $rawImg = is_object($tObj) ? ($tObj->image_url ?? $tObj->images ?? null) : ($tObj['image_url'] ?? $tObj['images'] ?? null);
                                    if ($rawImg && (str_starts_with($rawImg, 'http://') || str_starts_with($rawImg, 'https://'))) {
                                        $tImage = $rawImg;
                                    } elseif ($rawImg) {
                                        $tImage = asset('storage/' . ltrim($rawImg, '/'));
                                    } else {
                                        $tImage = asset('logo/yalia-logos-trnsprnt.svg');
                                    }
                                }

                                $catName = '';
                                if (is_object($tObj) && isset($tObj->category)) {
                                    $catName = is_object($tObj->category) ? ($tObj->category->name ?? '') : ($tObj->category['name'] ?? '');
                                }
                            @endphp

                            <div x-show="matchesFilter('{{ strtolower(addslashes($tName)) }}', '{{ strtolower(addslashes($tDesc)) }}', '{{ strtolower(addslashes($catName)) }}')"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="glass-card rounded-2xl overflow-hidden flex flex-col group hover:shadow-xl transition-all duration-300 border border-border-subtle">

                                <div class="h-36 overflow-hidden relative aspect-video bg-rose-50">
                                    <img alt="{{ $tName }}" width="300" height="144" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $tImage }}"/>
                                    <div class="absolute top-2 left-2 bg-white/90 backdrop-blur-md px-2 py-1 rounded-full text-xs font-bold text-primary shadow-sm flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>Top {{ $loop->iteration }}</span>
                                    </div>
                                </div>
                                <div class="p-4 flex flex-col flex-1">
                                    <div class="flex justify-between items-start mb-2 gap-2">
                                        <h3 class="font-headline-sm text-base text-text-heading line-clamp-1 font-semibold">{{ $tName }}</h3>
                                        <div class="flex items-center gap-1 text-primary shrink-0 bg-primary/10 px-2 py-1 rounded-md">
                                            <span class="material-symbols-outlined text-sm text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                                            <span class="font-label-md text-label-md font-bold">{{ number_format($tRating, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 text-on-surface-variant font-body-sm text-xs mb-2">
                                        <span class="flex items-center gap-1"><span class="material-symbols-outlined text-sm">schedule</span> {{ $tDuration }} min</span>
                                        <span class="flex items-center gap-1 font-semibold text-primary"><span class="material-symbols-outlined text-sm">payments</span> Rp {{ number_format($tPrice, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="font-body-sm text-xs text-on-surface-variant mb-4 line-clamp-2">{{ $tDesc }}</p>
                                    <a href="{{ route('user.bookings.create', ['treatment_id' => $tId]) }}" class="mt-auto w-full py-2 bg-primary hover:bg-primary-container text-white rounded-full font-button text-xs text-center shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 group-hover:scale-[1.02]">
                                        <span>Book Now</span>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                </div>
                            </div>


                        @empty
                            <div class="col-span-full py-12 text-center text-on-surface-variant bg-surface-light rounded-2xl border border-dashed border-border-subtle">
                                <span class="material-symbols-outlined text-4xl mb-2 text-primary/50">spa</span>
                                <p>Belum ada treatment terbaik yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Right Column: Daily Reward — versi ringkas & luxury --}}
                <div class="xl:col-span-4 space-y-4">
                    <div class="glass-card rounded-2xl p-4 bg-gradient-to-b from-surface-light via-surface-container-low to-surface-light border-2 border-primary/20 shadow-xl relative overflow-hidden">

                        {{-- Header --}}
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-headline-sm text-lg font-bold text-text-heading">Misi Harian</h3>
                                    <span class="animate-bounce text-base"></span>
                                </div>
                                <p class="text-xs text-on-surface-variant">Absen harian, raih hadiah glowing!</p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center shadow-lg transform rotate-6 shrink-0">
                                <span class="material-symbols-outlined text-lg">redeem</span>
                            </div>
                        </div>

                        {{-- Streak Status Banner --}}
                        <div class="bg-primary/10 border border-primary/30 rounded-xl p-3 mb-4 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-lg">local_fire_department</span>
                                <span class="text-xs font-bold text-text-heading">Streak Kamu:</span>
                            </div>
                            <span class="text-xs font-extrabold text-primary bg-white px-3 py-1 rounded-full shadow-sm">
                                <span x-text="currentStreak">3</span> / 7 Hari 🔥
                            </span>
                        </div>

                        {{--
                            Inline Flex Stepper — tanpa position absolute, tiap bundaran terhubung
                            garis horizontal (flex-1 h-1) yang tersambung mulus.
                        --}}
                        <div class="flex items-center justify-between mb-4 w-full px-1">
                            <template x-for="(mission, index) in missions" :key="'step-'+index">
                                <div class="flex items-center flex-1 last:flex-none">
                                    {{-- Bundaran Step --}}
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold border-2 bg-surface-container-lowest transition-all shrink-0 shadow-sm"
                                         :class="{
                                             'bg-primary text-on-primary border-primary': mission.status === 'completed',
                                             'bg-surface-container-lowest text-primary border-primary ring-4 ring-primary-fixed-dim/40 animate-pulse': mission.status === 'active',
                                             'bg-surface-container-highest text-on-surface-variant border-transparent': mission.status === 'locked'
                                         }"
                                         :title="mission.dayTitle">
                                        <template x-if="mission.status === 'completed'">
                                            <span class="material-symbols-outlined text-sm">check</span>
                                        </template>
                                        <template x-if="mission.status === 'active'">
                                            <span x-text="index + 1"></span>
                                        </template>
                                        <template x-if="mission.status === 'locked'">
                                            <span class="material-symbols-outlined text-xs opacity-60">lock</span>
                                        </template>
                                    </div>

                                    {{-- Garis Penghubung antar Bundaran --}}
                                    <template x-if="index < missions.length - 1">
                                        <div class="flex-1 h-1 mx-1 rounded-full transition-all duration-300"
                                             :class="index < (currentStreak - 1) ? 'bg-primary' : 'bg-surface-container-highest'">
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{--
                            Kartu "Misi Hari Ini" — versi soft & bersih (bg-surface-container-low)
                            tanpa gradient gelap/kasar.
                        --}}
                        <div class="rounded-2xl py-8 px-4 bg-surface-container-low border border-outline-variant/40 shadow-sm mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-primary">✨ Misi Hari Ini</span>
                                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-primary-fixed text-on-primary-fixed-variant" x-text="activeMission.reward"></span>
                            </div>
                            <h4 class="font-bold text-sm text-text-heading mb-1" x-text="activeMission.dayTitle"></h4>
                            <p class="text-xs text-on-surface-variant italic" x-text="activeMission.funnyText"></p>
                        </div>

                        {{-- Toggle Detail 7 Hari --}}
                        <button @click="showAllMissions = !showAllMissions" type="button" class="w-full flex items-center justify-center gap-1 text-xs font-semibold text-primary mb-8 hover:underline">
                            <span x-text="showAllMissions ? 'Sembunyikan Detail' : 'Lihat Semua Misi (7 Hari)'"></span>
                            <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="showAllMissions ? '-rotate-180' : ''">expand_more</span>
                        </button>

                        {{-- Detail List 7 Hari (collapsible) --}}
                        <div x-show="showAllMissions" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="space-y-2 mb-4 relative">
                            <div class="absolute left-4 top-4 bottom-4 w-1 bg-gradient-to-b from-primary via-primary-fixed-dim to-surface-container-highest z-0 rounded-full"></div>

                            <template x-for="(mission, index) in missions" :key="index">
                                <div class="relative z-10 flex items-center gap-2 transition-all duration-300 transform"
                                     :class="mission.status === 'active' ? 'scale-[1.02]' : ''">

                                    <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-md shrink-0 border-2 transition-all"
                                         :class="{
                                             'bg-primary text-on-primary border-primary': mission.status === 'completed',
                                             'bg-surface-container-lowest text-primary border-primary ring-4 ring-primary-fixed-dim/40 animate-pulse': mission.status === 'active',
                                             'bg-surface-container-highest text-on-surface-variant border-transparent': mission.status === 'locked'
                                         }">
                                        <template x-if="mission.status === 'completed'">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                        </template>
                                        <template x-if="mission.status === 'active'">
                                            <span class="font-bold text-xs" x-text="index + 1"></span>
                                        </template>
                                        <template x-if="mission.status === 'locked'">
                                            <span class="material-symbols-outlined text-sm opacity-60">lock</span>
                                        </template>
                                    </div>

                                    <div class="flex-1 p-2 rounded-xl border transition-all"
                                         :class="{
                                             'bg-surface-container/60 border-primary/10 opacity-75': mission.status === 'completed',
                                             'bg-surface-container-lowest border-2 border-primary shadow-md shadow-primary/10': mission.status === 'active',
                                             'bg-surface-container-low/50 border-transparent opacity-60': mission.status === 'locked'
                                         }">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-xs" :class="mission.status === 'active' ? 'text-primary' : 'text-text-heading'" x-text="mission.dayTitle"></span>
                                            <span class="text-xs font-bold px-2 py-1 rounded-full"
                                                  :class="mission.status === 'active' ? 'bg-primary text-on-primary' : 'bg-primary-fixed text-on-primary-fixed-variant'"
                                                  x-text="mission.reward"></span>
                                        </div>
                                        <p class="text-xs text-on-surface-variant line-clamp-1 italic" x-text="mission.funnyText"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{--
                            Claim CTA — gradient primary-secondary dari tailwind.config.js
                        --}}
                        <button @click="claimTodayReward()"
                                :disabled="hasClaimedToday"
                                type="button"
                                class="relative w-full py-4 rounded-full font-button text-sm shadow-xl transition-all duration-300 flex justify-center items-center gap-2 group transform active:scale-95 disabled:cursor-not-allowed overflow-hidden"
                                :class="hasClaimedToday
                                    ? 'bg-surface-container-highest text-on-surface-variant'
                                    : 'text-on-primary shimmer-btn bg-[linear-gradient(110deg,#b01f44,45%,#d23b5b,55%,#9b4054)] hover:shadow-2xl'">
                            <span class="material-symbols-outlined text-lg" :class="!hasClaimedToday ? 'group-hover:rotate-12 transition-transform' : ''">
                                <span x-text="hasClaimedToday ? 'task_alt' : 'celebration'"></span>
                            </span>
                            <span x-text="hasClaimedToday ? 'Hadiah Hari Ini Sudah Diklaim' : 'Klaim Hadiah Hari Ini!'"></span>
                        </button>
                    </div>
                </div>

            </div>

            {{-- Booking Management Section --}}
            <section class="mt-12 pt-8 border-t border-border-subtle">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <h2 class="font-headline-md text-2xl font-semibold text-text-heading">Riwayat & Booking Saya</h2>
                        <p class="text-body-sm text-on-surface-variant">Kelola janji temu dan periksa status jadwal perawatan Anda.</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex gap-4 overflow-x-auto no-scrollbar border-b border-border-subtle pb-2">
                            <template x-for="t in tabs" :key="t">
                                <button
                                    @click="switchTab(t)"
                                    class="pb-2 text-label-lg font-label-lg whitespace-nowrap transition-colors font-semibold"
                                    :class="activeTab === t ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary'"
                                    x-text="t.charAt(0).toUpperCase() + t.slice(1)">
                                </button>
                            </template>
                        </div>

                        <button
                            type="button"
                            @click="toggleSort()"
                            class="flex items-center gap-1 px-3 py-2 rounded-full border border-border-subtle bg-surface text-xs font-semibold text-on-surface-variant hover:text-primary transition-colors"
                        >
                            <span class="material-symbols-outlined text-sm">filter_list</span>
                            <span x-text="sort === 'asc' ? 'Urutkan: Lama' : 'Urutkan: Baru'"></span>
                        </button>
                    </div>
                </div>

                <div id="booking-list-container" aria-live="polite" class="relative">
                    <div x-show="loading" class="space-y-4 py-2">
                        <x-skeleton.card />
                        <x-skeleton.card />
                    </div>

                    <div x-show="!loading">
                        @include('user.bookings.BookingList', ['bookings' => $upcomingBookings, 'tab' => 'upcoming', 'paginated' => false])
                    </div>
                </div>

            </section>

        </main>

        {{-- Member QR Modal --}}
        <div x-show="showQrModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div @click.away="showQrModal = false" class="bg-surface-light rounded-2xl max-w-md w-full p-6 shadow-2xl border border-border-subtle relative text-center space-y-4">
                <button type="button" @click="showQrModal = false" aria-label="Tutup modal QR Code" class="absolute top-4 right-4 text-on-surface-variant hover:text-primary transition-colors">
                    <span class="material-symbols-outlined text-xl" aria-hidden="true">close</span>
                </button>

                <div class="inline-flex p-3 rounded-full bg-primary/10 text-primary">
                    <span class="material-symbols-outlined text-2xl" aria-hidden="true">qr_code_2</span>
                </div>

                <div>
                    <h3 class="font-headline-sm text-xl font-bold text-text-heading mb-1">Kode QR Member Yalia</h3>
                    <p class="text-xs text-on-surface-variant">Tunjukkan QR code ini ke kasir salon untuk klaim poin & benefit member.</p>
                </div>

                {{-- QR Code Image Box --}}
                <div class="p-4 bg-white rounded-2xl shadow-inner border border-primary/20 inline-block relative group">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode('YB-MEMBER-' . $user->id) }}"
                         alt="Member QR Code"
                         width="160"
                         height="160"
                         loading="lazy"
                         decoding="async"
                         class="w-40 h-40 mx-auto object-contain">
                    <div class="mt-2 text-xs font-mono font-bold text-primary tracking-widest uppercase">
                        YB-{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                </div>

                <div class="bg-surface-container p-4 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <span class="text-on-surface-variant block">Nama Member:</span>
                        <strong class="text-text-heading text-sm">{{ $user->name }}</strong>
                    </div>
                    <span class="px-3 py-1 bg-primary text-white rounded-full font-bold uppercase text-xs tracking-wider">
                        {{ $membership['current'] }}
                    </span>
                </div>

                <button @click="copyMemberId()" type="button" class="w-full py-3 bg-surface-container-high hover:bg-surface-container-highest text-primary font-button text-sm rounded-full transition-colors flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">content_copy</span>
                    <span x-text="copied ? '✅ Berhasil Disalin!' : 'Salin ID Member'"></span>
                </button>
            </div>
        </div>

        {{-- Funny Reward Claim Modal Popup --}}
        <div x-show="showRewardModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-md">
            <div class="bg-surface-light rounded-[32px] max-w-sm w-full p-6 shadow-2xl border-4 border-primary text-center space-y-4 relative overflow-hidden">
                <div class="absolute -top-8 -right-8 w-32 h-32 bg-primary/20 rounded-full blur-xl pointer-events-none"></div>

                <div class="text-5xl animate-bounce">
                    <span x-text="rewardModalData.icon">🎉</span>
                </div>

                <div>
                    <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-extrabold tracking-widest uppercase">
                        AURA GLOW-UP MEMANCAR! ✨
                    </span>
                    <h3 class="font-headline-sm text-xl font-extrabold text-primary mt-2" x-text="rewardModalData.title"></h3>
                </div>

                <div class="p-4 bg-surface-container/70 rounded-2xl border border-primary/20 text-xs text-on-surface-variant leading-relaxed font-body-md">
                    <p x-text="rewardModalData.message"></p>
                </div>

                <div class="bg-primary/10 p-3 rounded-xl border border-primary/30 flex items-center justify-center gap-2 text-primary font-bold text-sm">
                    <span class="material-symbols-outlined">stars</span>
                    <span>HADIAH: <span x-text="rewardModalData.reward"></span></span>
                </div>

                <button @click="closeRewardModal()" type="button" class="w-full py-4 bg-primary hover:bg-primary-container text-white font-bold text-sm rounded-full shadow-lg transition-all transform hover:scale-105">
                    SIAP GLOW-UP BANGET! 🚀
                </button>
            </div>
        </div>

    </div>

    @include('layouts.footer')

    @push('styles')
    <style>
        [x-cloak] { display: none !important; }

        /* Efek slash/shimmer pada tombol dengan easing curve lambat di awal */
        @keyframes slash-sweep {
            0% {
                transform: translateX(-150%) skewX(-25deg);
            }
            100% {
                transform: translateX(250%) skewX(-25deg);
            }
        }
        .shimmer-btn {
            position: relative;
            overflow: hidden;
        }
        .shimmer-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 60%;
            height: 200%;
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(255, 255, 255, 0.08) 20%,
                rgba(255, 255, 255, 0.75) 50%,
                rgba(255, 255, 255, 0.08) 80%,
                transparent 100%
            );
            animation: slash-sweep 3.2s cubic-bezier(0.7, 0, 0.25, 1) infinite;
            pointer-events: none;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboardPage', () => ({
                userPoints: {{ $user->total_points }},
                showQrModal: false,
                showRewardModal: false,
                showAllMissions: false,
                copied: false,
                searchQuery: '',
                selectedCategory: 'all',

                // Dynamic Day of Week Calculation (Monday = 1 ... Friday = 5 ... Sunday = 7)
                todayDayOfWeek: (new Date().getDay() === 0 ? 7 : new Date().getDay()),
                currentStreak: 4,
                hasClaimedToday: @json((bool) ($user->last_daily_checkin_at && $user->last_daily_checkin_at->isToday())),

                tabs: ['upcoming', 'past', 'cancelled'],
                activeTab: 'upcoming',
                sort: 'desc',
                loading: false,

                rewardModalData: {
                    icon: '🌟',
                    title: 'Glow Up Striking Back!',
                    reward: '+25 Points',
                    message: 'Selamat Sis! Aura cantikmu resmi naik 1000 Watt hari ini. Kenangan kusam ter-exfoliate sempurna!'
                },

                missions: [
                    { dayNum: 1, dayTitle: 'Day 1: Muka Bantal Eradication', reward: '+5 pts', funnyText: 'Membasmi jejak bantal di pipi saat bangun jam 11 siang.', status: 'completed' },
                    { dayNum: 2, dayTitle: 'Day 2: Bebas Kusam Anti-Galau', reward: '+10 pts', funnyText: 'Eksfoliasi pikiran negatif & mantan yang bikin kusam.', status: 'completed' },
                    { dayNum: 3, dayTitle: 'Day 3: Glow Up Striking Back', reward: '+15 pts', funnyText: 'Aura cantik memancar 1000 watt. Siap bikin pangling!', status: 'completed' },
                    { dayNum: 4, dayTitle: 'Day 4: Keindahan Hakiki Level 4', reward: '+20 pts', funnyText: 'Kuku manja & kulit selembut sutra menanti.', status: 'completed' },
                    { dayNum: 5, dayTitle: 'Day 5: Ratu Skincare Masuk Vibe', reward: '+25 pts', funnyText: 'Darah sultan mengalir, siap perawatan ala putri.', status: 'active' },
                    { dayNum: 6, dayTitle: 'Day 6: Gratis Ongkir Auto-Sultan', reward: 'Gratis Ongkir', funnyText: 'Penghargaan khusus untuk ratu yang malas bayar ongkir!', status: 'locked' },
                    { dayNum: 7, dayTitle: 'Day 7: Mahkota Glowing 5% Off', reward: 'Diskon 5% + 100 Pts', funnyText: 'Puncak komedi & kecantikan! Diskon Ratu Beauty milikmu!', status: 'locked' }
                ],

                get activeMission() {
                    const activeIndex = this.todayDayOfWeek - 1;
                    return this.missions[activeIndex] || this.missions.find(m => m.status === 'active') || this.missions[0];
                },

                init() {
                    const todayStr = new Date().toISOString().split('T')[0];
                    const lastClaimDate = localStorage.getItem('yalia_last_claimed_date');
                    if (lastClaimDate === todayStr) {
                        this.hasClaimedToday = true;
                    }

                    const currentDayIdx = this.todayDayOfWeek - 1; // 0..6 (Friday = 4 -> Day 5)

                    // Dynamically set status for all days based on current day of week (Friday = Day 5)
                    this.missions.forEach((m, idx) => {
                        if (idx < currentDayIdx) {
                            m.status = 'completed';
                        } else if (idx === currentDayIdx) {
                            m.status = this.hasClaimedToday ? 'completed' : 'active';
                        } else {
                            m.status = 'locked';
                        }
                    });

                    this.currentStreak = this.hasClaimedToday ? this.todayDayOfWeek : Math.max(1, this.todayDayOfWeek - 1);
                },

                matchesFilter(name, desc, category) {
                    const q = this.searchQuery.toLowerCase();
                    const matchesSearch = !q || name.includes(q) || desc.includes(q);
                    const matchesCat = this.selectedCategory === 'all' || category.includes(this.selectedCategory);
                    return matchesSearch && matchesCat;
                },

                async claimTodayReward() {
                    if (this.hasClaimedToday) return;

                    const todayStr = new Date().toISOString().split('T')[0];
                    const currentDayIdx = this.todayDayOfWeek - 1;
                    const activeM = this.missions[currentDayIdx];

                    this.hasClaimedToday = true;
                    this.currentStreak = this.todayDayOfWeek;

                    if (activeM) {
                        activeM.status = 'completed';
                    }

                    localStorage.setItem('yalia_last_claimed_date', todayStr);

                    try {
                        const response = await fetch(`{{ route('user.daily-checkin') }}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                        });

                        const data = await response.json();
                        if (data.total_points !== undefined) {
                            this.userPoints = data.total_points;
                        } else {
                            this.userPoints += 25;
                        }
                    } catch (e) {
                        this.userPoints += 25;
                    }

                    if (typeof confetti === 'function') {
                        confetti({
                            particleCount: 120,
                            spread: 80,
                            origin: { y: 0.6 }
                        });
                    }

                    this.rewardModalData = {
                        icon: '✨',
                        title: `${activeM ? activeM.dayTitle : 'Hadiah Hari Ini'}!`,
                        reward: activeM ? activeM.reward : '+25 Points',
                        message: `Selamat Ratu Beauty! Kamu berhasil mengklaim misi hari ini. Bonus poin PTS telah disimpan ke akunmu!`
                    };

                    this.showRewardModal = true;
                },

                closeRewardModal() {
                    this.showRewardModal = false;
                },

                copyMemberId() {
                    navigator.clipboard.writeText('YB-{{ str_pad($user->id, 6, "0", STR_PAD_LEFT) }}');
                    this.copied = true;
                    setTimeout(() => { this.copied = false; }, 2000);
                },

                async switchTab(tab) {
                    if (this.loading || this.activeTab === tab) return;
                    this.activeTab = tab;
                    await this.fetchList();
                },

                async toggleSort() {
                    this.sort = this.sort === 'asc' ? 'desc' : 'asc';
                    await this.fetchList();
                },

                async fetchList() {
                    if (this.loading) return;
                    this.loading = true;
                    const startTime = Date.now();

                    try {
                        const response = await fetch(`{{ route('user.bookings.list') }}?tab=${this.activeTab}&sort=${this.sort}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        });

                        if (!response.ok) throw new Error('Gagal memuat data');

                        const htmlContent = await response.text();
                        const elapsed = Date.now() - startTime;
                        if (elapsed < 450) {
                            await new Promise(r => setTimeout(r, 450 - elapsed));
                        }

                        document.getElementById('booking-list-container').innerHTML = htmlContent;
                    } catch (e) {
                        document.getElementById('booking-list-container').innerHTML =
                            '<p class="text-center py-12 text-on-surface-variant">Gagal memuat data. Coba lagi.</p>';
                    } finally {
                        this.loading = false;
                    }
                }

            }));
        });
    </script>
    @endpush
</x-app-layout>