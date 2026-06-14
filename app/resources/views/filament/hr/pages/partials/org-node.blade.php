@php
    $initials = collect(explode(' ', trim($node['name'])))
        ->filter()
        ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $reportCount = count($node['children']);
@endphp

<div>
    <div class="flex max-w-md items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-100 text-sm font-semibold text-primary-700 dark:bg-primary-400/10 dark:text-primary-400">
            {{ $initials }}
        </div>

        <div class="min-w-0">
            <div class="truncate font-bold text-gray-950 dark:text-white">{{ $node['name'] }}</div>
            <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $node['title'] }}</div>
        </div>

        @if ($reportCount > 0)
            <span class="ms-auto inline-flex shrink-0 items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                {{ $reportCount }} {{ Str::plural('report', $reportCount) }}
            </span>
        @endif
    </div>

    @if ($reportCount > 0)
        <div class="ms-5 mt-3 space-y-3 border-s-2 border-gray-200 ps-5 dark:border-gray-700">
            @foreach ($node['children'] as $child)
                @include('filament.hr.pages.partials.org-node', ['node' => $child])
            @endforeach
        </div>
    @endif
</div>
