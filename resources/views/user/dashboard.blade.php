<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard
        </h2>
    </x-slot>

    {{-- Ornamen background — fixed supaya tidak ikut scroll, z-index negatif supaya tidak
         mengganggu klik di konten. Persis seperti .blob-bg di mockup asli. --}}
    <div class="blob-bg bg-accent-clear w-[500px] h-[500px] -top-24 -left-24" aria-hidden="true"></div>
    <div class="blob-bg bg-primary-fixed w-[400px] h-[400px] top-1/2 -right-24" aria-hidden="true"></div>

    <div x-data="bookingTabs('{{ route('user.bookings.list') }}')">

        <main class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-12">

            {{-- Hero + Membership --}}
            <section class="flex flex-col lg:flex-row gap-8 items-start mb-16">
                <div class="flex-1 space-y-4">
                    <h1 class="text-headline-lg-mobile md:text-headline-lg font-headline-lg text-text-heading">
                        {{ now()->format('H') < 11 ? 'Good Morning' : (now()->format('H') < 17 ? 'Good Afternoon' : 'Good Evening') }},
                        <span class="text-primary italic">{{ explode(' ', $user->name)[0] }}</span>
                    </h1>
                    <p class="text-body-lg font-body-lg text-on-surface-variant max-w-xl">
                        @if($stats['upcoming_count'] > 0)
                            Ready for your next glow-up? You have {{ $stats['upcoming_count'] }}
                            {{ Str::plural('appointment', $stats['upcoming_count']) }} scheduled.
                        @else
                            Ready for your next glow-up? Book your next treatment now.
                        @endif
                    </p>
                </div>

                {{-- Kartu Membership --}}
                <div class="w-full lg:w-[400px] glass-card p-6 rounded-2xl shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-primary"></div>
                    <div class="flex justify-between items-center mb-6">
                        <div class="flex flex-col">
                            <span class="text-label-md font-label-md uppercase tracking-wider text-tertiary">Current Status</span>
                            <span class="text-headline-sm font-headline-sm text-text-heading">{{ ucfirst($membership['current']) }} Member</span>
                        </div>
                        <span class="material-symbols-outlined text-4xl text-primary" style="font-variation-settings: 'FILL' 1;">stars</span>
                    </div>

                    <div class="space-y-2">
                        @if($membership['next'])
                            <div class="flex justify-between text-label-md font-label-md text-on-surface-variant">
                                <span>Progress to {{ ucfirst($membership['next']) }}</span>
                                <span>{{ number_format($user->total_points) }} / {{ number_format($membership['next_min']) }} pts</span>
                            </div>
                            <div class="w-full h-3 bg-surface-container-highest rounded-full overflow-hidden">
                                <div class="h-full bg-primary transition-all duration-1000" style="width: {{ $membership['percent'] }}%"></div>
                            </div>
                            <p class="text-body-sm font-body-sm text-on-surface-variant pt-2">
                                Earn {{ number_format($membership['points_needed']) }} more points to unlock {{ $membership['next'] }} benefits!
                            </p>
                        @else
                            <p class="text-body-sm font-body-sm text-on-surface-variant pt-2">
                                You've reached our highest tier. Thank you for your loyalty! 🌟
                            </p>
                        @endif
                    </div>
                </div>
            </section>

            {{-- Stats Row --}}
            <section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-16">
                <div class="bg-surface-light p-8 rounded-2xl border border-border-subtle shadow-sm hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-accent-clear flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined">event_available</span>
                        </div>
                        <span class="text-label-lg font-label-lg text-on-surface-variant">Total Bookings</span>
                    </div>
                    <div class="text-headline-md font-headline-md">{{ $stats['total_bookings'] }}</div>
                </div>

                <div class="bg-surface-light p-8 rounded-2xl border border-border-subtle shadow-sm hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-surface-container-high flex items-center justify-center text-secondary">
                            <span class="material-symbols-outlined">loyalty</span>
                        </div>
                        <span class="text-label-lg font-label-lg text-on-surface-variant">Loyalty Points</span>
                    </div>
                    <div class="text-headline-md font-headline-md">{{ number_format($user->total_points) }}</div>
                </div>

                <div class="bg-surface-light p-8 rounded-2xl border border-border-subtle shadow-sm hover:-translate-y-1 transition-transform duration-300">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-full bg-tertiary-fixed flex items-center justify-center text-tertiary">
                            <span class="material-symbols-outlined">favorite</span>
                        </div>
                        <span class="text-label-lg font-label-lg text-on-surface-variant">Upcoming</span>
                    </div>
                    <div class="text-headline-md font-headline-md">{{ $stats['upcoming_count'] }}</div>
                </div>
            </section>

            {{-- Booking Management --}}
            <section>
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 border-b border-border-subtle">
                    <div class="flex gap-8 overflow-x-auto no-scrollbar">
                        <template x-for="t in tabs" :key="t">
                            <button
                                @click="switchTab(t)"
                                class="pb-4 text-label-lg font-label-lg whitespace-nowrap transition-colors"
                                :class="activeTab === t ? 'text-primary border-b-2 border-primary' : 'text-on-surface-variant hover:text-primary'"
                                x-text="t.charAt(0).toUpperCase() + t.slice(1)">
                            </button>
                        </template>
                    </div>

                    {{-- Sort control — sama seperti mockup ("Sort by: Date"). Toggle
                         asc/desc dan ikut dikirim sebagai query param ke endpoint list,
                         supaya bukan cuma dekorasi. --}}
                    <div class="pb-4">
                        <button
                            type="button"
                            @click="toggleSort()"
                            class="flex items-center gap-2 text-label-md font-label-md text-on-surface-variant hover:text-primary transition-colors"
                        >
                            <span class="material-symbols-outlined text-sm">filter_list</span>
                            <span x-text="sort === 'asc' ? 'Sort by: Date (Oldest)' : 'Sort by: Date (Newest)'"></span>
                        </button>
                    </div>
                </div>

                <div id="booking-list-container" aria-live="polite">
                    @include('user.bookings.BookingList', ['bookings' => $upcomingBookings, 'tab' => 'upcoming', 'paginated' => false])
                </div>
            </section>
        </main>

        {{-- Footer — sama seperti mockup. Kalau mau tampil di semua halaman
             (bukan cuma dashboard), pindahkan block ini ke x-app-layout /
             resources/views/layouts/app.blade.php sekali saja. --}}
        @include('layouts.footer')
    </div>

    <script>
        function bookingTabs(listUrl) {
            return {
                tabs: ['upcoming', 'past', 'cancelled'],
                activeTab: 'upcoming',
                sort: 'desc',
                loading: false,

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

                    try {
                        const response = await fetch(`${listUrl}?tab=${this.activeTab}&sort=${this.sort}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        });

                        if (!response.ok) throw new Error('Gagal memuat data');

                        document.getElementById('booking-list-container').innerHTML = await response.text();
                    } catch (e) {
                        document.getElementById('booking-list-container').innerHTML =
                            '<p class="text-center py-12 text-on-surface-variant">Gagal memuat data. Coba lagi.</p>';
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
