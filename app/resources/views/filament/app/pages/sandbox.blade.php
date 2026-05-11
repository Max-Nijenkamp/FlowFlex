<x-filament-panels::page>
    <div class="max-w-2xl mx-auto space-y-6">
        @if ($this->sandbox === null)
            {{-- No sandbox provisioned --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-8 text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
                    <x-heroicon-o-beaker class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                </div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">No sandbox provisioned</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                    A sandbox gives you an isolated environment to test integrations,
                    import test data, and experiment safely without affecting live data.
                </p>
                <x-filament::button wire:click="provisionSandbox" wire:loading.attr="disabled" wire:target="provisionSandbox">
                    <span wire:loading.remove wire:target="provisionSandbox">Provision sandbox</span>
                    <span wire:loading wire:target="provisionSandbox">Provisioning…</span>
                </x-filament::button>
            </div>
        @else
            @php
                $statusColor = match($sandbox->status) {
                    'active'       => 'text-green-700 bg-green-100 dark:text-green-400 dark:bg-green-900/30',
                    'provisioning' => 'text-yellow-700 bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30',
                    'resetting'    => 'text-orange-700 bg-orange-100 dark:text-orange-400 dark:bg-orange-900/30',
                    'error'        => 'text-red-700 bg-red-100 dark:text-red-400 dark:bg-red-900/30',
                    default        => 'text-gray-600 bg-gray-100 dark:text-gray-400 dark:bg-gray-700/50',
                };
            @endphp

            {{-- Sandbox details --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Sandbox environment</h2>
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusColor }}">
                        {{ ucfirst($sandbox->status) }}
                    </span>
                </div>

                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-0.5">Subdomain</dt>
                        <dd class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $sandbox->subdomain ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-0.5">Seed type</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300 capitalize">
                            {{ $sandbox->seed_type ?? '—' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-0.5">Provisioned</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $sandbox->provisioned_at?->diffForHumans() ?? $sandbox->created_at->diffForHumans() }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-0.5">Last synced</dt>
                        <dd class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $sandbox->last_synced_at?->diffForHumans() ?? 'Never' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Actions --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Actions</h3>
                <div class="flex flex-wrap gap-3">
                    <x-filament::button
                        color="warning"
                        wire:click="resetSandbox"
                        wire:loading.attr="disabled"
                        wire:target="resetSandbox"
                        wire:confirm="This will erase all sandbox data. Are you sure?"
                    >
                        <span wire:loading.remove wire:target="resetSandbox">Reset sandbox</span>
                        <span wire:loading wire:target="resetSandbox">Resetting…</span>
                    </x-filament::button>
                </div>
                <p class="mt-3 text-xs text-gray-400 dark:text-gray-500">
                    Resetting wipes all sandbox data and returns it to a blank state. Live data is never affected.
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
