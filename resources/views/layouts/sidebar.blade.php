<div x-data="{ sidebarOpen: false }">
    {{-- Mobile Top Bar --}}
    <div class="md:hidden flex items-center justify-between bg-white/95 backdrop-blur-md px-4 py-3 border-b border-rose-100 sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="relative w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#f4b942] via-[#e0247e] to-[#b01f44] p-[2px] shadow-md shadow-rose-900/15">
                <div class="w-full h-full bg-white rounded-[14px] p-1 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Admin" width="36" height="36" class="w-full h-full object-contain">
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-amber-400 border-2 border-white rounded-full flex items-center justify-center shadow-sm">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                </div>
            </div>
            <div>
                <span class="font-serif font-black text-gray-900 text-sm tracking-tight block leading-tight">Yalia Beauty</span>
                <span class="text-xs font-black uppercase tracking-widest text-[#b01f44] font-mono">Admin Portal</span>
            </div>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" type="button" aria-label="Toggle Sidebar" class="p-2 text-rose-950/70 hover:text-[#b01f44] hover:bg-rose-50 rounded-xl transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    {{-- Mobile Overlay Backdrop --}}
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-950/60 backdrop-blur-sm z-40 md:hidden"></div>

    {{-- Sidebar Container --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed top-0 left-0 bottom-0 w-64 bg-white/95 backdrop-blur-xl border-r border-rose-100/80 shadow-xl md:shadow-none z-50 flex flex-col transition-transform duration-300 ease-in-out">
        
        {{-- Luxury Brand / Header --}}
        <div class="p-5 border-b border-rose-100/70 relative overflow-hidden bg-gradient-to-b from-rose-50/40 via-white to-white">
            {{-- Ambient Soft Glow --}}
            <div class="absolute -top-6 -left-6 w-28 h-28 bg-gradient-to-br from-rose-400/20 via-amber-300/15 to-transparent rounded-full blur-xl pointer-events-none"></div>

            <div class="flex items-center justify-between relative z-10">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3.5 group">
                    {{-- Luxurious Multi-Ring Logo Frame with Golden Accents --}}
                    <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#f4b942] via-[#e0247e] to-[#b01f44] p-[2px] shadow-[0_6px_20px_rgba(176,31,68,0.22)] group-hover:scale-105 transition-transform duration-300">
                        <div class="w-full h-full bg-white rounded-[14px] p-1.5 flex items-center justify-center overflow-hidden shadow-inner">
                            <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty" width="44" height="44" class="w-full h-full object-contain">
                        </div>
                        {{-- Floating Gem / Live Status Badge --}}
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-gradient-to-br from-amber-300 to-amber-500 border-2 border-white rounded-full flex items-center justify-center shadow-md">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                        </div>
                    </div>

                    {{-- Brand Title & Executive Badge --}}
                    <div class="min-w-0">
                        <div class="flex items-center gap-1">
                            <span class="font-serif font-black text-gray-900 text-lg tracking-tight block leading-tight">Yalia Beauty</span>
                            <span class="text-amber-500 text-xs">✨</span>
                        </div>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-widest bg-gradient-to-r from-rose-500/10 via-[#b01f44]/12 to-amber-500/10 border border-[#b01f44]/25 text-[#b01f44] shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Executive Suite
                            </span>
                        </div>
                    </div>
                </a>

                <button @click="sidebarOpen = false" type="button" aria-label="Close Sidebar" class="md:hidden text-rose-900/40 hover:text-rose-950/80 p-1.5 rounded-lg hover:bg-rose-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 p-3.5 space-y-1.5 overflow-y-auto">
            <div class="px-3 pt-2 pb-1 text-xs font-black uppercase tracking-wider text-rose-950/40">Main Menu</div>

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.dashboard') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    <span>Dashboard</span>
                </div>
                @if(request()->routeIs('admin.dashboard'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            {{-- Treatments --}}
            <a href="{{ Route::has('admin.treatments.index') ? route('admin.treatments.index') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.treatments.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.treatments.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.603 15.1a2 2 0 00-2.028 1.458l-1 3.5a2 2 0 002.32 2.477l3.585-.717a6 6 0 003.86-.517l.318-.158a6 6 0 013.86-.517l2.387.477a2 2 0 002.477-2.32l-.717-3.585z"/></svg>
                    <span>Treatments</span>
                </div>
                @if(request()->routeIs('admin.treatments.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            {{-- Bookings --}}
            <a href="{{ Route::has('admin.bookings.index') ? route('admin.bookings.index') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.bookings.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.bookings.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Bookings</span>
                </div>
                @if(request()->routeIs('admin.bookings.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            {{-- Beauticians --}}
            <a href="{{ Route::has('admin.beauticians.index') ? route('admin.beauticians.index') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.beauticians.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.beauticians.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Beauticians</span>
                </div>
                @if(request()->routeIs('admin.beauticians.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            <div class="px-3 pt-3 pb-1 text-xs font-black uppercase tracking-wider text-rose-950/40">Finance & Promos</div>

            {{-- Keuangan / Finances --}}
            <a href="{{ Route::has('admin.finances.index') ? route('admin.finances.index') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.finances.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.finances.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Keuangan</span>
                </div>
                @if(request()->routeIs('admin.finances.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            {{-- Vouchers --}}
            <a href="{{ Route::has('admin.vouchers.index') ? route('admin.vouchers.index') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.vouchers.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.vouchers.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-ticket text-base shrink-0"></i>
                    <span>Vouchers</span>
                </div>
                @if(request()->routeIs('admin.vouchers.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            <div class="px-3 pt-3 pb-1 text-xs font-black uppercase tracking-wider text-rose-950/40">Pengaturan Akun</div>

            {{-- Kelola User --}}
            <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.users.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-users-gear text-base shrink-0"></i>
                    <span>Kelola User</span>
                </div>
                @if(request()->routeIs('admin.users.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            {{-- Profil Admin --}}
            <a href="{{ Route::has('admin.profile.edit') ? route('admin.profile.edit') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.profile.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.profile.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-gear text-base shrink-0"></i>
                    <span>Pengaturan Profil</span>
                </div>
                @if(request()->routeIs('admin.profile.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            <div class="pt-3 border-t border-rose-100/70 my-2"></div>

            {{-- Switch to User Site --}}
            <a href="{{ route('user.dashboard') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold text-[#b01f44] bg-rose-50/80 hover:bg-rose-100/80 border border-rose-200/60 transition-all shadow-sm group">
                <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                <span>Lihat Tampilan Customer</span>
            </a>
        </nav>

        {{-- Footer User Profile --}}
        <div class="p-4 border-t border-rose-100/70 bg-gradient-to-t from-rose-50/40 to-white flex items-center justify-between">
            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 min-w-0 group hover:opacity-85 transition-opacity">
                <div class="relative w-9 h-9 rounded-full bg-gradient-to-tr from-[#f4b942] to-[#b01f44] p-[1.5px] shrink-0 shadow-sm">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=f45472&color=fff' }}" 
                         alt="Avatar" class="w-full h-full rounded-full object-cover bg-white">
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate group-hover:text-[#b01f44] transition-colors">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-rose-950/60 truncate">{{ auth()->user()->email }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-rose-900/50 hover:text-[#b01f44] rounded-xl hover:bg-rose-50 transition-colors">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
                </button>
            </form>
        </div>

    </aside>
</div>
