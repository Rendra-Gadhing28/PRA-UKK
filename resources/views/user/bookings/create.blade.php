<x-app-layout>
    <style>
        .blob-bg {
            position: fixed;
            z-index: -1;
            filter: blur(60px);
            opacity: 0.6;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffd2e1, #ffb2ba);
        }
        .blob-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            right: -100px;
        }
        .blob-2 {
            width: 300px;
            height: 300px;
            bottom: 10%;
            left: -50px;
        }
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div class="bg-gray-50 text-gray-900 font-sans min-h-screen relative overflow-x-hidden flex flex-col" x-data="bookingWizard()">
        <!-- Decorative Background -->
        <div class="blob-bg blob-1"></div>
        <div class="blob-bg blob-2"></div>
        
        <!-- Main Content Canvas -->
        <main class="flex-grow w-full max-w-3xl mx-auto px-4 md:px-0 pt-12 pb-32 flex flex-col gap-8 z-10">
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-2" style="font-family: 'Playfair Display', serif;">Book Your Treatment</h1>
                <p class="text-lg text-gray-500">Select a date and time for your session.</p>
            </div>
            
            <form action="{{ route('user.bookings.store') }}" method="POST" id="bookingForm" class="flex flex-col gap-8">
                @csrf
                @if($selectedTreatment)
                    <input type="hidden" name="treatment_id" value="{{ $selectedTreatment->id }}">
                @endif
                <input type="hidden" name="booking_date" x-model="selectedDate">
                <input type="hidden" name="time_start" x-model="selectedTime">
                
                <!-- Treatment Summary Card -->
                <section class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                    <div class="absolute top-0 left-0 w-full h-1 bg-amber-500"></div>
                    <div class="flex items-start justify-between">
                        <div class="flex gap-4">
                            <div class="w-20 h-20 rounded-xl overflow-hidden shadow-sm shrink-0 bg-amber-100 flex items-center justify-center">
                                @if($selectedTreatment && $selectedTreatment->images)
                                    <img class="w-full h-full object-cover" src="{{ Storage::url($selectedTreatment->images) }}" alt="{{ $selectedTreatment->name }}">
                                @else
                                    <i data-lucide="sparkles" class="text-amber-500 w-8 h-8"></i>
                                @endif
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-1" style="font-family: 'Playfair Display', serif;">{{ $selectedTreatment ? $selectedTreatment->name : 'Treatment' }}</h2>
                                @if($selectedTreatment && $selectedTreatment->rating_avg > 0)
                                <div class="flex items-center gap-2 mb-2">
                                    <i data-lucide="star" class="text-amber-500 w-4 h-4 fill-current"></i>
                                    <span class="text-sm text-gray-900 font-medium">{{ number_format($selectedTreatment->rating_avg, 1) }} <span class="text-gray-500 font-normal">({{ $selectedTreatment->rating_count }} reviews)</span></span>
                                </div>
                                @endif
                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                    <span class="flex items-center gap-1"><i data-lucide="clock" class="w-4 h-4"></i> {{ $selectedTreatment ? $selectedTreatment->duration_minutes : 60 }} mins</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl text-amber-600 font-bold">Rp {{ $selectedTreatment ? number_format($selectedTreatment->price, 0, ',', '.') : 0 }}</span>
                        </div>
                    </div>
                </section>

                <!-- Service Type Selection -->
                <section>
                    <h3 class="text-xl font-bold text-gray-900 mb-4" style="font-family: 'Playfair Display', serif;">Service Type</h3>
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
                </section>
                
                <!-- Date Selection (Horizontal Scroll) -->
                <section>
                    <h3 class="text-xl font-bold text-gray-900 mb-4" style="font-family: 'Playfair Display', serif;">Select Date</h3>
                    <div class="flex gap-3 overflow-x-auto hide-scrollbar pb-2 pt-1 px-1 -mx-1 snap-x">
                        <template x-for="d in dates" :key="d.date">
                            <button type="button" @click="selectDate(d.date)"
                                class="snap-start shrink-0 w-20 py-4 rounded-2xl flex flex-col items-center justify-center gap-1 transition-all relative"
                                :class="selectedDate === d.date ? 'bg-amber-500 border border-amber-500 text-white shadow-md transform scale-105' : 'bg-white border border-gray-200 text-gray-900 hover:border-amber-300 hover:shadow-sm'">
                                
                                <span x-show="selectedDate === d.date" class="absolute -top-1 -right-1 flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white border border-amber-500"></span>
                                </span>
                                
                                <span class="text-sm uppercase tracking-wide" :class="selectedDate === d.date ? 'text-white/90' : 'text-gray-500'" x-text="d.displayDay"></span>
                                <span class="text-2xl font-bold" :class="selectedDate === d.date ? 'text-white' : 'text-gray-900'" x-text="d.displayDate"></span>
                            </button>
                        </template>
                    </div>
                </section>
                
                <!-- Time Selection (Grid) -->
                <section x-show="selectedDate">
                    <div class="flex justify-between items-end mb-4">
                        <h3 class="text-xl font-bold text-gray-900" style="font-family: 'Playfair Display', serif;">Available Times</h3>
                        <span class="text-sm text-gray-500 font-medium" x-text="formatDateDisplay(selectedDate)"></span>
                    </div>
                    
                    <div x-show="isLoadingSlots" class="flex justify-center py-8">
                        <svg class="animate-spin h-8 w-8 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <div x-show="!isLoadingSlots" class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        <template x-for="t in times" :key="t">
                            <button type="button" @click="if(!isTimeDisabled(t)) { selectedTime = t }"
                                :disabled="isTimeDisabled(t)"
                                class="py-3 px-4 rounded-full text-sm font-semibold transition-all"
                                :class="[
                                    isTimeDisabled(t) ? 'bg-gray-100 border border-gray-200 text-gray-400 opacity-50 cursor-not-allowed' : '',
                                    !isTimeDisabled(t) && selectedTime === t ? 'bg-amber-500 text-white shadow-md transform scale-105' : '',
                                    !isTimeDisabled(t) && selectedTime !== t ? 'bg-amber-50 text-gray-900 border border-transparent hover:border-amber-300' : ''
                                ]"
                                x-text="formatTimeDisplay(t)">
                            </button>
                        </template>
                    </div>
                </section>
                
                <div class="mt-8 flex justify-end gap-4">
                    <a href="{{ route('user.treatments.index') }}" class="inline-flex justify-center items-center py-3 px-6 border border-gray-300 shadow-sm text-base font-medium rounded-full text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="button" @click="submitBooking" :disabled="!selectedDate || !selectedTime"
                        class="w-full sm:w-auto py-4 px-8 rounded-full bg-amber-600 text-white text-lg font-bold shadow-sm hover:bg-amber-700 transition-colors flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirm Booking
                        <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>
        </main>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('bookingWizard', () => ({
                serviceType: 'salon',
                dates: [],
                selectedDate: '',
                times: [],
                selectedTime: '',
                bookedSlots: [],
                isLoadingSlots: false,
                treatmentDuration: {{ $selectedTreatment ? $selectedTreatment->duration_minutes : 60 }},
                
                init() {
                    this.generateDates();
                    this.generateTimes();
                    
                    if(this.dates.length > 0) {
                        this.selectDate(this.dates[0].date);
                    }
                },
                
                generateDates() {
                    let today = new Date();
                    for(let i = 0; i < 14; i++) {
                        let d = new Date(today);
                        d.setDate(today.getDate() + i);
                        
                        let yyyy = d.getFullYear();
                        let mm = String(d.getMonth() + 1).padStart(2, '0');
                        let dd = String(d.getDate()).padStart(2, '0');
                        
                        this.dates.push({
                            date: `${yyyy}-${mm}-${dd}`,
                            displayDay: d.toLocaleDateString('en-US', { weekday: 'short' }),
                            displayDate: d.getDate()
                        });
                    }
                },
                
                generateTimes() {
                    for(let h = 9; h <= 17; h++) {
                        for(let m of ['00', '30']) {
                            this.times.push(`${String(h).padStart(2, '0')}:${m}:00`);
                        }
                    }
                },
                
                async selectDate(dateStr) {
                    this.selectedDate = dateStr;
                    this.selectedTime = '';
                    this.bookedSlots = [];
                    this.isLoadingSlots = true;
                    
                    try {
                        let res = await fetch(`{{ route('user.slots.check') }}?date=${dateStr}`);
                        let data = await res.json();
                        this.bookedSlots = data.booked_slots || [];
                    } catch (e) {
                        console.error('Failed to fetch slots', e);
                    } finally {
                        this.isLoadingSlots = false;
                    }
                },
                
                isTimeDisabled(timeStr) {
                    // Check if it's in the past today
                    let today = new Date();
                    let yyyy = today.getFullYear();
                    let mm = String(today.getMonth() + 1).padStart(2, '0');
                    let dd = String(today.getDate()).padStart(2, '0');
                    let todayStr = `${yyyy}-${mm}-${dd}`;
                    
                    if (this.selectedDate === todayStr) {
                        let currentHour = today.getHours();
                        let currentMin = today.getMinutes();
                        let [th, tm] = timeStr.split(':').map(Number);
                        if (th < currentHour || (th === currentHour && tm <= currentMin)) {
                            return true;
                        }
                    }
                
                    let [th, tm] = timeStr.split(':').map(Number);
                    let startMinutes = th * 60 + tm;
                    let endMinutes = startMinutes + this.treatmentDuration;
                    
                    for(let slot of this.bookedSlots) {
                        let [sh, sm] = slot.time_start.split(':').map(Number);
                        let slotStartMinutes = sh * 60 + sm;
                        
                        let [eh, em] = slot.time_end.split(':').map(Number);
                        let slotEndMinutes = eh * 60 + em;
                        
                        if (Math.max(startMinutes, slotStartMinutes) < Math.min(endMinutes, slotEndMinutes)) {
                            return true;
                        }
                    }
                    return false;
                },
                
                formatTimeDisplay(timeStr) {
                    let [h, m] = timeStr.split(':');
                    let hour = parseInt(h);
                    let ampm = hour >= 12 ? 'PM' : 'AM';
                    hour = hour % 12;
                    hour = hour ? hour : 12;
                    return `${String(hour).padStart(2, '0')}:${m} ${ampm}`;
                },
                
                formatDateDisplay(dateStr) {
                    if (!dateStr) return '';
                    let d = new Date(dateStr);
                    return d.toLocaleDateString('en-US', { weekday: 'short', day: 'numeric', month: 'short' });
                },
                
                submitBooking() {
                    if (this.selectedDate && this.selectedTime) {
                        document.getElementById('bookingForm').submit();
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
