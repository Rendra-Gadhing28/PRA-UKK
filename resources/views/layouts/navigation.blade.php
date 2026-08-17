<div x-data="{ scrolled: false, mobileMenu: false, profileDropdown: false, confirmLogout: false }"
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     class="relative">

    {{-- Main Navbar Container --}}
    <nav :class="scrolled ? 'top-2 h-14 bg-white/95 border-amber-500/30 shadow-lg' : 'top-4 h-16 bg-white/90 border-amber-500/20 shadow-md'"
         class="fixed left-1/2 -translate-x-1/2 w-[calc(100%-32px)] max-w-6xl backdrop-blur-md border rounded-full px-4 md:px-6 flex items-center justify-between z-50 transition-all duration-300 ease-out">
        
        {{-- Logo & Brand --}}
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 cursor-pointer shrink-0">
            <div :class="scrolled ? 'w-9 h-9' : 'w-10 h-10'"
                 class="rounded-full bg-gradient-to-br from-[#FF6B8A] via-[#FF8FA3] to-[#FFB6C1] p-0.5 flex items-center justify-center shadow-sm transition-all duration-300">
                <img src="{{ asset('logo/yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty" class="w-full h-full object-cover rounded-full bg-white">
            </div>
            <div class="flex flex-col">
                <span class="font-sans text-sm md:text-base font-bold tracking-wide bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] text-transparent bg-clip-text leading-tight">
                    Yalia Beauty
                </span>
                <span class="hidden sm:block text-[8px] uppercase tracking-widest text-[#FF6B8A] font-semibold">
                    Salon & Treatment
                </span>
            </div>
        </a>

        {{-- Desktop Navigation Menu --}}
        <div class="hidden md:flex items-center gap-6">
            <a href="{{ route('user.dashboard') }}" 
               class="text-xs font-semibold tracking-wide transition-all duration-200 hover:text-[#FF6B8A] {{ request()->routeIs('user.dashboard') ? 'text-[#FF6B8A]' : 'text-gray-800' }}">
                Dashboard
            </a>
            <a href="{{ route('user.bookings.list') }}" 
               class="text-xs font-semibold tracking-wide transition-all duration-200 hover:text-[#FF6B8A] {{ request()->routeIs('user.bookings.*') ? 'text-[#FF6B8A]' : 'text-gray-800' }}">
                My Bookings
            </a>
            <a href="{{ route('user.treatments.index') }}" 
               class="text-xs font-semibold tracking-wide transition-all duration-200 hover:text-[#FF6B8A] {{ request()->routeIs('user.treatments.*') ? 'text-[#FF6B8A]' : 'text-gray-800' }}">
                Treatments
            </a>
        </div>

        {{-- Right Action Buttons --}}
        <div class="flex items-center gap-3">
            {{-- User Profile Dropdown --}}
            <div class="relative" @click.away="profileDropdown = false">
                <button @click="profileDropdown = !profileDropdown" 
                        class="flex items-center gap-2 p-1 rounded-full border border-rose-300/40 hover:border-rose-400 transition-all bg-white/50">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=FF6B8A&color=fff' }}"
                         alt="Avatar" 
                         class="h-7 w-7 rounded-full object-cover">
                    <span class="hidden lg:inline text-xs font-semibold text-gray-700 pr-1 max-w-[100px] truncate">
                        {{ auth()->user()->name }}
                    </span>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="profileDropdown" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-3 w-48 rounded-2xl border border-rose-100 bg-white shadow-xl py-2 z-50 overflow-hidden">
                    <div class="px-4 py-2 border-b border-gray-100 mb-1">
                        <p class="text-xs font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-xs font-semibold text-gray-600 hover:text-[#FF6B8A] hover:bg-rose-50">Profile</a>
                    <button type="button" @click.prevent="confirmLogout = true; profileDropdown = false" class="w-full text-left px-4 py-2 text-xs font-bold text-rose-600 hover:bg-rose-50">
                        Log Out
                    </button>
                </div>
            </div>

            {{-- Book Now Button --}}
            <a href="{{ route('user.bookings.create') }}" 
               class="hidden md:inline-block py-2 px-5 text-[11px] font-bold uppercase tracking-wider rounded-full bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] text-white shadow-sm transition-all duration-300 hover:shadow-md">
                Book Now
            </a>

            {{-- Hamburger Button --}}
            <button class="md:hidden flex flex-col gap-1 w-8 h-8 p-1.5 bg-transparent border-none cursor-pointer items-center justify-center rounded-lg" 
                    @click="mobileMenu = !mobileMenu" aria-label="Menu Toggle">
                <span class="block w-5 h-0.5 bg-gray-800 rounded transition-all duration-300 ease-in-out" 
                      :class="mobileMenu ? 'rotate-45 translate-y-1.5' : ''"></span>
                <span class="block w-5 h-0.5 bg-gray-800 rounded transition-all duration-300 ease-in-out" 
                      :class="mobileMenu ? 'opacity-0' : ''"></span>
                <span class="block w-5 h-0.5 bg-gray-800 rounded transition-all duration-300 ease-in-out" 
                      :class="mobileMenu ? '-rotate-45 -translate-y-1.5' : ''"></span>
            </button>
        </div>
    </nav>

    {{-- Mobile Overlay --}}
    <div x-show="mobileMenu" x-cloak
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 z-40 md:hidden" 
         @click="mobileMenu = false">
    </div>

    {{-- Mobile Drawer --}}
    <div x-show="mobileMenu" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-5"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-5"
         class="fixed top-20 left-4 right-4 bg-white rounded-3xl p-6 z-50 flex flex-col gap-2 shadow-2xl md:hidden border border-rose-100">
        
        <div class="flex items-center gap-3 mb-2 pb-3 border-b border-gray-100">
            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=FF6B8A&color=fff' }}"
                 alt="Avatar" class="h-10 w-10 rounded-full object-cover">
            <div>
                <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                <a href="{{ route('profile.edit') }}" class="text-xs text-[#FF6B8A] font-semibold">Edit Profile</a>
            </div>
        </div>

        <a href="{{ route('user.dashboard') }}" class="block px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('user.dashboard') ? 'text-[#FF6B8A] bg-rose-50' : 'text-gray-800 hover:text-[#FF6B8A] hover:bg-rose-50' }}">
            Dashboard
        </a>
        <a href="{{ route('user.bookings.list') }}" class="block px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('user.bookings.*') ? 'text-[#FF6B8A] bg-rose-50' : 'text-gray-800 hover:text-[#FF6B8A] hover:bg-rose-50' }}">
            My Bookings
        </a>
        <a href="{{ route('user.treatments.index') }}" class="block px-4 py-3 text-sm font-semibold rounded-2xl transition-all duration-200 {{ request()->routeIs('user.treatments.*') ? 'text-[#FF6B8A] bg-rose-50' : 'text-gray-800 hover:text-[#FF6B8A] hover:bg-rose-50' }}">
            Treatments
        </a>

        <button type="button" @click="confirmLogout = true; mobileMenu = false" class="w-full text-left mt-2 px-4 py-3 text-sm font-bold text-rose-600 rounded-2xl hover:bg-rose-50 transition-colors">
            Log Out
        </button>

        <a href="{{ route('user.bookings.create') }}" class="block text-center mt-2 py-3 px-5 text-xs font-bold uppercase tracking-wider rounded-full bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] text-white shadow-sm">
            Book Now
        </a>
    </div>

    {{-- Logout Confirmation Modal --}}
    <div x-show="confirmLogout" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="confirmLogout = false"></div>
        <div class="relative bg-white rounded-3xl p-6 text-center max-w-sm w-full shadow-2xl z-10 border border-rose-100">
            <div class="w-14 h-14 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2 font-headline">Konfirmasi Keluar</h3>
            <p class="text-xs text-gray-500 mb-6">Apakah Anda yakin ingin keluar dari akun Yalia Beauty?</p>
            <div class="flex gap-3">
                <button type="button" @click="confirmLogout = false" class="flex-1 py-2.5 rounded-full border border-gray-200 text-gray-700 font-semibold text-xs hover:bg-gray-50">Batal</button>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-full bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-semibold text-xs shadow">Ya, Keluar</button>
                </form>
            </div>
        </div>
    </div>
</div>