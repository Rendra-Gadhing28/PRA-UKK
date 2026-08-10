<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Treatments & Services') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen" x-data="treatmentsPage()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    Discover Our Premium <span class="text-amber-600">Treatments</span>
                </h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Experience luxury and relaxation with our curated selection of beauty services.
                </p>
                
                <!-- Search & Filter -->
                <div class="mt-8 flex justify-center max-w-lg mx-auto">
                    <div class="relative w-full shadow-sm rounded-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                        </div>
                        <input type="text" x-model="searchQuery" @input.debounce.500ms="fetchTreatments"
                            class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3"
                            placeholder="Search treatments (e.g., Facial, Hair Spa)...">
                    </div>
                </div>

                <!-- Category Pills -->
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <button @click="setCategory('all')" 
                        :class="category === 'all' ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-full text-sm font-medium border border-gray-200 transition-colors shadow-sm">
                        All Services
                    </button>
                    @foreach($categories as $cat)
                    <button @click="setCategory('{{ $cat->slug }}')"
                        :class="category === '{{ $cat->slug }}' ? 'bg-amber-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'"
                        class="px-4 py-2 rounded-full text-sm font-medium border border-gray-200 transition-colors shadow-sm">
                        {{ $cat->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Treatments Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="treatments-grid">
                @include('user.treatments.partials.grid', ['treatments' => $treatments])
            </div>

            <!-- Loading State -->
            <div x-show="isLoading" class="mt-12 flex justify-center" style="display: none;">
                <svg class="animate-spin h-8 w-8 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Load More -->
            <div class="mt-12 text-center" x-show="hasMore">
                <button @click="loadMore"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-amber-700 bg-amber-100 hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                    Load More
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('treatmentsPage', () => ({
                searchQuery: '{{ $currentSearch ?? '' }}',
                category: '{{ $currentCategory ?? 'all' }}',
                nextCursor: '{{ $treatments->nextCursor()?->encode() ?? '' }}',
                hasMore: {{ $treatments->hasMorePages() ? 'true' : 'false' }},
                isLoading: false,

                setCategory(slug) {
                    this.category = slug;
                    this.nextCursor = '';
                    this.fetchTreatments();
                },

                async fetchTreatments(append = false) {
                    this.isLoading = true;
                    
                    try {
                        const url = new URL('{{ route('user.treatments.search') }}');
                        if (this.searchQuery) url.searchParams.append('search', this.searchQuery);
                        if (this.category !== 'all') url.searchParams.append('category', this.category);
                        if (append && this.nextCursor) url.searchParams.append('cursor', this.nextCursor);

                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        const grid = document.getElementById('treatments-grid');
                        if (append) {
                            grid.insertAdjacentHTML('beforeend', data.html);
                        } else {
                            grid.innerHTML = data.html;
                        }

                        this.nextCursor = data.next_cursor || '';
                        this.hasMore = data.has_more;
                    } catch (error) {
                        console.error('Error fetching treatments:', error);
                    } finally {
                        this.isLoading = false;
                        if (window.lucide) {
                            lucide.createIcons();
                        }
                    }
                },

                loadMore() {
                    this.fetchTreatments(true);
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
