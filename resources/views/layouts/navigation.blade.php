<div x-data="{ scrolled: false, mobileMenu: false, profileDropdown: false }"
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     class="relative">

    {{-- Main Navbar Container --}}
    <nav :class="scrolled ? 'top-2 h-[60px] bg-white/95 border-[#D4AF37]/30 shadow-[0_8px_32px_rgba(0,0,0,0.12),0_2px_8px_rgba(0,0,0,0.06)]' : 'top-4 h-[68px] bg-white/90 border-[#D4AF37]/20 shadow-[0_4px_24px_rgba(0,0,0,0.08),0_1px_3px_rgba(0,0,0,0.05)]'"
         class="fixed left-1/2 -translate-x-1/2 w-[calc(100%-32px)] max-w-[1200px] backdrop-blur-md border rounded-[40px] px-4 md:px-6 flex items-center justify-between z-[500] transition-all duration-300 ease-out">
        
        {{-- Logo & Brand --}}
        <a href="{{ route('user.dashboard') }}" class="flex items-center gap-2.5 cursor-pointer shrink-0">
            <div :class="scrolled ? 'w-[38px] h-[38px]' : 'w-[42px] h-[42px]'"
                 class="rounded-full bg-gradient-to-br from-[#FF6B8A] via-[#FF8FA3] to-[#FFB6C1] p-[2px] flex items-center justify-center shadow-[0_2px_12px_rgba(255,107,138,0.3),0_1px_3px_rgba(0,0,0,0.1)] transition-all duration-300">
                <img src="{{ asset('logo/'.'yalia-logos-trnsprnt.svg') }}" alt="Yalia Beauty" class="w-full h-full object-cover rounded-full bg-white">
            </div>
            <div class="flex flex-col">
                <span class="font-sans text-[14px] md:text-[16px] font-bold tracking-[0.5px] bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] text-transparent bg-clip-text leading-[1.2]">
                    Yalia Beauty
                </span>
                <span class="hidden sm:block text-[8px] uppercase tracking-[2px] text-[#FF6B8A] mt-[1px] font-semibold">
                    Salon & Treatment
                </span>
            </div>
        </a>

        {{-- Desktop Navigation Menu --}}
        <ul class="hidden md:flex items-center gap-4 m-0 p-0 list-none">
            <li>
                <a href="{{ route('user.dashboard') }}" 
                   class="text-[12px] py-1.5 px-3 rounded-full transition-all duration-300 whitespace-nowrap {{ request()->routeIs('user.dashboard') ? 'text-[#FF6B8A] bg-[#FF6B8A]/12 font-bold' : 'text-[#666] font-semibold hover:text-[#FF6B8A] hover:bg-[#FF6B8A]/10' }}">
                    Dashboard
                </a>
            </li>
            <li>
                <a href="{{ route('user.bookings.index') }}" 
                   class="text-[12px] py-1.5 px-3 rounded-full transition-all duration-300 whitespace-nowrap {{ request()->routeIs('user.bookings.*') ? 'text-[#FF6B8A] bg-[#FF6B8A]/12 font-bold' : 'text-[#666] font-semibold hover:text-[#FF6B8A] hover:bg-[#FF6B8A]/10' }}">
                    My Bookings
                </a>
            </li>
            <li>
                <a href="{{ route('user.treatments.index') }}" 
                   class="text-[12px] py-1.5 px-3 rounded-full transition-all duration-300 whitespace-nowrap {{ request()->routeIs('user.treatments.*') ? 'text-[#FF6B8A] bg-[#FF6B8A]/12 font-bold' : 'text-[#666] font-semibold hover:text-[#FF6B8A] hover:bg-[#FF6B8A]/10' }}">
                    Treatments
                </a>
            </li>
        </ul>



        {{-- Right Actions (Profile & CTA) --}}
        <div class="flex gap-3 md:gap-4 items-center shrink-0">
            
            {{-- Profile Dropdown (Desktop only, similar to your old Blade) --}}
            <div class="relative hidden sm:block" @click.outside="profileDropdown = false">
                <button @click="profileDropdown = !profileDropdown" class="flex items-center gap-2 rounded-full p-1 hover:bg-[#FF6B8A]/5 transition-colors">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=FF6B8A&color=fff' }}"
                         alt="Avatar" class="h-8 w-8 rounded-full object-cover border border-[#FF6B8A]/20">
                </button>
                
                <div x-show="profileDropdown" x-cloak 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute right-0 mt-3 w-48 rounded-[16px] border border-[#FF6B8A]/10 bg-white shadow-xl py-2 z-50 overflow-hidden">
                    <div class="px-4 py-2 border-b border-gray-100 mb-1">
                        <p class="text-[12px] font-bold text-gray-800">{{ auth()->user()->name }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-[12px] font-semibold text-[#666] hover:text-[#FF6B8A] hover:bg-[#FF6B8A]/5">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-[12px] font-bold text-red-500 hover:bg-red-50">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

            {{-- Book Now Button --}}
            <a href="{{ route('user.treatments.index') }}" 
               class="hidden md:inline-block py-2 px-5 text-[11px] font-bold uppercase tracking-[0.5px] rounded-full bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] text-white shadow-[0_4px_15px_rgba(233,30,99,0.3)] transition-all duration-300 hover:-translate-y-[1px] hover:shadow-[0_6px_20px_rgba(233,30,99,0.4)]">
                Book Now
            </a>

            {{-- Hamburger Button --}}
            <button class="md:hidden flex flex-col gap-[5px] w-8 h-8 p-1.5 bg-transparent border-none cursor-pointer items-center justify-center rounded-lg" 
                    @click="mobileMenu = !mobileMenu" aria-label="Menu Toggle">
                <span class="block w-[20px] h-[2px] bg-[#333] rounded-[2px] transition-all duration-300 ease-in-out" 
                      :class="mobileMenu ? 'rotate-45 translate-y-[7px]' : ''"></span>
                <span class="block w-[20px] h-[2px] bg-[#333] rounded-[2px] transition-all duration-300 ease-in-out" 
                      :class="mobileMenu ? 'opacity-0' : ''"></span>
                <span class="block w-[20px] h-[2px] bg-[#333] rounded-[2px] transition-all duration-300 ease-in-out" 
                      :class="mobileMenu ? '-rotate-45 -translate-y-[7px]' : ''"></span>
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
         class="fixed inset-0 bg-black/50 z-[400] md:hidden" 
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
         class="fixed top-[90px] left-4 right-4 bg-white rounded-[24px] p-5 z-[450] flex flex-col gap-2 shadow-[0_20px_60px_rgba(0,0,0,0.3)] md:hidden">
        
        <div class="flex items-center gap-3 mb-2 pb-3 border-b border-gray-100">
            <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=FF6B8A&color=fff' }}"
                 alt="Avatar" class="h-10 w-10 rounded-full object-cover">
            <div>
                <p class="text-[14px] font-bold text-gray-800">{{ auth()->user()->name }}</p>
                <a href="{{ route('profile.edit') }}" class="text-[11px] text-[#FF6B8A] font-semibold">Edit Profile</a>
            </div>
        </div>

        <a href="{{ route('user.dashboard') }}" class="block px-4 py-3 text-[14px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('user.dashboard') ? 'text-[#FF6B8A] bg-[#FF6B8A]/10' : 'text-[#333] hover:text-[#FF6B8A] hover:bg-[#FF6B8A]/10' }}">
            Dashboard
        </a>
        <a href="{{ route('user.bookings.list') }}" class="block px-4 py-3 text-[14px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('user.bookings.*') ? 'text-[#FF6B8A] bg-[#FF6B8A]/10' : 'text-[#333] hover:text-[#FF6B8A] hover:bg-[#FF6B8A]/10' }}">
            My Bookings
        </a>
        <a href="{{ route('user.treatments.index') }}" class="block px-4 py-3 text-[14px] font-semibold rounded-xl transition-all duration-200 {{ request()->routeIs('user.treatments.*') ? 'text-[#FF6B8A] bg-[#FF6B8A]/10' : 'text-[#333] hover:text-[#FF6B8A] hover:bg-[#FF6B8A]/10' }}">
            Treatments
        </a>

        <form method="POST" action="{{ route('logout') }}" class="mt-2">
            @csrf
            <button type="submit" class="w-full px-4 py-3 text-left text-[14px] font-bold text-red-500 rounded-xl hover:bg-red-50 transition-colors">
                Log Out
            </button>
        </form>

        <a href="{{ route('user.treatments.index') }}" class="block text-center mt-2 py-3 px-5 text-[13px] font-bold uppercase tracking-[0.5px] rounded-[12px] bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] text-white shadow-[0_4px_15px_rgba(233,30,99,0.3)]">
            Book Now
        </a>
    </div>
</div>