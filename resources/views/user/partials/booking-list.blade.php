@if ($bookings->isEmpty())
    {{-- Sama seperti empty state di mockup (yang di sana class="hidden" —
         di sini betulan ditampilkan kalau memang kosong) --}}
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <span class="material-symbols-outlined text-6xl text-primary/20 mb-4">calendar_today</span>
        <h3 class="text-headline-sm font-headline-sm">
            @switch($tab)
                @case('past')
                    No booking history yet
                    @break
                @case('cancelled')
                    No cancelled bookings
                    @break
                @default
                    No bookings found
            @endswitch
        </h3>
        <p class="text-body-md font-body-md text-on-surface-variant mb-8">
            Ready to treat yourself? Explore our services.
        </p>
        <a href="{{ route('user.treatments.index') }}"
           class="bg-primary text-on-primary px-8 py-3 rounded-full text-button font-button hover:bg-secondary transition-all inline-block">
            Explore Treatments
        </a>
    </div>
@else
    <div class="space-y-6">
        @foreach ($bookings as $booking)
            @php $status = $booking->status; @endphp
            <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-border-subtle flex flex-col lg:flex-row gap-8 items-center group transition-all hover:shadow-md {{ $status->value === 'pending' ? 'opacity-90' : '' }}">

                <div class="relative w-full lg:w-48 h-48 rounded-2xl overflow-hidden shrink-0">
                    <img
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        src="{{ $booking->treatment?->images->first() ? asset('storage/'.$booking->treatment->images->first()) : 'https://ui-avatars.com/api/?name='.urlencode($booking->treatment?->name ?? 'Treatment').'&background=ffe8ed&color=b01f44&size=256' }}"
                        alt="{{ $booking->treatment?->name ?? 'Treatment' }}"
                        loading="lazy"
                    >
                </div>

                <div class="flex-1 w-full space-y-4">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div>
                            <span class="px-3 py-1 rounded-full text-label-md font-label-md {{ $status->badgeClasses() }}">
                                {{ $status->badgeLabel() }}
                            </span>
                            <h3 class="text-headline-sm font-headline-sm mt-2">{{ $booking->treatment?->name ?? 'Treatment dihapus' }}</h3>
                            <p class="text-body-md font-body-md text-on-surface-variant mt-1">
                                with Specialist <span class="font-bold">{{ $booking->beautician?->name ?? '-' }}</span>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-headline-sm font-headline-sm text-primary">{{ $booking->formatted_total }}</p>
                            <p class="text-body-sm font-body-sm text-on-surface-variant">{{ $booking->treatment?->duration_minutes }} Minutes</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-6 text-on-surface-variant">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">calendar_month</span>
                            <span class="text-body-md font-body-md">
                                {{ $booking->booking_date->isTomorrow() ? 'Tomorrow, ' : '' }}{{ $booking->booking_date->translatedFormat('l, d M Y') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">schedule</span>
                            <span class="text-body-md font-body-md">
                                {{ \Illuminate\Support\Carbon::parse($booking->start_time)->format('h:i A') }}
                                - {{ \Illuminate\Support\Carbon::parse($booking->end_time)->format('h:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 w-full lg:w-48">
                    <a href="{{ route('user.bookings.show', $booking->booking_code) }}"
                       class="w-full bg-primary text-on-primary py-3 rounded-full text-button font-button text-center hover:bg-secondary transition-all active:scale-95">
                        View Details
                    </a>

                    @if (in_array($status->value, ['pending', 'confirmed']))
                        @if ($status->value === 'confirmed')
                            <a href="{{ route('user.bookings.reschedule', $booking->booking_code) }}"
                               class="w-full border border-tertiary text-tertiary py-3 rounded-full text-button font-button text-center hover:bg-surface-container-high transition-all">
                                Reschedule
                            </a>
                        @else
                            <form action="{{ route('user.bookings.cancel', $booking->booking_code) }}" method="POST"
                                  onsubmit="return confirm('Cancel this booking?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-full border border-tertiary text-tertiary py-3 rounded-full text-button font-button hover:bg-surface-container-high transition-all">
                                    Cancel
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if ($paginated ?? false)
        <div class="mt-8 flex justify-center">
            {{ $bookings->links() }}
        </div>
    @endif
@endif
