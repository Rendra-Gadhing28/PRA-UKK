@extends('layouts.app')

@section('content')
    <div class="py-32 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8 border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <a href="{{ route('user.bookings.index', ['tab' => 'upcoming']) }}" class="{{ $activeTab === 'upcoming' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Upcoming
                            </a>
                            <a href="{{ route('user.bookings.index', ['tab' => 'past']) }}" class="{{ $activeTab === 'past' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Past
                            </a>
                            <a href="{{ route('user.bookings.index', ['tab' => 'cancelled']) }}" class="{{ $activeTab === 'cancelled' ? 'border-amber-500 text-amber-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                Cancelled
                            </a>
                        </nav>
                    </div>

                    <div class="space-y-6">
                        @forelse($bookings as $booking)
                            <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="text-sm font-semibold px-2.5 py-0.5 rounded {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($booking->status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ $booking->booking_code }}</span>
                                        </div>
                                        <h3 class="text-lg font-bold text-gray-900">
                                            @foreach($booking->treatments as $treatment)
                                                {{ $treatment->name }}@if(!$loop->last), @endif
                                            @endforeach
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ \Carbon\Carbon::parse($booking->booking_date)->format('l, d F Y') }} at {{ \Carbon\Carbon::parse($booking->time_start)->format('H:i') }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</div>
                                        <div class="text-sm {{ $booking->payment_status === 'paid' ? 'text-green-600' : 'text-red-600' }} mt-1">
                                            {{ ucfirst($booking->payment_status) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end gap-3">
                                    <a href="{{ route('user.bookings.show', $booking->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <i data-lucide="calendar-x" class="mx-auto h-12 w-12 text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900">No {{ $activeTab }} bookings</h3>
                                <p class="mt-2 text-sm text-gray-500">You don't have any {{ $activeTab }} bookings at the moment.</p>
                                @if($activeTab === 'upcoming')
                                    <div class="mt-6">
                                        <a href="{{ route('user.treatments.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                            Book a Treatment
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
