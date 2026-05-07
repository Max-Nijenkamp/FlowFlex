<div class="fi-ta rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10">
        <div class="fi-ta-header flex items-center gap-x-4 px-4 py-3 sm:px-6">
            <div class="animate-pulse h-4 w-48 rounded bg-gray-200 dark:bg-gray-700"></div>
        </div>
    </div>
    <div class="divide-y divide-gray-200 dark:divide-white/10">
        @foreach (range(1, 5) as $row)
            <div class="flex items-center gap-x-4 px-4 py-3 sm:px-6">
                <div class="animate-pulse flex flex-1 gap-x-6" style="animation-delay: {{ ($row - 1) * 80 }}ms">
                    <div class="space-y-2 flex-1">
                        <div class="h-3.5 w-40 rounded bg-gray-200 dark:bg-gray-700"></div>
                        <div class="h-3 w-24 rounded bg-gray-100 dark:bg-gray-800"></div>
                    </div>
                    <div class="h-3.5 w-32 rounded bg-gray-200 dark:bg-gray-700 self-center"></div>
                    <div class="h-6 w-8 rounded-full bg-gray-200 dark:bg-gray-700 self-center"></div>
                    <div class="h-6 w-8 rounded-full bg-gray-200 dark:bg-gray-700 self-center"></div>
                    <div class="h-5 w-5 rounded bg-gray-200 dark:bg-gray-700 self-center"></div>
                    <div class="h-3.5 w-20 rounded bg-gray-100 dark:bg-gray-800 self-center"></div>
                </div>
            </div>
        @endforeach
    </div>
</div>
