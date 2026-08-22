@props([
    'size' => 'md',
    'withText' => false,
    'class' => ''
])

@php
    $sizeClasses = [
        'sm' => 'h-8 w-8',
        'md' => 'h-12 w-12',
        'lg' => 'h-16 w-16',
        'xl' => 'h-20 w-20',
    ][$size] ?? 'h-12 w-12';
@endphp

@if($withText)
    <div {{ $attributes->merge(['class' => 'flex items-center space-x-4 ' . $class]) }}>
        <x-skeleton class="{{ $sizeClasses }} rounded-full shrink-0" />
        <div class="space-y-2 flex-1">
            <x-skeleton class="h-4 w-[200px]" />
            <x-skeleton class="h-4 w-[150px]" />
        </div>
    </div>
@else
    <x-skeleton {{ $attributes->merge(['class' => $sizeClasses . ' rounded-full ' . $class]) }} />
@endif
