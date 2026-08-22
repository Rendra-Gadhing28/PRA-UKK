<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Riwayat Reservasi</h2>
    </x-slot>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        .booking-page { font-family: 'Work Sans', sans-serif; }
        
        /* Smooth stagger animation on card entry */
        @keyframes card-enter {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-card-enter {
            animation: card-enter 0.48s cubic-bezier(0.22, 0.61, 0.36, 1) both;
        }

        /* Tab indicator underline smooth slide */
        .tab-pill { position: relative; }
        .tab-pill::after {
            content: '';
            position: absolute;
            inset-x-0; bottom: -2px;
            height: 2px;
            border-radius: 9999px;
            background: #b01f44;
            transform: scaleX(0);
            transition: transform 0.28s cubic-bezier(0.34, 1.56, 0.64, 1);
            transform-origin: center;
        }
        .tab-pill.active::after { transform: scaleX(1); }
    </style>

    <div class="booking-page pt-28 pb-24 min-h-screen bg-background-main relative overflow-hidden">

        {{-- Ambient blobs --}}
        <div class="pointer-events-none absolute -top-32 -left-32 w-96 h-96 bg-primary-fixed/30 rounded-full blur-[100px]"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-32 w-80 h-80 bg-tertiary-fixed/20 rounded-full blur-[90px]"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 space-y-6 animate-card-enter">

            {{-- ─── Page Header ─────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 ">
                <div>
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-1">Yalia Beauty Salon</p>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-text-heading leading-tight" style="font-family:'Playfair Display',serif">
                        Riwayat Reservasi Saya
                    </h1>
                    <p class="text-sm text-on-surface-variant mt-1">Pantau & kelola semua jadwal perawatan kecantikanmu</p>
                </div>
                <div class="self-center content-center ">
                      <a href="{{ route('user.treatments.index') }}"
                   class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-md bg-primary text-on-primary text-xs font-bold shadow-sm
                          hover:bg-primary-container hover:shadow-md active:scale-95
                          transition-all duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)] shrink-0">
                    <i class="fas fa-plus text-[9px]"></i>
                    <span>Booking Baru</span>
                </a>
                </div>
            </div>
            

            {{-- ─── Tab Navigation ───────────────────────────── --}}
            <div class="bg-surface-container-lowest rounded-2xl p-1.5 shadow-sm border border-outline-variant/30 flex gap-1">
                @php
                    $tabs = [
                        ['key' => 'upcoming',  'label' => 'Mendatang',  'icon' => 'fa-clock'],
                        ['key' => 'past',      'label' => 'Selesai',    'icon' => 'fa-circle-check'],
                        ['key' => 'cancelled', 'label' => 'Dibatalkan', 'icon' => 'fa-circle-xmark'],
                    ];
                @endphp
                @foreach ($tabs as $t)
                    @php
                        $isActive = $t['key'] === $activeTab
                            || ($t['key'] === 'cancelled' && in_array($activeTab, ['canceled', 'cancelled']));
                    @endphp
                    <a href="{{ route('user.bookings.index', ['tab' => $t['key']]) }}"
                       class="flex-1 flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl text-xs font-bold transition-all duration-300
                              {{ $isActive
                                  ? 'bg-primary text-on-primary shadow-md'
                                  : 'text-on-surface-variant hover:text-on-surface hover:bg-surface-container' }}">
                        <i class="fas {{ $t['icon'] }} text-[10px]"></i>
                        <span>{{ $t['label'] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- ─── Booking List ─────────────────────────────── --}}
            @include('user.bookings.BookingList', ['bookings' => $bookings, 'tab' => $activeTab, 'paginated' => true])

        </div>
    </div>
</x-app-layout>
