{{--
    Partial grid kartu treatment.
    Dipakai ulang oleh:
    - user.treatments.index (render awal / SSR)
    - TreatmentController@search (response AJAX saat user mengetik/filter)

    $treatments di sini SUDAH melalui eager loading ->with('category')
    di TreatmentQueryService, sehingga mengakses $treatment->category->name
    di bawah TIDAK memicu query tambahan per baris (anti N+1).

    Semua output teks memakai {{ }} (bukan {!! !!}) agar otomatis di-escape
    oleh Blade, mencegah XSS dari data yang berasal dari admin/database.
--}}
@forelse ($treatments as $treatment)
    <div class="treatment-card group bg-white p-4 rounded-[32px] border border-border-subtle luxury-shadow flex flex-col h-full transition-all">
        <div class="relative aspect-[4/3] overflow-hidden rounded-[24px] mb-6">
            <img
                src="{{ $treatment->image_url }}"
                alt="{{ $treatment->name }}"
                loading="lazy"
                class="w-full h-full object-cover"
            />
            @if ($treatment->badge !== 'none')
                <div class="absolute top-4 left-4">
                    <span @class([
                        'px-4 py-1.5 rounded-full font-label-md text-label-md uppercase tracking-wider',
                        'bg-[#f45472] text-white' => $treatment->badge === 'best_seller',
                        'bg-accent-clear text-primary' => $treatment->badge === 'new',
                        'bg-primary text-white' => $treatment->badge === 'promo',
                    ])>
                        {{ match ($treatment->badge) {
                            'best_seller' => 'Best Seller',
                            'new' => 'New',
                            'promo' => 'Promo',
                            default => '',
                        } }}
                    </span>
                </div>
            @endif
        </div>

        <div class="px-4 flex-grow">
            <div class="flex items-center gap-1 mb-2">
                <span class="material-symbols-outlined text-[#f45472] text-[18px]" style="font-variation-settings: 'FILL' 1;">star</span>
                <span class="font-label-lg text-label-lg text-text-heading">
                    {{ number_format((float) $treatment->rating, 1) }}
                    <span class="text-on-surface-variant font-normal">({{ $treatment->rating_count }} reviews)</span>
                </span>
            </div>
            <h3 class="font-headline-sm text-headline-sm text-text-heading mb-2">{{ $treatment->name }}</h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mb-2 line-clamp-2">{{ $treatment->description }}</p>
            <span class="font-label-md text-label-md text-primary uppercase">{{ $treatment->category->name }}</span>
        </div>

        <div class="px-4 pb-4 border-t border-border-subtle pt-6 mt-auto">
            <div class="flex justify-between items-center mb-6">
                <div class="flex flex-col">
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase">Durasi</span>
                    <span class="font-label-lg text-label-lg text-text-heading">{{ $treatment->duration_minutes }} Menit</span>
                </div>
                <div class="flex flex-col text-right">
                    <span class="font-label-md text-label-md text-on-surface-variant uppercase">Harga</span>
                    <span class="font-headline-sm text-headline-sm text-primary">Rp{{ number_format((float) $treatment->price, 0, ',', '.') }}</span>
                </div>
            </div>
            <a
                href="{{ route('user.bookings.list', ['treatment' => $treatment->slug]) }}"
                class="block text-center w-full bg-[#f45472] text-white py-4 rounded-full font-button text-button hover:opacity-90 transition-all active:scale-95"
            >
                Booking Sekarang
            </a>
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-16">
        <p class="font-body-lg text-body-lg text-on-surface-variant">Tidak ada treatment yang ditemukan.</p>
    </div>
@endforelse