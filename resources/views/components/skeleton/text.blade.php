@props([
    'lines' => 3,
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'space-y-2.5 ' . $class]) }}>
    @for ($i = 0; $i < $lines; $i++)
        @php
            $widths = ['w-full', 'w-[92%]', 'w-[78%]', 'w-[88%]', 'w-[65%]'];
            $widthClass = $widths[$i % count($widths)];
        @endphp
        <x-skeleton class="h-4 {{ $widthClass }}" />
    @endfor
</div>
