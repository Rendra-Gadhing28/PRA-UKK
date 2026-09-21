{{-- resources/views/components/product-card.blade.php

    Komponen Kartu Produk Modern (Referensi Sony Headphone Card).
    - Struktur card rounded elegan (border-radius: 24px) dengan soft elevation shadow.
    - Media area bersih dengan circular favorite/wishlist button di pojok kanan atas.
    - Swatch pilihan warna bulat dengan ring indikator aktif.
    - Footer: Harga di sebelah kiri, Tombol CTA Pill di sebelah kanan ("Add to cart >").
    - Mendukung format Rupiah otomatis atau dolar / mata uang lain.

    Pemakaian:
    <x-product-card
        :image="asset('images/produk/sony-headphone.png')"
        name="Sony Headphone"
        description="WH-1000XM5 Wireless Industry Leading Noise Canceling Headphones"
        :colors="['#D9D9D9', '#1F2937', '#1E3A8A']"
        price="339"
        currency="$"
    />
--}}

@props([
    'image' => null,
    'name' => 'Product Name',
    'description' => '',
    'colors' => [],
    'price' => '0',
    'originalPrice' => null,
    'currency' => null,
    'ctaText' => 'Add to cart',
    'badge' => null,
    'url' => null,
    'isFavorite' => false,
])

@php
    $hasCurrency = filled($currency);
    $formattedPrice = is_numeric($price)
        ? ($hasCurrency ? $currency . number_format((float) $price, 0, ',', '.') : 'Rp ' . number_format((float) $price, 0, ',', '.'))
        : $price;

    $formattedOriginalPrice = (filled($originalPrice) && is_numeric($originalPrice))
        ? ($hasCurrency ? $currency . number_format((float) $originalPrice, 0, ',', '.') : 'Rp ' . number_format((float) $originalPrice, 0, ',', '.'))
        : $originalPrice;
@endphp

<div x-data="{
        isFav: {{ $isFavorite ? 'true' : 'false' }},
        selectedColorIndex: 0,
        toggleFav() {
            this.isFav = !this.isFav;
            $dispatch('product-favorited', { name: '{{ addslashes($name) }}', isFav: this.isFav });
        }
     }"
     class="product-card">

    {{-- Area Media / Gambar Produk --}}
    <div class="product-card__media">
        @if($badge)
            <span class="product-card__badge">{{ $badge }}</span>
        @endif

        {{-- Circular Wishlist / Favorite Button (Top-Right) --}}
        <button type="button"
                @click="toggleFav()"
                :class="{ 'is-active': isFav }"
                class="product-card__fav"
                :aria-label="isFav ? 'Hapus dari favorit' : 'Tambah ke favorit'"
                :aria-pressed="isFav.toString()">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path :fill="isFav ? 'currentColor' : 'none'"
                      d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
        </button>

        @if($image)
            @if($url)
                <a href="{{ $url }}" class="flex items-center justify-center w-full h-full" tabindex="-1">
                    <img src="{{ $image }}" alt="{{ $name }}" class="product-card__img" loading="lazy" decoding="async">
                </a>
            @else
                <img src="{{ $image }}" alt="{{ $name }}" class="product-card__img" loading="lazy" decoding="async">
            @endif
        @else
            <div class="product-card__img-placeholder">
                <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <rect width="18" height="18" x="3" y="3" rx="4"/>
                    <circle cx="9" cy="9" r="2"/>
                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                </svg>
            </div>
        @endif
    </div>

    {{-- Area Teks & Informasi Produk --}}
    <div class="product-card__body">
        <h3 class="product-card__name" title="{{ $name }}">
            @if($url)
                <a href="{{ $url }}" class="text-inherit hover:underline decoration-1 underline-offset-2">{{ $name }}</a>
            @else
                {{ $name }}
            @endif
        </h3>
        @if($description)
            <p class="product-card__desc" title="{{ $description }}">{{ $description }}</p>
        @endif
    </div>

    {{-- Swatch Pilihan Warna --}}
    @if(count($colors))
        <div class="product-card__swatches-wrapper">
            <div class="product-card__swatches" role="radiogroup" aria-label="Pilihan varian warna">
                @foreach($colors as $index => $color)
                    <button type="button"
                            role="radio"
                            @click="selectedColorIndex = {{ $index }}"
                            :class="{ 'is-selected': selectedColorIndex === {{ $index }} }"
                            :aria-checked="(selectedColorIndex === {{ $index }}).toString()"
                            aria-label="Warna varian {{ $index + 1 }}"
                            class="product-card__swatch-btn">
                        <span class="product-card__swatch" style="background-color: {{ $color }}"></span>
                    </button>
                @endforeach
            </div>
            <span class="product-card__color-count">{{ count($colors) }} Warna</span>
        </div>
    @endif

    {{-- Footer: Harga di kiri, Tombol CTA Pill di kanan --}}
    <div class="product-card__footer">
        <div class="product-card__pricing">
            @if($formattedOriginalPrice)
                <span class="product-card__price-original">{{ $formattedOriginalPrice }}</span>
            @endif
            <span class="product-card__price">{{ $formattedPrice }}</span>
        </div>

        @if($url)
            <a href="{{ $url }}" class="product-card__cta" aria-label="Beli atau tambah {{ $name }} ke keranjang">
                <span>{{ $ctaText }}</span>
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </a>
        @else
            <button type="button"
                    @click="$dispatch('add-to-cart', { name: '{{ addslashes($name) }}', price: '{{ addslashes($price) }}', colorIndex: selectedColorIndex })"
                    class="product-card__cta"
                    aria-label="Tambah {{ $name }} ke keranjang">
                <span>{{ $ctaText }}</span>
                <svg viewBox="0 0 24 24" width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </button>
        @endif
    </div>
</div>
