<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>


    <div x-data="dashboardPage()" class="relative z-10 min-h-screen pt-28 pb-24 text-[#2B0F23]">
        <main class="max-w-[1280px] mx-auto px-margin-mobile md:px-margin-desktop space-y-12">

            {{-- Top Header Section: Left = Welcome & Stats, Right = Compact VIP Member Card --}}
            @php
                $currentTier = $membership['current_meta'] ?? [
                    'name' => ucfirst($membership['current']),
                    'label' => ucfirst($membership['current']) . ' Member',
                    'discount' => '0%',
                    'bg_gradient' => 'from-[#2B0F23] via-[#5C1439] to-[#7A1F52]',
                    'border' => 'border-[#F4B942]/30',
                    'gem_gradient' => 'from-[#FDE68A] via-[#F4B942] to-[#E0247E]',
                    'gem_glow' => 'rgba(224, 36, 126, 0.55)',
                ];

                $allTiers = $membership['all_tiers'] ?? \App\Support\Membership::TIERS;
            @endphp

            <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch mb-8">

                {{-- Left Column (7 Cols): Welcome Header & 3 Stats Cards --}}
                <div class="lg:col-span-7 flex flex-col justify-between space-y-6">

                    {{-- Greeting & Account Status --}}
                    <div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h1 class="font-headline-xl text-3xl md:text-4xl font-black text-[#2B0F23] mb-1">
                                    Hello, <span class="text-[#E0247E] italic">{{ explode(' ', $user->name)[0] }}</span> 
                                </h1>
                                <p class="font-body-md text-[#5C1439]/80 font-medium">
                                    Ready for your glow up today? Jelajahi treatment & klaim reward harianmu!
                                </p>
                            </div>

                            <div class="flex items-center gap-2 bg-[#FFF6FA] px-4 py-2 rounded-full border border-[#E0247E]/20 shadow-[0_4px_15px_rgba(224,36,126,0.08)] shrink-0 self-start sm:self-auto">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-xs font-bold text-[#5C1439]">Status Akun: <strong class="text-[#E0247E] uppercase font-black">{{ $membership['current'] }}</strong></span>
                            </div>
                        </div>
                    </div>

                    {{-- Compact 3 Stats Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                        <div class="rounded-2xl p-4 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg backdrop-blur-xl shadow-sm" style="background: rgba(255, 240, 245, 0.85); border: 1px solid rgba(224, 36, 126, 0.15);">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-extrabold uppercase tracking-wider" style="color: #9B4054;">Bookings</p>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background: rgba(224, 36, 126, 0.12); color: #E0247E;">
                                    <i class="fa-solid fa-calendar-check text-base"></i>
                                </div>
                            </div>
                            <p class="font-headline-md text-xl font-black" style="color: #2B0F23;">{{ number_format($stats['total_bookings']) }} Bookings</p>
                        </div>

                        <div class="rounded-2xl p-4 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg backdrop-blur-xl shadow-sm" style="background: rgba(255, 240, 245, 0.85); border: 1px solid rgba(224, 36, 126, 0.15);">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-extrabold uppercase tracking-wider" style="color: #9B4054;">Spending</p>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background: rgba(92, 20, 57, 0.12); color: #5C1439;">
                                    <i class="fa-solid fa-wallet text-base"></i>
                                </div>
                            </div>
                            <p class="font-headline-md text-xl font-black truncate" style="color: #2B0F23;">Rp {{ number_format($stats['total_spending'], 0, ',', '.') }}</p>
                        </div>

                        <div class="rounded-2xl p-4 flex flex-col justify-center transition-all duration-300 hover:-translate-y-1 hover:shadow-lg backdrop-blur-xl shadow-sm" style="background: rgba(255, 240, 245, 0.85); border: 1px solid rgba(224, 36, 126, 0.15);">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-xs font-extrabold uppercase tracking-wider" style="color: #9B4054;">Total Points</p>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0" style="background: rgba(244, 185, 66, 0.25); color: #9A6200;">
                                    <i class="fa-solid fa-award text-base"></i>
                                </div>
                            </div>
                            <p class="font-headline-md text-xl font-black flex items-center gap-1.5" style="color: #E0247E;">
                                <span x-text="userPoints">{{ number_format($user->total_points) }}</span> pts
                                <span class="material-symbols-outlined text-base" style="color: #F4B942; font-variation-settings: 'FILL' 1;">stars</span>
                            </p>
                        </div>
                    </div>

                </div>

                {{-- Right Column (5 Cols): Compact VIP Member Card (Positioned Right, Credit Card Format) --}}
                <div class="lg:col-span-5 flex justify-center lg:justify-end items-center">
                    <div class="relative w-full max-w-[440px] group transition-all duration-500">

                        {{-- Warm Neon Glow Backdrop (pink-plum-gold, bukan purple/indigo) --}}
                        <div class="absolute -inset-1 rounded-[28px] bg-gradient-to-r from-[#E0247E]/40 via-[#F4B942]/25 to-[#5C1439]/40 blur-lg opacity-80 group-hover:opacity-100 transition duration-500"></div>

                        {{-- Physical VIP Member Card (Aspect Ratio ~ 1.6 : 1) --}}
                        <div class="relative rounded-[24px] overflow-hidden p-5 md:p-6 text-white border shadow-2xl backdrop-blur-2xl flex flex-col justify-between min-h-[230px]" style="{{ $currentTier['style_bg'] ?? 'background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);' }} {{ $currentTier['border_style'] ?? 'border-color: #334155;' }}">

                            {{-- Subtle Luxury Shimmer Overlay --}}
                            <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/5 to-transparent opacity-60 pointer-events-none"></div>

                            {{-- 3D Faceted Gem Cutout at Top Right --}}
                            <div class="absolute top-3 right-3 sm:right-4 z-20">
                                <div class="relative flex items-center justify-center">
                                    <div class="absolute w-12 h-12 rounded-full blur-md" style="background: {{ $currentTier['gem_glow'] ?? 'rgba(224,36,126,0.55)' }};"></div>
                                    <div class="relative w-12 h-12 drop-shadow-[0_8px_16px_rgba(0,0,0,0.6)] transform group-hover:rotate-12 group-hover:scale-110 transition-all duration-300">
                                        <svg viewBox="0 0 100 100" class="w-full h-full filter drop-shadow-md">
                                            <defs>
                                                <linearGradient id="gemCardGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                                    <stop offset="0%" stop-color="#FDE68A" />
                                                    <stop offset="40%" stop-color="#F4B942" />
                                                    <stop offset="80%" stop-color="#E0247E" />
                                                    <stop offset="100%" stop-color="#7A1F52" />
                                                </linearGradient>
                                                <linearGradient id="gemCardHighlight" x1="0%" y1="0%" x2="100%" y2="0%">
                                                    <stop offset="0%" stop-color="#ffffff" stop-opacity="0.9" />
                                                    <stop offset="100%" stop-color="#FDE2ED" stop-opacity="0.25" />
                                                </linearGradient>
                                            </defs>
                                            <polygon points="50,5 85,25 50,95" fill="url(#gemCardGrad)" />
                                            <polygon points="50,5 15,25 50,95" fill="url(#gemCardGrad)" opacity="0.85" />
                                            <polygon points="15,25 50,25 50,95" fill="url(#gemCardHighlight)" />
                                            <polygon points="50,5 15,25 50,25" fill="#ffffff" opacity="0.7" />
                                            <polygon points="50,5 85,25 50,25" fill="#ffffff" opacity="0.9" />
                                            <polygon points="50,25 85,25 50,95" fill="url(#gemCardHighlight)" opacity="0.5" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Header: Brand & Discount Pill (No Collision Layout) --}}
                            <div class="flex items-center justify-between pr-14 mb-3 relative z-10">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-widest bg-white/15 text-white/95 px-2.5 py-1 rounded-full border border-white/25 backdrop-blur-md shadow-sm">
                                        YALIA BEAUTY VIP
                                    </span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] sm:text-xs font-black uppercase tracking-wider bg-gradient-to-r from-[#F4B942] to-[#E0247E] text-white border border-white/25 shadow-md flex items-center gap-1">
                                        <i class="fa-solid fa-percent text-[10px]"></i>
                                        <span>DISKON {{ $currentTier['discount'] ?? '0%' }}</span>
                                    </span>
                                </div>
                            </div>

                            {{-- Card Center: Tier Title & Member Details --}}
                            <div class="my-2 relative z-10">
                                <h3 class="font-serif text-xl md:text-2xl font-extrabold tracking-wide text-white uppercase line-clamp-1 drop-shadow-md">
                                    {{ $currentTier['label'] ?? ucfirst($membership['current']) . ' Member' }}
                                </h3>
                                <div class="flex justify-between items-center text-xs text-white/80 font-semibold mt-1">
                                    <span class="truncate max-w-[180px] font-medium">{{ $user->name }}</span>
                                    <span class="font-mono text-xs text-[#FDE2ED] tracking-wider font-bold bg-black/20 px-2 py-0.5 rounded border border-white/10">YB-{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>

                            {{-- Card Bottom: Progress & Actions --}}
                            <div class="space-y-3 pt-2.5 border-t border-white/15 relative z-10">
                                <div>
                                    <div class="flex justify-between items-center text-xs font-bold text-white/80 mb-1">
                                        <span>Progress: {{ number_format($user->tier_points) }} Pts</span>
                                        <span class="text-[#F4B942] font-black text-xs drop-shadow-sm">{{ $membership['percent'] }}%</span>
                                    </div>
                                    <div class="w-full bg-black/50 rounded-full h-2.5 overflow-hidden border border-white/20 p-[1px]">
                                        <div class="h-full rounded-full bg-amber-400 bg-gradient-to-r from-amber-400 via-pink-500 to-rose-500 shadow-[0_0_10px_rgba(244,185,66,0.8)] transition-all duration-500"
                                             style="width: {{ max(3, (int) $membership['percent']) }}%; background: linear-gradient(90deg, #F4B942 0%, #E0247E 50%, #FF6FB5 100%);">
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons Row --}}
                                <div class="flex gap-2 pt-0.5">
                                    <button @click="showQrModal = true" type="button" class="flex-1 flex items-center justify-center gap-1.5 bg-white/15 hover:bg-white/25 text-white py-2 px-3 rounded-xl border border-white/25 text-xs font-bold transition-all shadow-sm">
                                        <span class="material-symbols-outlined text-sm">qr_code_scanner</span>
                                        <span>QR Member</span>
                                    </button>
                                    <button @click="showTiersModal = true" type="button" class="flex-1 flex items-center justify-center gap-1.5 bg-gradient-to-r from-[#E0247E] via-[#b01f44] to-[#7A1F52] hover:brightness-110 text-white py-2 px-3 rounded-xl shadow-md border border-[#F4B942]/50 text-xs font-bold transition-all">
                                        <span class="material-symbols-outlined text-sm text-amber-300">workspace_premium</span>
                                        <span>4 Tier & Diskon</span>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </section>

            {{-- Main Content Grid: Catalog (70%) & Missions (30%) --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8 items-start">

                {{-- Left Column: Treatment Catalog - Top 3 Rated (70% / 8 Cols) --}}
                <div class="xl:col-span-8 space-y-4 flex flex-col h-fit">
                    <div>
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-headline-md text-2xl md:text-3xl text-[#2B0F23] font-black">Treatment Catalog</h2>
                                    <span class="px-3 py-1 bg-[#FFF0F2] text-[#B01F44] rounded-full text-xs font-extrabold border border-[#F4DDE1] flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined text-sm text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span> Top 3 Rating
                                    </span>
                                </div>
                                <p class="text-xs md:text-sm text-[#594043] mt-1 font-medium">Layanan favorit pilihan pelanggan Yalia Beauty dengan rating tertinggi.</p>
                            </div>

                            {{-- Search Bar --}}
                            <div class="relative w-full md:w-64">
                                <input aria-label="Cari treatment" x-model="searchQuery" class="w-full pl-10 pr-4 py-2.5 rounded-full border border-[#F4DDE1] bg-white focus:ring-[#B01F44] focus:border-[#B01F44] text-xs font-medium text-[#2B0F23] placeholder-[#8D7072] shadow-sm transition-all" placeholder="Cari treatment..." type="text"/>
                                <span class="material-symbols-outlined absolute left-3.5 top-3 text-[#B01F44] text-lg" aria-hidden="true">search</span>
                            </div>
                        </div>

                        {{-- Filter Chips --}}
                        <div class="flex gap-2 overflow-x-auto pb-3 no-scrollbar">
                            <button type="button" @click="selectedCategory = 'all'" :style="selectedCategory === 'all' ? 'background: linear-gradient(to right, #B01F44, #9B4054); color: white; border-color: #B01F44;' : 'background: white; color: #2B1A1F; border-color: #F4DDE1;'" class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all shadow-sm">Semua</button>
                            <button type="button" @click="selectedCategory = 'facial'" :style="selectedCategory === 'facial' ? 'background: linear-gradient(to right, #B01F44, #9B4054); color: white; border-color: #B01F44;' : 'background: white; color: #2B1A1F; border-color: #F4DDE1;'" class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all shadow-sm">Facial</button>
                            <button type="button" @click="selectedCategory = 'hair'" :style="selectedCategory === 'hair' ? 'background: linear-gradient(to right, #B01F44, #9B4054); color: white; border-color: #B01F44;' : 'background: white; color: #2B1A1F; border-color: #F4DDE1;'" class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all shadow-sm">Hair</button>
                            <button type="button" @click="selectedCategory = 'nails'" :style="selectedCategory === 'nails' ? 'background: linear-gradient(to right, #B01F44, #9B4054); color: white; border-color: #B01F44;' : 'background: white; color: #2B1A1F; border-color: #F4DDE1;'" class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all shadow-sm">Nails</button>
                            <button type="button" @click="selectedCategory = 'massage'" :style="selectedCategory === 'massage' ? 'background: linear-gradient(to right, #B01F44, #9B4054); color: white; border-color: #B01F44;' : 'background: white; color: #2B1A1F; border-color: #F4DDE1;'" class="px-5 py-2 rounded-full text-xs font-bold whitespace-nowrap transition-all shadow-sm">Massage</button>
                        </div>
                    </div>

                    {{-- Treatment Grid (Only Top 3 Rated) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 flex-1">
                        @forelse($topTreatments as $treatment)
                            @php
                                $tItem = is_array($treatment) ? $treatment : (is_object($treatment) ? get_object_vars($treatment) : []);
                                $tName = $tItem['name'] ?? ($treatment->name ?? 'Treatment');
                                $tDesc = $tItem['description'] ?? ($treatment->description ?? '');
                                $tPrice = (float) ($tItem['price'] ?? ($treatment->price ?? 0));
                                $tRating = (float) ($tItem['rating'] ?? ($treatment->rating ?? 4.9));
                                $tDuration = (int) ($tItem['duration_minutes'] ?? ($treatment->duration_minutes ?? 60));
                                $tId = $tItem['id'] ?? ($treatment->id ?? null);
                                $tImage = $tItem['image_url'] ?? ($treatment->image_url ?? \App\Support\ImageHelper::url($tItem['images'] ?? ($treatment->images ?? null)));
                                $catName = $tItem['category_name'] ?? ($treatment->category?->name ?? '');
                            @endphp

                            <div x-show="matchesFilter('{{ strtolower(addslashes($tName)) }}', '{{ strtolower(addslashes($tDesc)) }}', '{{ strtolower(addslashes($catName)) }}')"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 class="bg-gradient-to-b from-white via-[#FFF5F7] to-[#FFE8ED]/60 rounded-3xl overflow-hidden flex flex-col group hover:-translate-y-1 hover:shadow-xl transition-all duration-300 border border-[#F4DDE1] shadow-[0_8px_25px_rgba(176,31,68,0.06)] h-full justify-between">

                                <div class="h-44 overflow-hidden relative aspect-video bg-[#FFE5EC]/50">
                                    <img alt="{{ $tName }}" width="300" height="176" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="{{ $tImage }}"/>
                                    <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-black text-[#B01F44] shadow-sm flex items-center gap-1.5 border border-[#F4DDE1]">
                                        <span class="material-symbols-outlined text-xs text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                                        <span>Top {{ $loop->iteration }}</span>
                                    </div>
                                </div>
                                <div class="p-5 flex flex-col flex-1 justify-between">
                                    <div>
                                        <div class="flex justify-between items-start mb-2 gap-2">
                                            <h3 class="font-headline-sm text-base text-[#2B0F23] line-clamp-1 font-black">{{ $tName }}</h3>
                                            <div class="flex items-center gap-1 text-amber-800 bg-amber-50 px-2.5 py-1 rounded-xl border border-amber-200/80 shrink-0">
                                                <span class="material-symbols-outlined text-xs text-amber-500" style="font-variation-settings: 'FILL' 1;">star</span>
                                                <span class="font-label-md text-xs font-extrabold">{{ number_format($tRating, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 text-[#594043] font-body-sm text-xs mb-3">
                                            <span class="flex items-center gap-1 font-bold bg-[#FFF0F2] text-[#B01F44] border border-[#F4DDE1] px-2.5 py-1 rounded-xl"><span class="material-symbols-outlined text-xs text-[#B01F44]">schedule</span> {{ $tDuration }} min</span>
                                            <span class="flex items-center gap-1 font-black text-[#B01F44] text-xs bg-[#FFF0F2] border border-[#F4DDE1] px-2.5 py-1 rounded-xl"><span class="material-symbols-outlined text-xs text-[#B01F44]">payments</span> Rp {{ number_format($tPrice, 0, ',', '.') }}</span>
                                        </div>
                                        <p class="font-body-sm text-xs text-[#594043] mb-4 line-clamp-2 leading-relaxed">{{ $tDesc }}</p>
                                    </div>

                                    {{-- Smooth Rose Crimson Book Now Button --}}
                                    <a href="{{ route('user.bookings.create', ['treatment_id' => $tId]) }}" style="background: linear-gradient(to right, #B01F44, #C82D53, #9B4054);" class="mt-auto w-full py-3 text-white rounded-2xl font-button text-xs text-center shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-2 group-hover:scale-[1.01] font-bold tracking-wide hover:opacity-90">
                                        <span class="text-white">Book Now</span>
                                        <span class="material-symbols-outlined text-sm text-white transition-transform group-hover:translate-x-1">arrow_forward</span>
                                    </a>
                                </div>
                            </div>


                        @empty
                            <div class="col-span-full py-12 text-center text-[#594043] bg-white/80 rounded-3xl border border-dashed border-[#F4DDE1]">
                                <span class="material-symbols-outlined text-4xl mb-2 text-[#B01F44]">spa</span>
                                <p class="font-bold">Belum ada treatment terbaik yang tersedia.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Right Column: Daily Reward — Natural Height --}}
                <div class="xl:col-span-4 flex flex-col h-fit">
                    
                    {{-- ── SECTION 2: GLOW REWARDS DAILY STREAK CARD ── --}}
                    <div x-data="dailyStreakApp()" class="mb-8">
                        <div class="bg-white/85 backdrop-blur-md rounded-3xl p-6 shadow-sm border border-[#e0bec1] transition-all hover:shadow-md">
                            
                            {{-- Card Header --}}
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-headline-sm text-lg font-black text-[#2B0F23]">Misi Harian</h3>
                                        <span class="animate-pulse text-base">✨</span>
                                    </div>
                                    <p class="text-xs text-[#594043] font-medium">Absen harian, raih hadiah glowing!</p>
                                </div>
                                <div style="background: linear-gradient(135deg, #B01F44, #9B4054);" class="w-10 h-10 rounded-2xl text-white flex items-center justify-center shadow-md transform rotate-6 shrink-0">
                                    <span class="material-symbols-outlined text-xl text-white">redeem</span>
                                </div>
                            </div>

                            {{-- Streak Status Banner --}}
                            <div style="background: linear-gradient(to right, #B01F44, #9B4054);" class="text-white rounded-2xl p-4 mb-4 flex items-center justify-between shadow-md">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-amber-300 text-xl">local_fire_department</span>
                                    <span class="text-xs font-bold text-white">Streak Kamu:</span>
                                </div>
                                <span class="text-xs font-black text-amber-950 bg-amber-300 px-3.5 py-1 rounded-full shadow-sm">
                                    <span x-text="currentStreak">3</span> / 7 Hari 🔥
                                </span>
                            </div>

                            {{-- Inline Stepper --}}
                            <div class="flex items-center justify-between mb-4 w-full px-1">
                                <template x-for="(mission, index) in missions" :key="'step-'+index">
                                    <div class="flex items-center flex-1 last:flex-none">
                                        {{-- Step Circles --}}
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black border-2 transition-all shrink-0 shadow-sm"
                                             :style="{
                                                 'background-color': mission.status === 'completed' ? '#B01F44' : (mission.status === 'active' ? '#fbbf24' : '#FFF0F2'),
                                                 'color': mission.status === 'completed' ? 'white' : (mission.status === 'active' ? '#020617' : '#8D7072'),
                                                 'border-color': mission.status === 'completed' ? '#B01F44' : (mission.status === 'active' ? '#fcd34d' : '#F4DDE1')
                                             }"
                                             :title="mission.dayTitle">
                                            <template x-if="mission.status === 'completed'">
                                                <span class="material-symbols-outlined text-sm font-black text-white">check</span>
                                            </template>
                                            <template x-if="mission.status === 'active'">
                                                <span x-text="index + 1"></span>
                                            </template>
                                            <template x-if="mission.status === 'locked'">
                                                <span class="material-symbols-outlined text-xs opacity-60">lock</span>
                                            </template>
                                        </div>

                                        {{-- Connecting Line --}}
                                        <template x-if="index < missions.length - 1">
                                            <div class="flex-1 h-1 mx-1 rounded-full transition-all duration-300"
                                                 :style="{ 'background-color': index < (currentStreak - 1) ? '#B01F44' : '#F4DDE1' }">
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            {{-- Kartu "Misi Hari Ini" --}}
                            <div class="rounded-2xl py-4 px-4 bg-white/80 backdrop-blur-md border border-[#F4DDE1] shadow-sm mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-black uppercase tracking-wider text-[#B01F44]">✨ Misi Hari Ini</span>
                                    <span class="text-xs font-black px-3 py-1 rounded-full text-white shadow-sm" style="background: linear-gradient(to right, #B01F44, #9B4054);" x-text="activeMission.reward"></span>
                                </div>
                                <h4 class="font-black text-sm text-[#2B0F23] mb-1" x-text="activeMission.dayTitle"></h4>
                                <p class="text-xs text-[#594043] italic font-medium" x-text="activeMission.funnyText"></p>
                            </div>

                            {{-- Toggle Detail 7 Hari --}}
                            <button @click="showAllMissions = !showAllMissions" type="button" class="w-full flex items-center justify-center gap-1 text-xs font-extrabold text-[#B01F44] mb-3 hover:underline">
                                <span x-text="showAllMissions ? 'Sembunyikan Detail' : 'Lihat Semua Misi (7 Hari)'"></span>
                                <span class="material-symbols-outlined text-sm transition-transform duration-200" :class="showAllMissions ? '-rotate-180' : ''">expand_more</span>
                            </button>

                            {{-- Expanded 7-Day List --}}
                            <div x-show="showAllMissions" x-collapse x-cloak class="mt-2 mb-4 relative space-y-2">
                                <div class="absolute left-4 top-4 bottom-4 w-1 rounded-full z-0" style="background: linear-gradient(to bottom, #B01F44, #F4DDE1);"></div>
                                <template x-for="(mission, index) in missions" :key="'detail-'+index">
                                    <div class="relative z-10 flex items-center gap-2 transition-all duration-300 transform"
                                         :style="{ 'transform': mission.status === 'active' ? 'scale(1.02)' : 'none' }">
                                         
                                        <div class="w-8 h-8 rounded-full flex items-center justify-center shadow-md shrink-0 border-2 transition-all"
                                             :style="{
                                                 'background-color': mission.status === 'completed' ? '#B01F44' : (mission.status === 'active' ? '#fbbf24' : '#FFF0F2'),
                                                 'color': mission.status === 'completed' ? 'white' : (mission.status === 'active' ? '#020617' : '#8D7072'),
                                                 'border-color': mission.status === 'completed' ? '#B01F44' : (mission.status === 'active' ? '#fcd34d' : '#F4DDE1')
                                             }">
                                            <template x-if="mission.status === 'completed'">
                                                <span class="material-symbols-outlined text-sm font-black text-white">check</span>
                                            </template>
                                            <template x-if="mission.status === 'active'">
                                                <span class="font-bold text-xs" x-text="index + 1"></span>
                                            </template>
                                            <template x-if="mission.status === 'locked'">
                                                <span class="material-symbols-outlined text-sm opacity-60">lock</span>
                                            </template>
                                        </div>

                                        <div class="flex-1 p-2 rounded-xl border transition-all"
                                             :style="{
                                                 'background-color': mission.status === 'completed' ? '#FFF0F2' : (mission.status === 'active' ? 'white' : 'rgba(255, 245, 247, 0.5)'),
                                                 'border-color': mission.status === 'completed' ? 'rgba(244, 221, 225, 0.5)' : (mission.status === 'active' ? '#B01F44' : 'transparent'),
                                                 'opacity': mission.status === 'completed' ? '0.8' : (mission.status === 'active' ? '1' : '0.6'),
                                                 'box-shadow': mission.status === 'active' ? '0 4px 6px -1px rgba(176, 31, 68, 0.1), 0 2px 4px -1px rgba(176, 31, 68, 0.06)' : 'none'
                                             }">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="font-bold text-xs" :style="{ 'color': mission.status === 'active' ? '#B01F44' : '#2B0F23' }" x-text="mission.dayTitle"></span>
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                                      :style="{
                                                          'background-color': mission.status === 'active' ? '#B01F44' : '#FFF0F2',
                                                          'color': mission.status === 'active' ? 'white' : '#594043',
                                                          'border': mission.status === 'active' ? 'none' : '1px solid #F4DDE1'
                                                      }"
                                                      x-text="mission.reward"></span>
                                            </div>
                                            <p class="text-xs text-[#594043]/90 line-clamp-1 italic font-medium" x-text="mission.funnyText"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Claim CTA Button --}}
                        <div class="pt-2">
                            <button @click="claimTodayReward()"
                                    :disabled="hasClaimedToday"
                                    type="button"
                                    class="relative w-full py-3.5 rounded-2xl font-button text-xs shadow-md transition-all duration-300 flex justify-center items-center gap-2 group transform active:scale-95 disabled:cursor-not-allowed overflow-hidden"
                                    :style="hasClaimedToday ? 'background: #B01F44; color: white; opacity: 0.9; box-shadow: none;' : 'background: linear-gradient(to right, #B01F44, #C82D53, #9B4054); color: white;'">
                                <span class="material-symbols-outlined text-lg text-white" :class="!hasClaimedToday ? 'group-hover:rotate-12 transition-transform' : ''">
                                    <span x-text="hasClaimedToday ? 'task_alt' : 'celebration'"></span>
                                </span>
                                <span class="text-white font-black" x-text="hasClaimedToday ? 'Hadiah Hari Ini Sudah Diklaim' : 'Klaim Hadiah Hari Ini!'"></span>
                            </button>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Booking Management Section --}}
            <section class="mt-12 pt-8 border-t border-[#E0247E]/20">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <h2 class="font-headline-md text-2xl font-black text-[#2B0F23]">Riwayat & Booking Saya</h2>
                        <p class="text-body-sm text-[#5C1439]/80 font-medium">Kelola janji temu dan periksa status jadwal perawatan Anda.</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex gap-4 overflow-x-auto no-scrollbar border-b border-[#E0247E]/20 pb-2">
                            <template x-for="t in tabs" :key="t">
                                <button
                                    @click="switchTab(t)"
                                    class="pb-2 text-label-lg font-label-lg whitespace-nowrap transition-colors font-bold"
                                    :class="activeTab === t ? 'text-[#E0247E] border-b-2 border-[#E0247E]' : 'text-[#5C1439]/70 hover:text-[#E0247E]'"
                                    x-text="t.charAt(0).toUpperCase() + t.slice(1)">
                                </button>
                            </template>
                        </div>

                        <button
                            type="button"
                            @click="toggleSort()"
                            class="flex items-center gap-1 px-3 py-2 rounded-full border border-[#E0247E]/25 bg-[#FFF6FA] text-xs font-bold text-[#5C1439] hover:border-[#E0247E] hover:text-[#E0247E] transition-colors shadow-sm"
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
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#2B0F23]/70 backdrop-blur-sm">
            {{-- Modal Content (QR Code) --}}
            <div @click.away="showQrModal = false"
                 class="backdrop-blur-xl rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl relative border border-white/20 text-white"
                 style="{{ $currentTier['style_bg'] ?? 'background: linear-gradient(135deg, #B01F44, #5C1439);' }}"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-90 translate-y-4">

                <button @click.stop="showQrModal = false" type="button" class="absolute top-4 right-4 text-white hover:text-white transition-colors z-[100] bg-black/40 p-2.5 rounded-full backdrop-blur-md shadow-lg cursor-pointer">
                    <span class="material-symbols-outlined font-black pointer-events-none text-xl block">close</span>
                </button>

                <div class="p-6 text-center border-b border-white/10 relative z-10">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl mx-auto flex items-center justify-center mb-3 shadow-inner text-white">
                        <span class="material-symbols-outlined text-2xl">qr_code_scanner</span>
                    </div>
                    <h3 class="font-headline-sm text-xl font-bold text-white mb-1">Kode QR Member Yalia</h3>
                    <p class="text-xs text-white/80">Tunjukkan QR code ini ke kasir salon untuk klaim poin & benefit member.</p>
                </div>

                {{-- QR Code Image Box --}}
                <div class="p-4 bg-white rounded-2xl shadow-inner border border-white/40 inline-block relative group mt-6 mb-2 mx-auto left-1/2 -translate-x-1/2">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode('YB-MEMBER-' . $user->id) }}"
                         alt="Member QR Code"
                         width="160"
                         height="160"
                         loading="lazy"
                         decoding="async"
                         class="w-40 h-40 mx-auto object-contain">
                    <div class="mt-2 text-xs font-mono font-bold tracking-widest uppercase text-center" style="color: #2B0F23 !important;">
                        YB-{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}
                    </div>
                </div>

                <div class="px-6 pb-6">
                    <div class="bg-black/20 p-4 rounded-xl flex items-center justify-between text-xs mb-4 border border-white/10 shadow-inner">
                        <div>
                            <span class="text-white/70 block">Nama Member:</span>
                            <strong class="text-white text-sm drop-shadow-sm">{{ $user->name }}</strong>
                        </div>
                        <span class="px-3 py-1 bg-white/20 text-white rounded-full font-bold uppercase text-xs tracking-wider border border-white/20 shadow-sm backdrop-blur-sm">
                            {{ $membership['current'] }}
                        </span>
                    </div>

                    <button @click="copyMemberId()" type="button" class="w-full py-3 bg-white hover:bg-gray-100 font-button text-sm rounded-full transition-colors flex items-center justify-center gap-2 shadow-lg font-black" style="color: #2B0F23 !important;">
                        <span class="material-symbols-outlined text-sm" style="color: #2B0F23 !important;">content_copy</span>
                        <span x-text="copied ? 'Berhasil Disalin!' : 'Salin ID Member'" style="color: #2B0F23 !important;"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Modern Tier Showcase Modal (dark plum-magenta-gold, kontras premium dari background pink) --}}
        <div x-show="showTiersModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md overflow-y-auto">
            <div @click.away="showTiersModal = false" class="bg-slate-900 rounded-[32px] max-w-5xl w-full p-6 md:p-8 shadow-2xl border border-slate-700/80 relative space-y-6 my-8 max-h-[90vh] overflow-y-auto no-scrollbar text-white">

                {{-- Close Button --}}
                <button type="button" @click="showTiersModal = false" aria-label="Tutup modal tiers" class="absolute top-6 right-6 text-slate-400 hover:text-white transition-colors bg-white/10 p-2.5 rounded-full backdrop-blur-md">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>

                {{-- Modal Header --}}
                <div class="text-center max-w-xl mx-auto space-y-2">
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-widest bg-purple-500/20 text-purple-300 border border-purple-500/40 inline-flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">workspace_premium</span>
                        YALIA BEAUTY TIER SYSTEM
                    </span>
                    <h3 class="text-2xl md:text-3xl font-black text-white tracking-tight">Level Membership & Benefit Diskon</h3>
                    <p class="text-xs md:text-sm text-slate-300 font-medium">Tingkatkan poin perawatan Anda untuk membuka diskon otomatis di setiap transaksi!</p>
                </div>

                {{-- 4 Tier Member Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 pt-2">

                    {{-- Loop 4 Tiers --}}
                    @foreach($allTiers as $tierKey => $tMeta)
                        @php
                            $isActive = ($membership['current'] ?? '') === $tierKey;
                        @endphp
                        <div class="relative rounded-2xl p-5 flex flex-col justify-between overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:shadow-2xl border text-white {{ $isActive ? 'ring-4 ring-amber-400 ring-offset-2 ring-offset-slate-900' : '' }}" style="{{ $tMeta['style_bg'] ?? '' }} {{ $tMeta['border_style'] ?? '' }}">

                            {{-- 3D Gem Header Icon --}}
                            <div class="flex justify-between items-start mb-4 relative z-10">
                                <div>
                                    <span class="text-xs font-black tracking-widest uppercase text-slate-400 block">TIER {{ $loop->iteration }}</span>
                                    <h4 class="text-lg font-black tracking-wide text-white uppercase">{{ $tMeta['name'] }}</h4>
                                    <p class="text-xs text-slate-300 font-semibold mt-0.5">{{ number_format($tMeta['min_points']) }} PTS Minimum</p>
                                </div>

                                {{-- Gem Cutout Icon --}}
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $tMeta['gem_gradient'] }} p-2 shadow-lg flex items-center justify-center text-slate-950 font-bold shrink-0">
                                    <i class="fa-solid fa-gem text-lg"></i>
                                </div>
                            </div>

                            {{-- Active Badge indicator --}}
                            @if($isActive)
                                <div class="mb-3">
                                    <span class="inline-flex items-center gap-1 text-xs font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                        Level Saat Ini
                                    </span>
                                </div>
                            @endif

                            {{-- Big Discount Highlight Banner (Solid Color for 100% Crisp Visibility) --}}
                            <div class="py-3 px-3.5 rounded-xl bg-slate-950/60 backdrop-blur-md border border-white/15 my-2 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-300 uppercase">Benefit Diskon:</span>
                                <span class="text-base font-black text-amber-300 tracking-wide drop-shadow-md">
                                    {{ $tMeta['discount'] }} OFF
                                </span>
                            </div>

                            {{-- Benefits Checklist --}}
                            <ul class="space-y-1.5 text-xs text-slate-200 mt-2 mb-4 font-medium">
                                @foreach($tMeta['benefits'] as $benefit)
                                    <li class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-circle-check text-amber-400 text-xs shrink-0"></i>
                                        <span class="line-clamp-1 text-slate-100">{{ $benefit }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            {{-- Bottom Tier Status Bar --}}
                            <div class="pt-2 border-t border-white/10 text-center">
                                @if($isActive)
                                    <span class="text-xs font-bold text-emerald-400">✨ Status Aktif</span>
                                @elseif(($user->tier_points ?? 0) < $tMeta['min_points'])
                                    <span class="text-xs text-slate-400">Tersisa {{ number_format($tMeta['min_points'] - ($user->tier_points ?? 0)) }} Pts</span>
                                @else
                                    <span class="text-xs font-bold text-purple-300">Unlocked 🎉</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                </div>

                <div class="pt-4 text-center">
                    <button @click="showTiersModal = false" type="button" class="px-8 py-3 bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs rounded-full shadow-lg transition-all border border-purple-400/40">
                        Mengerti & Kembali
                    </button>
                </div>

            </div>
        </div>

        {{-- Funny Reward Claim Modal Popup (Velvet-Rose Luxury Dark Glassmorphism) --}}
        <div x-show="showRewardModal" x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90 blur-sm"
             x-transition:enter-end="opacity-100 scale-100 blur-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 blur-0"
             x-transition:leave-end="opacity-0 scale-90 blur-sm"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#1e0a16]/85 backdrop-blur-2xl">
            <div class="relative w-full max-w-sm overflow-hidden rounded-[32px] bg-gradient-to-b from-[#2e1022]/95 via-[#1d0a16]/95 to-[#12050e]/95 p-6 text-center space-y-5 shadow-[0_25px_70px_rgba(0,0,0,0.85)] border border-white/20 backdrop-blur-3xl ring-1 ring-white/10">
                <div class="absolute -top-12 -right-12 w-44 h-44 bg-[#f45472]/30 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-12 -left-12 w-44 h-44 bg-amber-400/25 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-56 h-56 bg-purple-600/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 mx-auto flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-400/20 via-pink-500/20 to-purple-600/20 border border-amber-300/40 shadow-[0_0_25px_rgba(244,185,66,0.3)] animate-pulse">
                    <span class="text-4xl filter drop-shadow-[0_4px_12px_rgba(244,185,66,0.5)]" x-text="rewardModalData.icon">✨</span>
                </div>

                <div class="relative z-10 space-y-2">
                    <div class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-gradient-to-r from-amber-500/20 via-pink-500/20 to-amber-500/20 border border-amber-400/50 rounded-full shadow-[0_2px_12px_rgba(244,185,66,0.25)]">
                        <span class="text-[11px] font-black tracking-widest uppercase text-amber-300 drop-shadow-[0_1px_2px_rgba(0,0,0,0.8)]">
                            AURA GLOW-UP MEMANCAR! ✨
                        </span>
                    </div>
                    <h3 class="font-headline-sm text-2xl font-black text-white tracking-wide drop-shadow-[0_2px_10px_rgba(0,0,0,0.8)] font-serif" x-text="rewardModalData.title"></h3>
                </div>

                <div class="relative z-10 p-4 bg-black/40 backdrop-blur-md rounded-2xl border border-white/15 text-xs text-rose-100/90 leading-relaxed font-medium shadow-[inner_0_2px_4px_rgba(0,0,0,0.6)]">
                    <p x-text="rewardModalData.message"></p>
                </div>

                <div class="relative z-10 bg-gradient-to-r from-amber-500/25 via-pink-500/20 to-amber-500/25 p-4 rounded-2xl border border-amber-400/50 flex items-center justify-center gap-2.5 text-white font-bold text-sm shadow-[0_4px_20px_rgba(244,185,66,0.2)]">
                    <span class="material-symbols-outlined text-amber-300 text-xl filter drop-shadow-[0_0_8px_rgba(244,185,66,0.8)]">stars</span>
                    <span class="tracking-wide text-xs uppercase text-amber-100/90 font-extrabold">HADIAH:</span>
                    <span class="text-amber-300 text-base font-black tracking-wider drop-shadow-[0_2px_10px_rgba(244,185,66,0.8)]" x-text="rewardModalData.reward"></span>
                </div>

                <div class="relative z-10 pt-1">
                    <button @click="closeRewardModal()" type="button" class="shimmer-btn relative w-full py-4 bg-gradient-to-r from-[#b01f44] via-[#f45472] to-[#e0247e] hover:brightness-110 text-white font-black text-sm rounded-full shadow-[0_8px_30px_rgba(244,84,114,0.5)] transition-all transform hover:scale-[1.02] active:scale-95 border border-white/20 tracking-wider uppercase">
                        SIAP GLOW-UP BANGET!
                    </button>
                </div>
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
                showTiersModal: false,
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