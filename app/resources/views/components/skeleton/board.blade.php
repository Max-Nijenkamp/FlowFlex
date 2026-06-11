@props(['columns' => 4, 'cards' => 3])
{{-- Skeleton kanban board — mirrors pipeline layout. --}}
<div {{ $attributes->merge(['class' => 'grid animate-pulse gap-4']) }} style="grid-template-columns: repeat({{ $columns }}, minmax(16rem, 1fr));" aria-hidden="true">
    @for ($c = 0; $c < $columns; $c++)
        <div class="rounded-xl bg-gray-50 p-3 dark:bg-gray-800/50">
            <div class="mb-3 h-3 w-20 rounded bg-gray-200 dark:bg-gray-700"></div>
            <div class="space-y-2">
                @for ($i = 0; $i < $cards; $i++)
                    <div class="rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900" style="animation-delay: {{ ($c * $cards + $i) * 30 }}ms">
                        <div class="h-3 w-3/4 rounded bg-gray-200 dark:bg-gray-700"></div>
                        <div class="mt-2 h-2 w-1/2 rounded bg-gray-100 dark:bg-gray-800"></div>
                    </div>
                @endfor
            </div>
        </div>
    @endfor
</div>
