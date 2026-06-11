<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.table :rows="13" :cols="5" />
        @else
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 text-left">
                        <tr>
                            <th class="px-4 py-3">Week</th>
                            <th class="px-4 py-3 text-right">Opening</th>
                            <th class="px-4 py-3 text-right">Inflows</th>
                            <th class="px-4 py-3 text-right">Outflows</th>
                            <th class="px-4 py-3 text-right">Closing</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($this->getWeeks() as $week)
                            <tr @class(['bg-red-50 dark:bg-red-950/30' => $week->closing_cents < 0])>
                                <td class="px-4 py-2">{{ $week->week_start->format('d M') }}</td>
                                <td class="px-4 py-2 text-right">€{{ number_format($week->opening_cents / 100) }}</td>
                                <td class="px-4 py-2 text-right text-emerald-600">+€{{ number_format($week->inflow_cents / 100) }}</td>
                                <td class="px-4 py-2 text-right text-red-600">−€{{ number_format($week->outflow_cents / 100) }}</td>
                                <td class="px-4 py-2 text-right font-medium">€{{ number_format($week->closing_cents / 100) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-filament-panels::page>
