<nav x-data="{ mobileOpen: false }" class="bg-surface/80 backdrop-blur-md sticky top-0 z-50 border-b border-border-subtle shadow-sm">
    <div class="flex justify-between items-center w-full px-margin-mobile md:px-margin-desktop py-4 max-w-container-max mx-auto">
        <a href="{{ route('user.dashboard') }}" class="text-headline-md font-headline-md font-bold text-primary shrink-0">
            Yalia Beauty
        </a>

        <div class="hidden md:flex gap-8 items-center">
            <a href="{{ route('user.dashboard') }}"
               class="text-label-lg font-label-lg pb-1 transition-colors {{ request()->routeIs('user.dashboard') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
                Dashboard
            </a>
            <a href="{{ route('user.bookings.list') }}"
               class="text-label-lg font-label-lg pb-1 transition-colors {{ request()->routeIs('user.bookings.*') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
                My Bookings
            </a>
            <a href="{{ route('user.treatments.index') }}"
               class="text-label-lg font-label-lg pb-1 transition-colors {{ request()->routeIs('user.treatments.*') ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary' }}">
                Treatments
            </a>
            <a href="#"
               class="text-label-lg font-label-lg pb-1 text-on-surface-variant hover:text-primary transition-colors">
                Membership
            </a>
        </div>

        <div class="flex gap-3 md:gap-4 items-center">
            {{-- Mockup punya tombol "Login" di sini, tapi halaman ini cuma bisa
                 diakses setelah login — jadi diganti avatar + dropdown profil. --}}
            <div class="relative hidden sm:block" x-data="{ open: false }" @click.outside="open = false">
                <button @click="open = !open" class="flex items-center gap-2 rounded-full px-2 py-1 hover:bg-surface-container-high transition-colors">
                    <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=D6336C&color=fff' }}"
                         alt="" class="h-9 w-9 rounded-full object-cover">
                    <span class="text-label-md font-label-md text-on-surface-variant">{{ auth()->user()->name }}</span>
                    <span class="material-symbols-outlined text-base text-on-surface-variant">expand_more</span>
                </button>
                <div x-show="open" x-cloak x-transition
                     class="absolute right-0 mt-2 w-48 rounded-xl border border-border-subtle bg-white shadow-lg py-2 z-50">
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-body-sm font-body-sm text-on-surface hover:bg-surface-container-high">Profile</a>
                    <a href="{{ route('user.bookings.list') }}" class="block px-4 py-2 text-body-sm font-body-sm text-on-surface hover:bg-surface-container-high">My Bookings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-body-sm font-body-sm text-error hover:bg-surface-container-high">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>

            <a href="{{ route('user.treatments.index') }}"
               class="bg-primary text-on-primary px-6 py-2.5 rounded-full text-button font-button hover:bg-secondary transition-all shadow-md active:scale-95">
                Book Now
            </a>

            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-on-surface-variant" aria-label="Toggle menu">
                <span class="material-symbols-outlined" x-text="mobileOpen ? 'close' : 'menu'"></span>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-border-subtle bg-surface px-margin-mobile py-4 space-y-1">
        <a href="{{ route('user.dashboard') }}" class="block py-2 text-label-lg font-label-lg text-on-surface-variant hover:text-primary">Dashboard</a>
        <a href="{{ route('user.bookings.list') }}" class="block py-2 text-label-lg font-label-lg text-on-surface-variant hover:text-primary">My Bookings</a>
        <a href="{{ route('user.treatments.index') }}" class="block py-2 text-label-lg font-label-lg text-on-surface-variant hover:text-primary">Treatments</a>
        <a href="{{ route('profile.edit') }}" class="block py-2 text-label-lg font-label-lg text-on-surface-variant hover:text-primary">Profile</a>
        <form method="POST" action="{{ route('logout') }}" class="pt-2">
            @csrf
            <button type="submit" class="block w-full text-left py-2 text-label-lg font-label-lg text-error">Log Out</button>
        </form>
    </div>
</nav>
