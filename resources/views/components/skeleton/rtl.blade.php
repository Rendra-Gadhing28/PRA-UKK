@props([
    'direction' => 'rtl',
    'class' => ''
])

<div dir="{{ $direction }}" {{ $attributes->merge(['class' => 'space-y-4 p-5 rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white/70 dark:bg-slate-900/70 shadow-sm ' . $class]) }}>
    <div class="flex items-center space-x-4 space-x-reverse">
        <x-skeleton class="h-12 w-12 rounded-full shrink-0" />
        <div class="space-y-2 flex-1">
            <x-skeleton class="h-4 w-[200px]" />
            <x-skeleton class="h-4 w-[150px]" />
        </div>
    </div>
    <div class="space-y-2 pt-2">
        <x-skeleton class="h-4 w-full" />
        <x-skeleton class="h-4 w-[85%]" />
        <x-skeleton class="h-4 w-[60%]" />
    </div>
</div>
