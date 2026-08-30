{{-- ============================================================
     _partials/voucher-grid.blade.php
     Dipakai oleh tab: Semua, Tukar PTS, Event
     Variables:
       $vouchers          — Collection<Vouchers>
       $claimedVoucherIds — array<int>

     Kolom yang tersedia (dari skema asli + tambahan):
       code, name, description, type, value, min_purchase,
       max_discount, event_name, is_event, points_required,
       quota, used_count, valid_from, valid_until, image,
       [accessor] remaining_quota, is_quota_out, type_label
     ============================================================ --}}

@if ($vouchers->isEmpty())
    <div class="flex flex-col items-center justify-center py-16 text-center">
        <svg width="96" height="96" viewBox="0 0 96 96" fill="none"
             xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect width="96" height="96" rx="48" fill="#F3F4F6"/>
            <path d="M24 48h48M48 24v48" stroke="#9CA3AF" stroke-width="2.5"
                  stroke-linecap="round"/>
        </svg>
        <p class="mt-4 text-gray-500 text-sm">Tidak ada voucher yang tersedia.</p>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach ($vouchers as $voucher)
            <article
                class="bg-white rounded-2xl border border-gray-100 shadow-sm
                       hover:shadow-md transition-shadow overflow-hidden flex flex-col"
                aria-label="Voucher {{ $voucher->code }}"
            >
                {{-- Thumbnail — WebP, width/height eksplisit, lazy load --}}
                    <img
                        src="{{ \App\Support\ImageHelper::url($voucher->image) }}"
                        alt="Banner voucher {{ $voucher->name }}"
                        width="400"
                        height="160"
                        loading="lazy"
                        class="w-full object-cover"
                        style="aspect-ratio: 400/160"
                    >
                @else
                    <div
                        class="w-full bg-gradient-to-br from-indigo-50 to-purple-50
                               flex items-center justify-center"
                        style="height:100px"
                        aria-hidden="true"
                    >
                        <span class="text-3xl select-none">
                            @if($voucher->is_event) 🎁
                            @elseif($voucher->points_required > 0) 💎
                            @else 🎟️
                            @endif
                        </span>
                    </div>
                @endif

                <div class="p-4 flex flex-col flex-1 gap-2">

                    {{-- Badge tipe voucher --}}
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($voucher->is_event)
                            <span class="text-xs bg-amber-100 text-amber-700
                                         px-2 py-0.5 rounded-full font-medium">
                                Event
                            </span>
                        @endif

                        @if ($voucher->points_required > 0)
                            <span class="text-xs bg-indigo-100 text-indigo-700
                                         px-2 py-0.5 rounded-full font-medium">
                                {{ number_format($voucher->points_required) }} PTS
                            </span>
                        @else
                            <span class="text-xs bg-green-100 text-green-700
                                         px-2 py-0.5 rounded-full font-medium">
                                Gratis
                            </span>
                        @endif

                        {{-- Badge nilai diskon dari accessor type_label --}}
                        <span class="text-xs bg-rose-50 text-rose-600
                                     px-2 py-0.5 rounded-full font-semibold">
                            {{ $voucher->type_label }}
                        </span>
                    </div>

                    {{-- Info voucher --}}
                    <div class="flex-1">
                        <h2 class="font-semibold text-gray-800 text-sm leading-snug line-clamp-1">
                            {{ $voucher->name }}
                        </h2>
                        <p class="text-xs text-indigo-600 font-mono font-bold mt-0.5">
                            {{ $voucher->code }}
                        </p>

                        @if ($voucher->description)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                {{ $voucher->description }}
                            </p>
                        @endif

                        {{-- Syarat minimum pembelian --}}
                        @if ($voucher->min_purchase)
                            <p class="text-xs text-gray-400 mt-1">
                                Min. belanja
                                Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}
                            </p>
                        @endif
                    </div>

                    {{-- Meta: kuota & tanggal --}}
                    <div class="flex items-center justify-between text-xs text-gray-400 mt-1">
                        {{-- Accessor remaining_quota dari model --}}
                        <span>Kuota: {{ $voucher->remaining_quota }} tersisa</span>
                        <span>
                            s/d {{ $voucher->valid_until->isoFormat('D MMM Y') }}
                        </span>
                    </div>

                    {{-- CTA Klaim --}}
                    @if (in_array($voucher->id, $claimedVoucherIds, true))
                        <button
                            type="button"
                            disabled
                            aria-label="Voucher {{ $voucher->code }} sudah diklaim"
                            class="mt-2 w-full py-2 rounded-xl text-xs font-semibold
                                   bg-gray-100 text-gray-400 cursor-not-allowed"
                        >
                            ✓ Sudah Diklaim
                        </button>

                    @elseif ($voucher->is_quota_out)
                        {{-- Accessor is_quota_out dari model --}}
                        <button
                            type="button"
                            disabled
                            aria-label="Kuota voucher {{ $voucher->code }} habis"
                            class="mt-2 w-full py-2 rounded-xl text-xs font-semibold
                                   bg-gray-100 text-gray-400 cursor-not-allowed"
                        >
                            Kuota Habis
                        </button>

                    @else
                        <form
                            method="POST"
                            action="{{ route('user.vouchers.claim', $voucher) }}"
                            class="mt-2"
                        >
                            @csrf
                            <button
                                type="submit"
                                aria-label="Klaim voucher {{ $voucher->code }}"
                                class="w-full py-2 rounded-xl text-xs font-semibold
                                       text-white bg-indigo-600 hover:bg-indigo-700
                                       active:scale-95 transition-all focus:outline-none
                                       focus:ring-2 focus:ring-indigo-400 focus:ring-offset-1"
                            >
                                @if ($voucher->points_required > 0)
                                    Tukar {{ number_format($voucher->points_required) }} PTS
                                @else
                                    Klaim Sekarang
                                @endif
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif