{{-- resources/views/components/product-card.blade.php

    Komponen Kartu Produk Yalia / Modern Editorial.
    - Struktur sudut tegas (sharp corners, border-radius: 0) sesuai design brief.
    - Tombol CTA pill-shaped (border-radius: 9999px) sebagai penanda elemen aksi.
    - Disesuaikan dengan Bahasa Indonesia & mata uang Rupiah otomatis.
    - Swatch warna bulat interaktif dengan feedback status aktif.
    - Mendukung badge status & harga coret diskon opsional.

    Pemakaian:
    <x-product-card
        :image="asset('images/produk/sony-headphone.png')"
        name="Sony Headphone"
        description="WH-1000XM5 Wireless Industry Leading Noise Canceling Headphones"
        :colors="['#D9D9D9', '#3B3B3B', '#2B3A67']"
        price="3390000"
        original-price="3890000"
        badge="Promo"
    />
--}}

@props([
    'image' => null,
    'name' => 'Nama Produk',
    'description' => '',
    'colors' => [],
    'price' => '0',
    'originalPrice' => null,
    'badge' => null,
    'url' => null,
    'isFavorite' => false,
])

@php
    $formattedPrice = is_numeric($price) ? 'Rp ' . number_format((float) $price, 0, ',', '.') : $price;
    $formattedOriginalPrice = (filled($originalPrice) && is_numeric($originalPrice))
        ? 'Rp ' . number_format((float) $originalPrice, 0, ',', '.')
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

    {{-- Area Media / Gambar --}}
    <div class="product-card__media">
        @if($badge)
            <span class="product-card__badge">{{ $badge }}</span>
        @endif

        <button type="button"
                @click="toggleFav()"
                :class="{ 'is-active': isFav }"
                class="product-card__fav"
                :aria-label="isFav ? 'Hapus dari daftar favorit' : 'Tambah ke daftar favorit'"
                :aria-pressed="isFav.toString()">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
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
                    <rect width="18" height="18" x="3" y="3" rx="0"/>
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

    {{-- Swatch Pilihan Warna Interaktif --}}
    @if(count($colors))
        <div class="product-card__swatches-wrapper">
            <div class="product-card__swatches" role="radiogroup" aria-label="Pilihan varian warna">
                @foreach($colors as $index => $color)
                    <button type="button"
                            role="radio"
                            @click="selectedColorIndex = {{ $index }}"
                            :class="{ 'is-selected': selectedColorIndex === {{ $index }} }"
                            :aria-checked="(selectedColorIndex === {{ $index }}).toString()"
                            aria-label="Varian warna {{ $index + 1 }}"
                            class="product-card__swatch-btn">
                        <span class="product-card__swatch" style="background-color: {{ $color }}"></span>
                    </button>
                @endforeach
            </div>
            <span class="product-card__color-count">{{ count($colors) }} Warna</span>
        </div>
    @endif

    {{-- Bagian Bawah: Harga & Tombol Aksi Keranjang --}}
    <div class="product-card__footer">
        <div class="product-card__pricing">
            @if($formattedOriginalPrice)
                <span class="product-card__price-original">{{ $formattedOriginalPrice }}</span>
            @endif
            <span class="product-card__price">{{ $formattedPrice }}</span>
        </div>

        @if($url)
            <a href="{{ $url }}" class="product-card__cta" aria-label="Beli atau tambah {{ $name }} ke keranjang">
                <span>+ Keranjang</span>
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </a>
        @else
            <button type="button"
                    @click="$dispatch('add-to-cart', { name: '{{ addslashes($name) }}', price: '{{ addslashes($price) }}', colorIndex: selectedColorIndex })"
                    class="product-card__cta"
                    aria-label="Tambah {{ $name }} ke keranjang">
                <span>+ Keranjang</span>
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </button>
        @endif
    </div>
</div>
