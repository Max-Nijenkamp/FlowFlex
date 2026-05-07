<div class="fi-wi-stats-overview">
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
        @foreach (range(1, 4) as $i)
            <div class="fi-wi-stats-overview-stat relative rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="animate-pulse space-y-3">
                    <div class="h-3 w-24 rounded bg-gray-200 dark:bg-gray-700"></div>
                    <div class="h-7 w-16 rounded bg-gray-300 dark:bg-gray-600"></div>
                    <div class="h-3 w-32 rounded bg-gray-200 dark:bg-gray-700"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
