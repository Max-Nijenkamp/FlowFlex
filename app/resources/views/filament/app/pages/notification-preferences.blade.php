<x-filament-panels::page>
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Quiet hours</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Push and SMS notifications are suppressed during this window. Critical alerts are always delivered.</p>
            <div class="flex items-center gap-4">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Start</label>
                    <input type="time" wire:model="quietStart" class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-sm px-3 py-2">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">End</label>
                    <input type="time" wire:model="quietEnd" class="w-full rounded-lg border-gray-200 dark:border-gray-600 dark:bg-gray-700 text-sm px-3 py-2">
                </div>
            </div>
            <div class="mt-4">
                <x-filament::button wire:click="saveQuietHours" size="sm">Save quiet hours</x-filament::button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Channel preferences</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Configure how you receive different types of notifications. Critical events always deliver on all channels.</p>
            <p class="text-sm text-gray-400 italic">Per-event preferences will be available as domain modules are activated.</p>
        </div>
    </div>
</x-filament-panels::page>
