@forelse($treatments as $treatment)
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 group transform hover:-translate-y-1">
    <div class="relative h-48 w-full bg-gray-200 overflow-hidden">
        @if($treatment->image)
            <img src="{{ Storage::url($treatment->image) }}" alt="{{ $treatment->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
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
    
    <div class="p-6">
        <div class="flex justify-between items-start mb-2">
            <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition-colors">
                {{ $treatment->name }}
            </h3>
            <span class="inline-flex items-center gap-1 text-sm font-medium text-amber-500">
                <i data-lucide="star" class="h-4 w-4 fill-current"></i>
                {{ number_format($treatment->rating_avg, 1) }}
            </span>
        </div>
        
        <p class="text-sm text-gray-500 line-clamp-2 mb-4">
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
            <a href="{{ route('user.bookings.create', ['treatment' => $treatment->id]) }}" 
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-amber-600 hover:bg-amber-700 shadow-md hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                Book Now
            </a>
        </div>
    </div>
</div>
@empty
<div class="col-span-full text-center py-12">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 mb-4">
        <i data-lucide="search-x" class="h-8 w-8 text-amber-600"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900">No treatments found</h3>
    <p class="mt-2 text-sm text-gray-500">We couldn't find anything matching your criteria. Try adjusting your filters.</p>
</div>
@endforelse
