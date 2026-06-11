@props(['rows' => 8, 'cols' => 5])
{{-- Skeleton table — mirrors a Filament table layout (perceived-performance pattern). --}}
<div {{ $attributes->merge(['class' => 'w-full animate-pulse']) }} aria-hidden="true">
    <div class="flex gap-4 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
        @for ($c = 0; $c < $cols; $c++)
            <div class="h-3 flex-1 rounded bg-gray-200 dark:bg-gray-700"></div>
        @endfor
    </div>
    @for ($r = 0; $r < $rows; $r++)
        <div class="flex gap-4 border-b border-gray-100 px-4 py-4 dark:border-gray-800">
            @for ($c = 0; $c < $cols; $c++)
                <div class="h-3 flex-1 rounded bg-gray-100 dark:bg-gray-800" style="animation-delay: {{ $r * 30 }}ms"></div>
            @endfor
        </div>
    @endfor
</div>
