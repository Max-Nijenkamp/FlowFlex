<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.table :rows="13" :cols="5" />
        @else
            {{-- Switchboard+ table: white card, mono uppercase headers, zebra
                 rows, mono numbers; weeks that go negative carry a red edge. --}}
            <div class="overflow-x-auto rounded-[14px] border border-[#D8D4CA] bg-white shadow-[0_1px_2px_rgba(17,24,39,0.04)] dark:border-gray-700 dark:bg-gray-900">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#E7E4DD] text-left dark:border-gray-700">
                            <th class="px-5 py-3 font-mono text-[10px] font-medium uppercase tracking-[0.14em] text-[#98A0AB]">Week</th>
                            <th class="px-5 py-3 text-right font-mono text-[10px] font-medium uppercase tracking-[0.14em] text-[#98A0AB]">Opening</th>
                            <th class="px-5 py-3 text-right font-mono text-[10px] font-medium uppercase tracking-[0.14em] text-[#98A0AB]">Inflows</th>
                            <th class="px-5 py-3 text-right font-mono text-[10px] font-medium uppercase tracking-[0.14em] text-[#98A0AB]">Outflows</th>
                            <th class="px-5 py-3 text-right font-mono text-[10px] font-medium uppercase tracking-[0.14em] text-[#98A0AB]">Closing</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->getWeeks() as $i => $week)
                            <tr @class([
                                'border-b border-[#E7E4DD] last:border-b-0 dark:border-gray-800',
                                'bg-[#FAF9F5] dark:bg-white/5' => $i % 2 === 0 && $week->closing_cents >= 0,
                                'bg-red-50 shadow-[inset_2px_0_0_#E11D48] dark:bg-red-950/30' => $week->closing_cents < 0,
                            ])>
                                <td class="px-5 py-2.5 font-mono text-xs text-[#4B5563] dark:text-gray-300">{{ $week->week_start->format('d M') }}</td>
                                <td class="px-5 py-2.5 text-right font-mono text-xs text-[#4B5563] dark:text-gray-300">€{{ number_format($week->opening_cents / 100) }}</td>
                                <td class="px-5 py-2.5 text-right font-mono text-xs text-[#0E8C61]">+€{{ number_format($week->inflow_cents / 100) }}</td>
                                <td class="px-5 py-2.5 text-right font-mono text-xs text-[#E11D48]">−€{{ number_format($week->outflow_cents / 100) }}</td>
                                <td @class([
                                    'px-5 py-2.5 text-right font-mono text-xs font-bold',
                                    'text-gray-950 dark:text-white' => $week->closing_cents >= 0,
                                    'text-[#E11D48]' => $week->closing_cents < 0,
                                ])>€{{ number_format($week->closing_cents / 100) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="mt-3 font-mono text-[11px] text-[#98A0AB]">
                13 weeks ahead · reads open invoices, bills and payroll runs · weeks in red close below zero
            </p>
        @endif
    </div>
</x-filament-panels::page>
