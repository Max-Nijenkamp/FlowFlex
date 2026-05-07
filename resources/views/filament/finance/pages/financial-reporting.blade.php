<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <h3 class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500 dark:text-gray-400">
                    Period
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    {{ now()->format('F Y') }} (current month)
                </p>
            </div>
            <div class="fi-wi-stats-overview-stat relative rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <h3 class="fi-wi-stats-overview-stat-label text-sm font-medium text-gray-500 dark:text-gray-400">
                    Report Type
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                    P&L Summary · Cash Flow · Outstanding
                </p>
            </div>
        </div>

        <x-filament-widgets::widgets
            :columns="$this->getHeaderWidgetsColumns()"
            :widgets="$this->getHeaderWidgets()"
        />
    </div>
</x-filament-panels::page>
