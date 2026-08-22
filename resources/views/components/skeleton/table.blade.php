@props([
    'rows' => 5,
    'cols' => 4,
    'class' => ''
])

<div {{ $attributes->merge(['class' => 'w-full overflow-x-auto rounded-xl border border-slate-200/80 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm ' . $class]) }}>
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40">
                @for ($c = 0; $c < $cols; $c++)
                    <th class="p-4">
                        <x-skeleton class="h-4 w-20" />
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            @for ($r = 0; $r < $rows; $r++)
                <tr>
                    @for ($c = 0; $c < $cols; $c++)
                        <td class="p-4">
                            @if($c === 0)
                                <div class="flex items-center space-x-3">
                                    <x-skeleton class="h-8 w-8 rounded-full shrink-0" />
                                    <x-skeleton class="h-4 w-28" />
                                </div>
                            @elseif($c === $cols - 1)
                                <x-skeleton class="h-8 w-16 rounded-md" />
                            @else
                                <x-skeleton class="h-4 w-24" />
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>
</div>
