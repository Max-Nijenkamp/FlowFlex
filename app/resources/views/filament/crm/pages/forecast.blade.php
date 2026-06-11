<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.stat-cards :count="4" />
        @else
            @php($f = $this->getForecast())
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($f['categories'] as $category => $cents)
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                        <div class="text-sm text-gray-500 capitalize">{{ $category }}</div>
                        <div class="text-2xl font-semibold">€{{ number_format($cents / 100) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Weighted pipeline</div>
                    <div class="text-2xl font-semibold">€{{ number_format($f['weighted_cents'] / 100) }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Quota attainment</div>
                    <div class="text-2xl font-semibold">{{ $f['attainment'] }}%</div>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Pipeline coverage</div>
                    <div class="text-2xl font-semibold">{{ $f['coverage'] }}×</div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
