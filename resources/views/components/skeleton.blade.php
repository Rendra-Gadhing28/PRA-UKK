@props([
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'skeleton ' . $class]) }}></div>

