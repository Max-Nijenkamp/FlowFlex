<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.stat-cards :count="6" />
        @else
            @php($pl = $this->getProfitLoss())
            @php($bs = $this->getBalanceSheet())
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Revenue (YTD)</div>
                    <div class="text-2xl font-semibold">€{{ number_format($pl['revenue_cents'] / 100, 2) }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Expenses (YTD)</div>
                    <div class="text-2xl font-semibold">€{{ number_format($pl['expense_cents'] / 100, 2) }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Net profit (YTD)</div>
                    <div class="text-2xl font-semibold {{ $pl['net_profit_cents'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                        €{{ number_format($pl['net_profit_cents'] / 100, 2) }}
                    </div>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Assets</div>
                    <div class="text-2xl font-semibold">€{{ number_format($bs['assets_cents'] / 100, 2) }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Liabilities</div>
                    <div class="text-2xl font-semibold">€{{ number_format($bs['liabilities_cents'] / 100, 2) }}</div>
                </div>
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Equity</div>
                    <div class="text-2xl font-semibold">€{{ number_format($bs['equity_cents'] / 100, 2) }}</div>
                </div>
            </div>
            <h3 class="font-semibold mt-8 mb-3">P&L by account</h3>
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($pl['by_account'] as $code => $cents)
                    <div class="flex justify-between px-4 py-2 text-sm">
                        <span class="text-gray-500">{{ $code }}</span>
                        <span>€{{ number_format($cents / 100, 2) }}</span>
                    </div>
                @empty
                    <p class="p-4 text-gray-500 text-sm">No journal activity yet.</p>
                @endforelse
            </div>
        @endif
    </div>
</x-filament-panels::page>
