@props(['fields' => 6])
{{-- Skeleton form — label + input pairs. --}}
<div {{ $attributes->merge(['class' => 'animate-pulse space-y-5']) }} aria-hidden="true">
    @for ($i = 0; $i < $fields; $i++)
        <div style="animation-delay: {{ $i * 30 }}ms">
            <div class="h-3 w-28 rounded bg-gray-200 dark:bg-gray-700"></div>
            <div class="mt-2 h-9 w-full rounded-lg bg-gray-100 dark:bg-gray-800"></div>
        </div>
    @endfor
</div>
