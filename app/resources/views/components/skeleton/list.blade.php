@props(['rows' => 6])
{{-- Skeleton list — avatar + two-line rows. --}}
<div {{ $attributes->merge(['class' => 'animate-pulse space-y-3']) }} aria-hidden="true">
    @for ($i = 0; $i < $rows; $i++)
        <div class="flex items-center gap-3 rounded-lg border border-gray-100 p-3 dark:border-gray-800" style="animation-delay: {{ $i * 30 }}ms">
            <div class="size-9 shrink-0 rounded-full bg-gray-200 dark:bg-gray-700"></div>
            <div class="flex-1 space-y-2">
                <div class="h-3 w-1/3 rounded bg-gray-200 dark:bg-gray-700"></div>
                <div class="h-2 w-1/2 rounded bg-gray-100 dark:bg-gray-800"></div>
            </div>
        </div>
    @endfor
</div>
