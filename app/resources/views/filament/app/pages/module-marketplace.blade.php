<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.stat-cards :count="6" />
        @else

    @foreach ($this->getModulesByDomain() as $domain => $modules)
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 capitalize">{{ $domain }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($modules as $module)
                    <div @class([
                        'rounded-xl border p-4 bg-white dark:bg-gray-900',
                        'border-primary-400 ring-1 ring-primary-400' => $module->is_active_for_company,
                        'border-gray-200 dark:border-gray-700' => ! $module->is_active_for_company,
                    ])>
                        <div class="flex items-center justify-between">
                            <h3 class="font-medium">{{ $module->name }}</h3>
                            @if ($module->is_free_core)
                                <x-filament::badge color="success">Included</x-filament::badge>
                            @elseif ($module->is_active_for_company)
                                <x-filament::badge color="info">Active</x-filament::badge>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            @if ($module->per_user_monthly_price_cents === 0)
                                Free — included with every subscription
                            @else
                                €{{ number_format($module->per_user_monthly_price_cents / 100, 2) }}/user/month
                                — adds €{{ number_format($module->price_preview_cents / 100, 2) }}/month at your current user count
                            @endif
                        </p>
                        @if (! $module->is_free_core && $this->canManage())
                            <div class="mt-3">
                                @if ($module->is_active_for_company)
                                    <x-filament::button color="danger" size="sm"
                                        wire:click="deactivate('{{ $module->module_key }}')"
                                        wire:confirm="Deactivate {{ $module->name }}? Access is gated, data is retained.">
                                        Deactivate
                                    </x-filament::button>
                                @else
                                    <x-filament::button size="sm" wire:click="activate('{{ $module->module_key }}')">
                                        Activate
                                    </x-filament::button>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

        @endif
    </div>
</x-filament-panels::page>
