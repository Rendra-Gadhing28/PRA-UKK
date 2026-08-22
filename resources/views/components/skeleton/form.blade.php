@props([
    'fields' => 3,
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'space-y-6 max-w-md ' . $class]) }}>
    @for ($i = 0; $i < $fields; $i++)
        <div class="space-y-2">
            <x-skeleton class="h-4 w-24" />
            <x-skeleton class="h-10 w-full rounded-md" />
        </div>
    @endfor
    <div class="pt-2 flex items-center justify-between">
        <x-skeleton class="h-10 w-32 rounded-md bg-slate-300 dark:bg-slate-600" />
        <x-skeleton class="h-4 w-20" />
    </div>
</div>
