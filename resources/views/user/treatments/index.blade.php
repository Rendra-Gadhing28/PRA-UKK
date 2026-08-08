{{--
    Halaman daftar treatment.
    Asumsi: layout dasar (nav, footer, font, konfigurasi Tailwind) sudah
    didefinisikan di resources/views/layouts/app.blade.php sesuai struktur
    project di PRD. Sesuaikan nama @extends bila layout kamu berbeda.
--}}
@extends('layouts.app')

@section('title', 'Treatments | Yalia Beauty')

@section('content')
    <main
        x-data="treatmentBrowser({
            initialSearch: @js($currentSearch),
            initialCategory: @js($currentCategory),
            initialCursor: @js($treatments->nextCursor()?->encode()),
            initialHasMore: @js($treatments->hasMorePages()),
            searchUrl: @js(route('user.treatments.search')),
        })"
        class="pt-32 pb-section-gap px-4 md:px-margin-desktop max-w-container-max mx-auto"
    >
        {{-- Header --}}
        <header class="text-center mb-16">
            <h1 class="font-headline-xl text-headline-xl text-text-heading mb-4">Our Signature Treatments</h1>
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto italic">
                Indulge in a world of refined beauty where every treatment is a ritual of rejuvenation.
                Experience professional care tailored to your unique essence.
            </p>
        </header>

        {{-- Filter Bar --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6 bg-surface-container-low p-4 rounded-[32px] luxury-shadow border border-border-subtle">
            <div class="flex flex-wrap gap-2 justify-center">
                <button
                    type="button"
                    @click="setCategory('all')"
                    :class="category === 'all' ? 'bg-primary text-on-primary' : 'bg-white text-on-surface-variant border border-border-subtle hover:bg-accent-clear'"
                    class="px-6 py-2 rounded-full font-label-lg text-label-lg transition-all"
                >All</button>
                @foreach ($categories as $cat)
                    <button
                        type="button"
                        @click="setCategory('{{ $cat->slug }}')"
                        :class="category === '{{ $cat->slug }}' ? 'bg-primary text-on-primary' : 'bg-white text-on-surface-variant border border-border-subtle hover:bg-accent-clear'"
                        class="px-6 py-2 rounded-full font-label-lg text-label-lg transition-all"
                    >{{ $cat->name }}</button>
                @endforeach
            </div>
            <div class="relative w-full md:w-80">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input
                    type="text"
                    x-model="search"
                    @input.debounce.400ms="runSearch()"
                    maxlength="100"
                    placeholder="Search treatments..."
                    class="w-full pl-12 pr-6 py-3 rounded-full bg-white border border-border-subtle focus:border-primary focus:ring-0 font-body-sm text-body-sm transition-all outline-none"
                />
                <template x-if="loading">
                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary animate-spin text-[18px]">progress_activity</span>
                </template>
            </div>
        </div>

        {{-- Treatment Grid --}}
        <div id="treatment-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @include('user.treatments.partials.grid', ['treatments' => $treatments])
        </div>

        {{-- Custom Pagination: cursor-based "Load More" --}}
        <div class="mt-16 text-center" x-show="hasMore" x-cloak>
            <button
                type="button"
                @click="loadMore()"
                :disabled="loading"
                class="px-12 py-4 rounded-full border-2 border-primary text-primary font-button text-button hover:bg-primary hover:text-on-primary transition-all active:scale-95 disabled:opacity-50"
            >
                <span x-show="!loading">Load More Treatments</span>
                <span x-show="loading">Loading...</span>
            </button>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        /**
         * Komponen Alpine untuk pencarian & filter treatment secara real-time.
         *
         * - `search()` dipanggil dengan debounce 400ms (lihat @input.debounce.400ms
         *   di input di atas) agar tidak mengirim request ke server di setiap
         *   ketukan tombol, melainkan hanya setelah user berhenti mengetik.
         * - Setiap perubahan search/kategori mereset cursor (mulai dari halaman 1).
         * - `loadMore()` mengirim cursor halaman berikutnya (custom cursor-based
         *   pagination), meng-append hasil ke grid yang sudah ada.
         */
        function treatmentBrowser({ initialSearch, initialCategory, initialCursor, initialHasMore, searchUrl }) {
            return {
                search: initialSearch ?? '',
                category: initialCategory ?? 'all',
                cursor: null,
                nextCursor: initialCursor,
                hasMore: initialHasMore,
                loading: false,
                requestToken: 0,

                setCategory(slug) {
                    this.category = slug;
                    this.runSearch();
                },

                async runSearch() {
                    this.cursor = null;
                    await this.fetchResults({ append: false });
                },

                async loadMore() {
                    if (!this.hasMore || this.loading) return;
                    this.cursor = this.nextCursor;
                    await this.fetchResults({ append: true });
                },

                async fetchResults({ append }) {
                    const token = ++this.requestToken;
                    this.loading = true;

                    const params = new URLSearchParams();
                    if (this.search) params.set('q', this.search);
                    if (this.category && this.category !== 'all') params.set('category', this.category);
                    if (this.cursor) params.set('cursor', this.cursor);

                    try {
                        const response = await fetch(`${searchUrl}?${params.toString()}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        });

                        if (!response.ok) throw new Error('Request failed');

                        const data = await response.json();

                        // Abaikan response basi (misal user mengetik cepat, request lama tiba belakangan)
                        if (token !== this.requestToken) return;

                        const grid = document.getElementById('treatment-grid');
                        grid.innerHTML = append ? grid.innerHTML + data.html : data.html;

                        this.nextCursor = data.next_cursor;
                        this.hasMore = data.has_more;
                    } catch (error) {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: { type: 'error', message: 'Gagal memuat data treatment. Coba lagi.' },
                        }));
                    } finally {
                        if (token === this.requestToken) this.loading = false;
                    }
                },
            };
        }
    </script>
@endpush