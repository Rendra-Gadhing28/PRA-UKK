<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Treatments & Services') }}
        </h2>
    </x-slot>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── Design tokens ── */
        :root {
            --yalia-pink:       #f45472;
            --yalia-pink-soft:  #ff8fa4;
            --yalia-pink-pale:  #ffeef2;
            --yalia-rose-dark:  #b5294a;
            --yalia-rose-mid:   #d94060;
            --yalia-brown:      #5b3a29;
            --yalia-bg:         #fdf5f6;
            --yalia-surface:    #fff8f9;
        }

        body { font-family: 'Work Sans', sans-serif; }

        .yalia-heading {
            font-family: 'Playfair Display', serif;
            color: var(--yalia-brown);
        }

        /* ── Search & filter bar ── */
        .yalia-input {
            border: 1.5px solid #f9c5cf;
            border-radius: 12px;
            padding: 0.65rem 1rem 0.65rem 2.75rem;
            font-family: 'Work Sans', sans-serif;
            font-size: 0.875rem;
            background: var(--yalia-surface);
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .yalia-input:focus {
            border-color: var(--yalia-pink);
            box-shadow: 0 0 0 3px rgba(244,84,114,.12);
        }

        .yalia-select {
            border: 1.5px solid #f9c5cf;
            border-radius: 12px;
            padding: 0.65rem 2.5rem 0.65rem 1rem;
            font-family: 'Work Sans', sans-serif;
            font-size: 0.875rem;
            background: var(--yalia-surface);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%23f45472' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .yalia-select:focus {
            border-color: var(--yalia-pink);
            box-shadow: 0 0 0 3px rgba(244,84,114,.12);
        }

        .yalia-search-btn {
            background: linear-gradient(135deg, var(--yalia-pink), var(--yalia-rose-mid));
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.65rem 1.5rem;
            font-family: 'Work Sans', sans-serif;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(244,84,114,.3);
            transition: transform .2s, box-shadow .2s;
        }
        .yalia-search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(244,84,114,.4);
        }

        /* ── Treatment card ── */
        .treatment-card {
            background: var(--yalia-surface);
            border: 1px solid #fce4e9;
            border-radius: 24px;
            overflow: hidden;
            transition: transform .3s, box-shadow .3s;
            display: flex;
            flex-direction: column;
        }
        .treatment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(180,40,70,.12);
        }

        .card-img-wrapper {
            position: relative;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, #fce4e9, #fff0f3);
        }
        .card-img-wrapper img {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .5s;
        }
        .treatment-card:hover .card-img-wrapper img { transform: scale(1.07); }

        /* badge overlay (best seller, new, promo) */
        .overlay-badge {
            position: absolute;
            top: 12px; right: 12px;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .badge-best-seller { background: #b5294a; color: #fff; }
        .badge-new         { background: #14a879; color: #fff; }
        .badge-promo       { background: var(--yalia-pink); color: #fff; }

        /* category pill */
        .category-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--yalia-pink-pale);
            color: var(--yalia-rose-dark);
            border: 1px solid #fac8d2;
            border-radius: 999px;
            padding: 3px 10px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: .3px;
        }

        /* rating chip */
        .rating-chip {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--yalia-rose-dark);
        }
        .new-chip {
            background: #f3f4f6;
            color: #9ca3af;
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 0.72rem;
            font-weight: 600;
        }

        /* Book Now button */
        .btn-book {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, var(--yalia-pink), var(--yalia-rose-mid));
            color: #fff;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(244,84,114,.35);
            transition: transform .2s, box-shadow .2s;
            white-space: nowrap;
        }
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 22px rgba(244,84,114,.45);
        }

        /* price text */
        .price-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--yalia-brown);
        }

        /* ── Empty state ── */
        .empty-state {
            background: var(--yalia-surface);
            border: 1.5px dashed #fac8d2;
            border-radius: 24px;
            padding: 3rem 1.5rem;
            text-align: center;
        }

        /* ── Pagination ── */
        .yalia-pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        /* override Laravel's default pagination links */
        .yalia-pagination nav { width: 100%; }
        .yalia-pagination nav > div:first-child { display: none; } /* hide "Showing X to Y" */
        .yalia-pagination nav > div:last-child {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        /* all anchor/span tags inside pagination */
        .yalia-pagination span[aria-current="page"] > span,
        .yalia-pagination span > span,
        .yalia-pagination a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border-radius: 10px;
            font-family: 'Work Sans', sans-serif;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
            border: 1.5px solid transparent;
        }

        /* inactive page numbers */
        .yalia-pagination a {
            color: var(--yalia-rose-dark);
            background: var(--yalia-pink-pale);
            border-color: #fac8d2;
        }
        .yalia-pagination a:hover {
            background: var(--yalia-pink);
            color: #fff;
            border-color: var(--yalia-pink);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(244,84,114,.3);
        }

        /* active page */
        .yalia-pagination span[aria-current="page"] > span {
            background: linear-gradient(135deg, var(--yalia-pink), var(--yalia-rose-mid));
            color: #fff;
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(244,84,114,.35);
        }

        /* disabled (prev/next when at edge) */
        .yalia-pagination span > span {
            color: #d1a3ac;
            background: #fdf0f2;
            border-color: #fce4e9;
            cursor: not-allowed;
        }

        /* dots */
        .yalia-pagination span.pagination-dots > span {
            background: transparent;
            border-color: transparent;
            color: #c47d8e;
            cursor: default;
        }
    </style>

    <div style="background: var(--yalia-bg); min-height: 100vh; padding: 5rem 0 4rem;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ── Page header ── --}}
            <div class="text-center mb-40 mt-10">
                <p class="text-xs font-semibold tracking-widest uppercase mb-3" style="color: var(--yalia-pink);">
                    Our Services
                </p>
                <h1 class="yalia-heading text-4xl sm:text-5xl font-bold leading-tight mb-4">
                    Discover Premium <br class="hidden sm:block">
                    <span style="color: var(--yalia-pink);">Treatments</span>
                </h1>
                <p class="max-w-xl mx-auto text-base" style="color: #9b6374; font-family: 'Work Sans', sans-serif;">
                    Experience luxury and relaxation with our curated selection of beauty services.
                </p>

                {{-- ── React Search & filter bar ── --}}
                <div id="react-user-treatment-filter"
                     data-categories='@json($categories)'
                     data-initial-category="{{ request('category', 'all') }}"
                     data-initial-search="{{ request('search', '') }}"
                     data-action-url="{{ route('user.treatments.index') }}">
                    
                    {{-- Fallback form --}}
                    <form action="{{ route('user.treatments.index') }}" method="GET"
                          class="mt-8 max-w-2xl mx-auto flex flex-col sm:flex-row gap-3">
                        <div class="relative flex-grow">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   class="yalia-input w-full"
                                   placeholder="Search treatments...">
                        </div>
                        <div class="flex-shrink-0">
                            <select name="category" class="yalia-select w-full sm:w-auto">
                                <option value="all" {{ request('category', 'all') === 'all' ? 'selected' : '' }}>
                                    All Services
                                </option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->slug }}"
                                        {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="yalia-search-btn flex-shrink-0">
                            Search
                        </button>
                    </form>
                </div>
            </div>

            {{-- ── Active filter pill ── --}}
            @if(request('search') || (request('category') && request('category') !== 'all'))
                <div class="flex items-center gap-2 mb-6 flex-wrap">
                    <span class="text-xs font-medium" style="color:#9b6374;">Filtered by:</span>
                    @if(request('search'))
                        <span class="category-pill">
                            <i data-lucide="search" class="h-3 w-3"></i>
                            {{ request('search') }}
                        </span>
                    @endif
                    @if(request('category') && request('category') !== 'all')
                        <span class="category-pill">
                            <i data-lucide="layers" class="h-3 w-3"></i>
                            {{ $categories->firstWhere('slug', request('category'))?->name }}
                        </span>
                    @endif
                    <a href="{{ route('user.treatments.index') }}"
                       class="text-xs font-semibold underline"
                       style="color: var(--yalia-rose-dark);">Clear</a>
                </div>
            @endif

            {{-- ── Treatments grid ── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse($treatments as $treatment)
                <div class="treatment-card">

                    {{-- Image --}}
                    <div class="card-img-wrapper">
                        <img src="{{ $treatment->image_url }}"
                             alt="{{ $treatment->name }}">

                        {{-- Overlay badge --}}
                        @if($treatment->badge && $treatment->badge !== 'none')
                            <span class="overlay-badge
                                {{ $treatment->badge === 'best_seller' ? 'badge-best-seller' :
                                   ($treatment->badge === 'new' ? 'badge-new' : 'badge-promo') }}">
                                {{ str_replace('_', ' ', $treatment->badge) }}
                            </span>
                        @endif
                    </div>

                    {{-- Body --}}
                    <div class="p-5 flex flex-col flex-1">

                        {{-- Name + rating --}}
                        <div class="flex justify-between items-start gap-2 mb-2">
                            <h3 class="yalia-heading text-base font-bold leading-snug">
                                {{ $treatment->name }}
                            </h3>
                            @if((float)$treatment->rating > 0)
                                <span class="rating-chip shrink-0 inline-flex items-center gap-1">
                                    <i class="fas fa-star text-amber-400"></i>
                                    <span class="font-bold text-gray-800">{{ number_format($treatment->rating, 1) }}</span>
                                    @if($treatment->rating_count > 0)
                                        <span class="text-xs text-gray-400 font-normal">({{ $treatment->rating_count }})</span>
                                    @endif
                                </span>
                            @else
                                <span class="new-chip shrink-0 inline-flex items-center gap-1">
                                    <i class="fas fa-star text-amber-400"></i>
                                    <span>New</span>
                                </span>
                            @endif
                        </div>

                        {{-- Description --}}
                        <p class="text-sm leading-relaxed line-clamp-2 mb-4 flex-1"
                           style="color:#9b6374;">
                            {{ $treatment->description }}
                        </p>

                        {{-- Meta row: duration + category pill --}}
                        <div class="flex items-center gap-2 mb-5 flex-wrap">
                            <div class="flex items-center gap-1 text-xs font-medium"
                                 style="color:#9b6374;">
                                <i data-lucide="clock" class="h-3.5 w-3.5"
                                   style="color: var(--yalia-pink-soft);"></i>
                                {{ $treatment->duration_minutes }} min
                            </div>

                            {{-- Category pill badge --}}
                            <span class="category-pill">
                                @if($treatment->category?->icon)
                                    <i class="{{ $treatment->category->icon }} text-xs"></i>
                                @else
                                    <i data-lucide="tag" class="h-3 w-3"></i>
                                @endif
                                {{ $treatment->category?->name }}
                            </span>
                        </div>

                        {{-- Price + CTA --}}
                        <div class="flex items-center justify-between mt-auto">
                            <span class="price-text">
                                Rp {{ number_format($treatment->price, 0, ',', '.') }}
                            </span>

                            <a href="{{ route('user.bookings.create', ['treatment' => $treatment->id]) }}"
                               class="btn-book">
                                <i data-lucide="calendar-plus" class="h-3.5 w-3.5"></i>
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>

                @empty
                <div class="col-span-full empty-state">
                    <div class="inline-flex items-center justify-center w-14 h-14 rounded-full mb-4"
                         style="background: var(--yalia-pink-pale);">
                        <i data-lucide="search-x" class="h-6 w-6" style="color: var(--yalia-pink);"></i>
                    </div>
                    <h3 class="yalia-heading text-lg font-bold mb-1">No treatments found</h3>
                    <p class="text-sm mb-4" style="color:#9b6374;">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                    <a href="{{ route('user.treatments.index') }}"
                       class="inline-flex items-center gap-1 text-sm font-semibold"
                       style="color: var(--yalia-rose-dark);">
                        <i data-lucide="x-circle" class="h-4 w-4"></i>
                        Clear Filters
                    </a>
                </div>
                @endforelse
            </div>

            {{-- ── Pagination ── --}}
            @if($treatments->hasPages())
                <div class="mt-12 yalia-pagination">
                    {{ $treatments->withQueryString()->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>