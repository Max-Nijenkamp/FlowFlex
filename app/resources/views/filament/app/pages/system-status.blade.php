<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" wire:poll.60s>
        @forelse ($this->getChecks() as $check)
            <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-900 flex items-center justify-between">
                <div>
                    <h3 class="font-medium">{{ $check['name'] }}</h3>
                    <p class="text-sm text-gray-500">{{ $check['message'] }}</p>
                </div>
                <x-filament::badge :color="$check['status'] === 'ok' ? 'success' : ($check['status'] === 'warning' ? 'warning' : 'danger')">
                    {{ $check['status'] }}
                </x-filament::badge>
            </div>
        @empty
            <p class="text-gray-500">No health results yet — checks run every minute.</p>
        @endforelse
    </div>
</x-filament-panels::page>
