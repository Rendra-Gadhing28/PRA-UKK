<div x-data="{ sidebarOpen: false }">
    {{-- Mobile Top Bar --}}
    <div class="md:hidden flex items-center justify-between bg-[#1f0d11]/95 backdrop-blur-md px-4 py-3 border-b border-white/10 sticky top-0 z-40 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="relative w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#f4b942] via-[#e0247e] to-[#b01f44] p-[2px] shadow-md shadow-black/40">
                <div class="w-full h-full bg-white rounded-[14px] p-1 flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Admin" width="36" height="36" class="w-full h-full object-contain">
                </div>
            </div>
            <div>
                <span class="font-serif font-black text-white text-sm tracking-tight block leading-tight">Yalia Beauty</span>
                <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-widest text-rose-300 font-mono">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Admin Portal
                </span>
            </div>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" type="button" aria-label="Toggle Sidebar" class="p-2 text-rose-200 hover:text-white hover:bg-white/10 rounded-xl transition-colors focus:outline-none">
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
         class="fixed inset-0 bg-black/75 backdrop-blur-sm z-40 md:hidden"></div>

    {{-- Sidebar Container --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed top-0 left-0 bottom-0 w-64 bg-[#1f0d11] text-rose-100 border-r border-white/10 shadow-2xl md:shadow-none z-50 flex flex-col transition-transform duration-300 ease-in-out">
        
        {{-- Luxury Brand / Header --}}
        <div class="p-5 border-b border-white/10 relative overflow-hidden bg-gradient-to-b from-[#2e1219]/80 via-[#1f0d11] to-[#1f0d11]">
            {{-- Ambient Soft Glow --}}
            <div class="absolute -top-6 -left-6 w-28 h-28 bg-gradient-to-br from-rose-500/20 via-amber-400/10 to-transparent rounded-full blur-xl pointer-events-none"></div>

            <div class="flex items-center justify-between relative z-10">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3.5 group">
                    {{-- Luxurious Multi-Ring Logo Frame with Golden Accents --}}
                    <div class="relative w-12 h-12 rounded-2xl bg-gradient-to-tr from-[#f4b942] via-[#e0247e] to-[#b01f44] p-[2px] shadow-[0_6px_20px_rgba(176,31,68,0.35)] group-hover:scale-105 transition-transform duration-300">
                        <div class="w-full h-full bg-white rounded-[14px] p-1.5 flex items-center justify-center overflow-hidden shadow-inner">
                            <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty" width="44" height="44" class="w-full h-full object-contain">
                        </div>
                    </div>

                    {{-- Brand Title & Executive Badge --}}
                    <div class="min-w-0">
                        <div class="flex items-center gap-1.5">
                            <span class="font-serif font-black text-white text-lg tracking-tight block leading-tight">Yalia Beauty</span>
                            <i class="fa-solid fa-gem text-amber-400 text-xs" aria-hidden="true"></i>
                        </div>
                        <div class="mt-1">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-black uppercase tracking-widest bg-rose-500/15 border border-rose-500/30 text-rose-200 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Executive Suite
                            </span>
                        </div>
                    </div>
                </a>

                <button @click="sidebarOpen = false" type="button" aria-label="Close Sidebar" class="md:hidden text-rose-300 hover:text-white p-1.5 rounded-lg hover:bg-white/10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 p-3.5 space-y-1.5 overflow-y-auto">
            <div class="px-3 pt-2 pb-1 text-xs font-black uppercase tracking-wider text-rose-200/40">Main Menu</div>

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

            {{-- Notifikasi --}}
            @php
                $sidebarUnreadCount = \App\Models\Notifications::unread()->active()->count();
            @endphp
            <a href="{{ route('admin.notifications.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.notifications.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.notifications.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center justify-center">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if($sidebarUnreadCount > 0)
                            <span class="absolute -top-1 -right-1 w-2 h-2 bg-amber-400 rounded-full animate-ping"></span>
                        @endif
                    </div>
                    <span>Notifikasi</span>
                </div>
                <div class="flex items-center gap-1.5">
                    @if($sidebarUnreadCount > 0)
                        <span class="px-2 py-0.5 text-[10px] font-black rounded-full bg-rose-500 text-white shadow-xs">
                            {{ $sidebarUnreadCount }}
                        </span>
                    @endif
                    @if(request()->routeIs('admin.notifications.*'))
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                    @endif
                </div>
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

            {{-- Ulasan Pelanggan --}}
            <a href="{{ Route::has('admin.reviews.index') ? route('admin.reviews.index') : '#' }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.reviews.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.reviews.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-comments text-base shrink-0"></i>
                    <span>Ulasan Pelanggan</span>
                </div>
                @if(request()->routeIs('admin.reviews.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            <div class="px-3 pt-3 pb-1 text-xs font-black uppercase tracking-wider text-rose-200/40">Finance & Promos</div>

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

            <div class="px-3 pt-3 pb-1 text-xs font-black uppercase tracking-wider text-rose-200/40">Pengaturan Akun</div>

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

            <div class="px-3 pt-3 pb-1 text-xs font-black uppercase tracking-wider text-rose-200/40">Sistem & Audit</div>

            {{-- Activity Logs --}}
            <a href="{{ route('admin.activity-logs.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.activity-logs.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.activity-logs.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-clock-rotate-left text-base shrink-0"></i>
                    <span>Log Aktivitas</span>
                </div>
                @if(request()->routeIs('admin.activity-logs.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            {{-- Tong Sampah (Trash) --}}
            <a href="{{ route('admin.trash.index') }}" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-2xl text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.trash.*') ? 'admin-nav-active' : 'admin-nav-idle' }}"
               style="{{ request()->routeIs('admin.trash.*') ? 'background: linear-gradient(135deg, #b01f44 0%, #c82d53 50%, #e0247e 100%); color: #ffffff;' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-trash-can text-base shrink-0"></i>
                    <span>Tong Sampah</span>
                </div>
                @if(request()->routeIs('admin.trash.*'))
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                @endif
            </a>

            <div class="pt-3 border-t border-white/10 my-2"></div>

            {{-- Switch to User Site --}}
            <a href="{{ route('user.dashboard') }}" data-spa-ignore
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-2xl text-xs font-bold text-rose-200 bg-white/5 hover:bg-white/10 border border-white/10 transition-all shadow-sm group">
                <i class="fa-solid fa-arrow-left text-xs transition-transform group-hover:-translate-x-1"></i>
                <span>Lihat Tampilan Customer</span>
            </a>
        </nav>

        {{-- Footer User Profile --}}
        <div class="p-4 border-t border-white/10 bg-gradient-to-t from-[#14080b] to-[#1f0d11] flex items-center justify-between">
            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 min-w-0 group hover:opacity-90 transition-opacity">
                <div class="relative w-9 h-9 rounded-full bg-gradient-to-tr from-[#f4b942] to-[#b01f44] p-[1.5px] shrink-0 shadow-sm">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=f45472&color=fff' }}" 
                         alt="Avatar" class="w-full h-full rounded-full object-cover bg-rose-950">
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate group-hover:text-rose-300 transition-colors">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-rose-300/50 truncate">{{ auth()->user()->email }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-rose-300/60 hover:text-white rounded-xl hover:bg-white/10 transition-colors">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
                </button>
            </form>
        </div>

    </aside>
</div>
