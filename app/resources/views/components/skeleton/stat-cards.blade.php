@props(['count' => 4])
{{-- Skeleton stat tiles — mirrors dashboard stat cards. --}}
<div {{ $attributes->merge(['class' => 'grid animate-pulse gap-4 sm:grid-cols-2 lg:grid-cols-' . min($count, 4)]) }} aria-hidden="true">
    @for ($i = 0; $i < $count; $i++)
        <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900" style="animation-delay: {{ $i * 30 }}ms">
            <div class="h-3 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
            <div class="mt-3 h-7 w-16 rounded bg-gray-200 dark:bg-gray-700"></div>
            <div class="mt-2 h-2 w-32 rounded bg-gray-100 dark:bg-gray-800"></div>
        </div>
    @endfor
</div>
