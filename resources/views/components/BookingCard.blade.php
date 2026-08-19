{{-- Satu kartu booking. $booking sudah eager-loaded (beautician, treatments) — aman dari N+1. --}}
@php
    $stVal = is_object($booking->status) ? $booking->status->value : (string)$booking->status;
    $statusLabel = match($stVal) {
        'pending'     => 'Pending Approval',
        'confirmed'   => 'Confirmed',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'canceled'    => 'Cancelled',
        default       => is_object($booking->status) && method_exists($booking->status, 'badgeLabel') ? $booking->status->badgeLabel() : ucfirst($stVal),
    };
    $statusBadgeClass = $stVal === 'confirmed'
        ? 'bg-accent-clear text-on-secondary-container'
        : 'bg-surface-container-high text-on-secondary-container';
    $thumbnail = optional($booking->treatments->first())->image;
@endphp

<div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-border-subtle flex flex-col lg:flex-row gap-8 items-center transition-all hover:shadow-md {{ $stVal === 'canceled' ? 'opacity-70' : '' }}">
    <div class="relative w-full lg:w-48 h-48 rounded-2xl overflow-hidden shrink-0 bg-surface-container-high">
        @if($thumbnail)
            <img class="w-full h-full object-cover" src="{{ asset('storage/' . $thumbnail) }}" alt="{{ $booking->treatments->first()->name }}">
        @else
            <div class="w-full h-full flex items-center justify-center text-4xl">💆</div>
        @endif
    </div>

    <div class="flex-1 w-full space-y-4">
        <div class="flex flex-wrap justify-between items-start gap-4">
            <div>
                <span class="{{ $statusBadgeClass }} px-3 py-1 rounded-full text-label-md font-label-md">{{ $statusLabel }}</span>
                <h3 class="text-headline-sm font-headline-sm mt-2">
                    {{ $booking->treatments->pluck('name')->join(', ') }}
                </h3>
                @if($booking->beautician)
                    <p class="text-body-md font-body-md text-on-surface-variant mt-1">
                        with Specialist <span class="font-bold">{{ $booking->beautician->name }}</span>
                    </p>
                @endif
            </div>
            <div class="text-right">
                <p class="text-headline-sm font-headline-sm text-primary">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                <p class="text-body-sm font-body-sm text-on-surface-variant">{{ $booking->booking_code }}</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-6 text-on-surface-variant">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">calendar_month</span>
                <span class="text-body-md font-body-md">{{ $booking->booking_date->translatedFormat('l, d M Y') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">schedule</span>
                <span class="text-body-md font-body-md">{{ $booking->time_start }} - {{ $booking->time_end }}</span>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-3 w-full lg:w-48">
        <a href="{{ route('user.bookings.show', $booking) }}"
           class="w-full text-center bg-primary text-on-primary py-3 rounded-full text-button hover:bg-secondary transition-all active:scale-95">
            View Details
        </a>

        @if(in_array($stVal, ['pending', 'confirmed']))
            <form method="POST" action="{{ route('user.bookings.cancel', $booking) }}"
                  onsubmit="return confirm('Cancel this booking?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="w-full border border-tertiary text-tertiary py-3 rounded-full text-button hover:bg-surface-container-high transition-all">
                    Cancel
                </button>
            </form>
        @endif
    </div>
</div>