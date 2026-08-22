@props([
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'flex flex-col space-y-3 rounded-xl border border-slate-200/60 dark:border-slate-800/80 p-4 bg-white/50 dark:bg-slate-900/50 ' . $class]) }}>
    <x-skeleton class="h-[125px] w-full rounded-xl" />
    <div class="space-y-2 pt-1">
        <x-skeleton class="h-4 w-[250px]" />
        <x-skeleton class="h-4 w-[200px]" />
    </div>
</div>
