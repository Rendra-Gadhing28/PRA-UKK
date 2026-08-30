<div x-data="{ sidebarOpen: false }">
    {{-- Mobile Top Bar --}}
    <div class="md:hidden flex items-center justify-between bg-white px-4 py-3 border-b border-rose-100 sticky top-0 z-40">
        <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-[#f45472] to-[#ff8fa4] p-[2px] flex items-center justify-center shadow-md">
                <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Admin" width="36" height="36" style="width: 36px; height: 36px;" class="w-full h-full object-cover rounded-full bg-white">
            </div>
            <span class="font-headline font-bold text-gray-900 text-sm tracking-wide">Yalia Admin</span>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-600 hover:text-rose-600 focus:outline-none">
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
         class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 md:hidden"></div>

    {{-- Sidebar Container --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
           class="fixed top-0 left-0 bottom-0 w-64 bg-white/95 backdrop-blur-md border-r border-rose-100 shadow-xl md:shadow-none z-50 flex flex-col transition-transform duration-300 ease-in-out">
        
        {{-- Brand / Header --}}
        <div class="p-6 border-b border-rose-50 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#f45472] to-[#ff8fa4] p-[2px] flex items-center justify-center shadow-lg shadow-rose-200">
                    <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty" width="40" height="40" style="width: 40px; height: 40px;" class="w-full h-full object-cover rounded-full bg-white">
                </div>
                <div>
                    <span class="font-headline font-bold text-gray-900 text-base block leading-tight">Yalia Beauty</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-[#f45472] bg-rose-50 px-2 py-0.5 rounded-full inline-block mt-0.5">Admin Executive</span>
                </div>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <div class="px-3 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">Main Menu</div>

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-[#f45472] text-white shadow-md shadow-rose-200 font-bold' : 'text-gray-600 hover:text-[#f45472] hover:bg-rose-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>

            {{-- Treatments --}}
            <a href="{{ Route::has('admin.treatments.index') ? route('admin.treatments.index') : '#' }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.treatments.*') ? 'bg-[#f45472] text-white shadow-md shadow-rose-200 font-bold' : 'text-gray-600 hover:text-[#f45472] hover:bg-rose-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L5.603 15.1a2 2 0 00-2.028 1.458l-1 3.5a2 2 0 002.32 2.477l3.585-.717a6 6 0 003.86-.517l.318-.158a6 6 0 013.86-.517l2.387.477a2 2 0 002.477-2.32l-.717-3.585z"/></svg>
                Treatments
            </a>

            {{-- Bookings --}}
            <a href="{{ Route::has('admin.bookings.index') ? route('admin.bookings.index') : '#' }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.bookings.*') ? 'bg-[#f45472] text-white shadow-md shadow-rose-200 font-bold' : 'text-gray-600 hover:text-[#f45472] hover:bg-rose-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Bookings
            </a>

            {{-- Beauticians --}}
            <a href="{{ Route::has('admin.beauticians.index') ? route('admin.beauticians.index') : '#' }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.beauticians.*') ? 'bg-[#f45472] text-white shadow-md shadow-rose-200 font-bold' : 'text-gray-600 hover:text-[#f45472] hover:bg-rose-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                Beauticians
            </a>

            <div class="px-3 pt-4 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">Finance & Promos</div>

            {{-- Keuangan / Finances --}}
            <a href="{{ Route::has('admin.finances.index') ? route('admin.finances.index') : '#' }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.finances.*') ? 'bg-[#f45472] text-white shadow-md shadow-rose-200 font-bold' : 'text-gray-600 hover:text-[#f45472] hover:bg-rose-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Keuangan
            </a>

            {{-- Vouchers --}}
            <a href="{{ Route::has('admin.vouchers.index') ? route('admin.vouchers.index') : '#' }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.vouchers.*') ? 'bg-[#f45472] text-white shadow-md shadow-rose-200 font-bold' : 'text-gray-600 hover:text-[#f45472] hover:bg-rose-50' }}">
                <i class="fa-solid fa-ticket text-[#f45472] {{ request()->routeIs('admin.vouchers.*') ? 'text-white' : '' }} text-base"></i>
                <span>Vouchers</span>
            </a>

            <div class="px-3 pt-4 py-2 text-[10px] font-bold uppercase tracking-wider text-gray-400">Pengaturan Akun</div>

            {{-- Profil Admin --}}
            <a href="{{ Route::has('admin.profile.edit') ? route('admin.profile.edit') : '#' }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-2xl text-sm font-semibold transition-all {{ request()->routeIs('admin.profile.*') ? 'bg-[#f45472] text-white shadow-md shadow-rose-200 font-bold' : 'text-gray-600 hover:text-[#f45472] hover:bg-rose-50' }}">
                <i class="fa-solid fa-user-gear text-[#f45472] {{ request()->routeIs('admin.profile.*') ? 'text-white' : '' }} text-base"></i>
                <span>Pengaturan Profil</span>
            </a>

            <div class="pt-4 border-t border-rose-50 my-2"></div>

            {{-- Switch to User Site --}}
            <a href="{{ route('user.dashboard') }}" 
               class="flex items-center gap-3 px-4 py-2.5 rounded-2xl text-xs font-semibold text-rose-600 bg-rose-50/70 hover:bg-rose-100 transition-all">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Lihat Tampilan Customer</span>
            </a>
        </nav>

        {{-- Footer User Profile --}}
        <div class="p-4 border-t border-rose-50 bg-rose-50/30 flex items-center justify-between">
            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-3 min-w-0 group hover:opacity-80 transition-opacity">
                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=f45472&color=fff' }}" 
                     alt="Avatar" class="w-9 h-9 rounded-full object-cover border border-rose-200 shrink-0">
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-900 truncate group-hover:text-[#f45472] transition-colors">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" title="Logout" class="p-2 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-white transition-colors">
                    <i class="fa-solid fa-right-from-bracket text-base"></i>
                </button>
            </form>
        </div>

    </aside>
</div>
