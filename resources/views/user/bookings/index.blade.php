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

                    @include('user.bookings.BookingList', ['bookings' => $bookings, 'tab' => $activeTab, 'paginated' => true])
                </div>
            </div>
        </div>
    </div>
@endsection
