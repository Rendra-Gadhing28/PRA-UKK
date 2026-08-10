<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Book Your Appointment') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-100">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                            Complete Your <span class="text-amber-600">Booking</span>
                        </h1>
                        <p class="mt-2 text-lg text-gray-500">
                            You're just a few steps away from a premium experience.
                        </p>
                    </div>

                    @if($selectedTreatment)
                        <div class="mb-8 bg-amber-50 rounded-xl p-6 border border-amber-100 flex items-center gap-6">
                            @if($selectedTreatment->image)
                                <img src="{{ Storage::url($selectedTreatment->image) }}" class="w-24 h-24 rounded-lg object-cover shadow-sm">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-amber-200 flex items-center justify-center text-amber-600">
                                    <i data-lucide="sparkles" class="w-10 h-10"></i>
                                </div>
                            @endif
                            
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $selectedTreatment->name }}</h3>
                                <p class="text-gray-600">{{ $selectedTreatment->duration_minutes }} Minutes</p>
                                <p class="text-lg font-semibold text-amber-600 mt-2">Rp {{ number_format($selectedTreatment->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('user.bookings.store') }}" method="POST" x-data="{ serviceType: 'salon' }">
                        @csrf
                        
                        @if($selectedTreatment)
                            <input type="hidden" name="treatment_id" value="{{ $selectedTreatment->id }}">
                        @else
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Treatment</label>
                                <select name="treatment_id" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-md" required>
                                    <option value="">Choose a treatment...</option>
                                    <!-- In a real scenario, we'd pass all treatments here if none is selected -->
                                </select>
                            </div>
                        @endif

                        <div class="mb-8">
                            <label class="block text-sm font-medium text-gray-900 mb-4">Service Type</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="booking_type" value="salon" x-model="serviceType" class="peer sr-only">
                                    <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 hover:bg-gray-50 border-gray-200 text-center">
                                        <i data-lucide="store" class="mx-auto h-8 w-8 mb-2" :class="serviceType === 'salon' ? 'text-amber-500' : 'text-gray-400'"></i>
                                        <span class="block font-medium text-gray-900">Visit Salon</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="booking_type" value="home" x-model="serviceType" class="peer sr-only">
                                    <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 hover:bg-gray-50 border-gray-200 text-center">
                                        <i data-lucide="home" class="mx-auto h-8 w-8 mb-2" :class="serviceType === 'home' ? 'text-amber-500' : 'text-gray-400'"></i>
                                        <span class="block font-medium text-gray-900">Home Service</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                                <input type="date" name="booking_date" min="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm py-3 px-4" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                                <input type="time" name="time_start" min="09:00" max="20:00" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 sm:text-sm py-3 px-4" required>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-200 flex justify-end gap-4">
                            <a href="{{ route('user.treatments.index') }}" class="inline-flex justify-center py-3 px-6 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-3 px-8 border border-transparent shadow-md text-base font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                                Confirm Booking
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
