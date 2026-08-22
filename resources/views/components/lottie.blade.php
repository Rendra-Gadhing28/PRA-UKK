@props([
    'src' => '',
    'background' => 'transparent',
    'speed' => '1',
    'loop' => true,
    'autoplay' => true,
])

@php
    $srcUrl = Str::startsWith($src, ['http://', 'https://', '//']) ? $src : asset($src);
@endphp

<dotlottie-player
    src="{{ $srcUrl }}"
    background="{{ $background }}"
    speed="{{ $speed }}"
    {{ $loop ? 'loop' : '' }}
    {{ $autoplay ? 'autoplay' : '' }}
    {{ $attributes->merge(['class' => '']) }}
></dotlottie-player>
