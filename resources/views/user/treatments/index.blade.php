<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Treatments & Services') }}
        </h2>
    </x-slot>

    <div class="bg-gray-50 min-h-screen py-20">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                    Discover Our Premium <span class="text-amber-600">Treatments</span>
                </h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">
                    Experience luxury and relaxation with our curated selection of beauty services.
                </p>
                
                <!-- Search & Filter Form -->
                <form action="{{ route('user.treatments.index') }}" method="GET" class="mt-8 max-w-2xl mx-auto">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="h-5 w-5 text-gray-400"></i>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3"
                                placeholder="Search treatments (e.g., Facial, Hair Spa)...">
                        </div>
                        
                        <div class="flex-shrink-0">
                            <select name="category" class="block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md">
                                <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>All Services</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <button type="submit" class="inline-flex justify-center items-center py-3 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            Search
                        </button>
                    </div>
                </form>
            </div>

            <!-- Treatments Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($treatments as $treatment)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group transform hover:-translate-y-1">
                    <div class="relative h-48 w-full bg-gray-200 overflow-hidden">
                        @if($treatment->images)
                            <img src="{{ Storage::url($treatment->images) }}" alt="{{ $treatment->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-100 to-amber-50">
                                <i data-lucide="sparkles" class="h-12 w-12 text-amber-300"></i>
                            </div>
                        @endif
                        
                        <div class="absolute top-4 right-4 flex flex-col gap-2">
                            @if($treatment->badge && $treatment->badge !== 'none')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm {{ $treatment->badge === 'best_seller' ? 'bg-amber-500 text-white' : ($treatment->badge === 'new' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white') }}">
                                    {{ str_replace('_', ' ', strtoupper($treatment->badge)) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-6 flex flex-col h-full">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">
                                {{ $treatment->name }}
                            </h3>
                            @if($treatment->rating_avg > 0)
                                <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-500 shrink-0 ml-2">
                                    <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                                    {{ number_format($treatment->rating, 1) }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full shrink-0 ml-2">
                                    New
                                </span>
                            @endif
                        </div>
                        
                        <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-grow">
                            {{ $treatment->description }}
                        </p>
                        
                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-6">
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="clock" class="h-4 w-4 text-amber-500"></i>
                                <span>{{ $treatment->duration_minutes }} min</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <i data-lucide="tag" class="h-4 w-4 text-amber-500"></i>
                                <span>{{ $treatment->category->name }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-auto">
    <span class="text-xl font-bold text-gray-900">
        Rp {{ number_format($treatment->price, 0, ',', '.') }}
    </span>
    
    {{-- Tombol Book Now yang membawa data treatment_id ke halaman form --}}
    <a href="{{ route('user.bookings.create', ['treatment_id' => $treatment->id]) }}" 
        class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-[11px] font-bold rounded-full text-white bg-gradient-to-br from-[#FF6B8A] to-[#E91E63] shadow-[0_4px_15px_rgba(233,30,99,0.3)] hover:-translate-y-[1px] hover:shadow-[0_6px_20px_rgba(233,30,99,0.4)] transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#FF6B8A] uppercase tracking-[0.5px]">
        Book Now
    </a>
</div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 mb-4">
                        <i data-lucide="search-x" class="h-8 w-8 text-amber-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No treatments found</h3>
                    <p class="mt-2 text-sm text-gray-500">We couldn't find anything matching your criteria. Try adjusting your filters.</p>
                    <a href="{{ route('user.treatments.index') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-amber-700 bg-amber-100 hover:bg-amber-200">
                        Clear Filters
                    </a>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $treatments->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
