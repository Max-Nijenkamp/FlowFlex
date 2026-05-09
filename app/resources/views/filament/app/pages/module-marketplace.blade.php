<x-filament-panels::page>
    <div class="space-y-6">
        @forelse ($this->getModules() as $domain => $domainModules)
            <x-filament::section :heading="ucfirst($domain)">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($domainModules as $module)
                        @php $isActive = in_array($module->module_key, $activeKeys); @endphp
                        <div class="flex flex-col justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                            <div>
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-medium text-gray-900 dark:text-white">
                                        {{ $module->name }}
                                    </h3>
                                    @if ($isActive)
                                        <x-filament::badge color="success">Active</x-filament::badge>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $module->isFree() ? 'Free' : '€' . number_format($module->per_user_monthly_price, 2) . ' / user / month' }}
                                </p>
                            </div>

                            <div class="mt-4">
                                @if ($isActive)
                                    <x-filament::button
                                        wire:click="disableModule('{{ $module->module_key }}')"
                                        wire:confirm="Your data will be preserved but the module will be hidden from all users. Continue?"
                                        color="gray"
                                        size="sm"
                                    >
                                        Disable
                                    </x-filament::button>
                                @else
                                    <x-filament::button
                                        wire:click="enableModule('{{ $module->module_key }}')"
                                        color="primary"
                                        size="sm"
                                    >
                                        Enable
                                    </x-filament::button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @empty
            <x-filament::section>
                <p class="text-center text-gray-500 dark:text-gray-400">No modules available yet.</p>
            </x-filament::section>
        @endforelse
    </div>
</x-filament-panels::page>
