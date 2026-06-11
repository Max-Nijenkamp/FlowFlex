<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.stat-cards :count="3" />
        @else

    @php($tiles = $this->getTiles())
    @if ($tiles['employee'] === null)
        <p class="text-gray-500">No employee record is linked to your account yet — ask HR to connect your profile.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                <div class="text-sm text-gray-500">You</div>
                <div class="font-semibold">{{ $tiles['employee']->full_name }}</div>
                <div class="text-sm text-gray-500">{{ $tiles['employee']->job_title }}</div>
            </div>
            @isset($tiles['leave_remaining'])
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Leave remaining</div>
                    <div class="text-2xl font-semibold">{{ $tiles['leave_remaining'] }} days</div>
                </div>
            @endisset
            @isset($tiles['open_tasks'])
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">
                    <div class="text-sm text-gray-500">Open onboarding tasks</div>
                    <div class="text-2xl font-semibold">{{ $tiles['open_tasks'] }}</div>
                </div>
            @endisset
        </div>
    @endif

        @endif
    </div>
</x-filament-panels::page>
