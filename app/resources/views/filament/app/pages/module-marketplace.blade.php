<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.stat-cards :count="6" />
        @else
            {{-- Toolbar: search + live cost summary --}}
            <x-filament::section>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="w-full sm:max-w-xs">
                        <x-filament::input.wrapper prefix-icon="heroicon-o-magnifying-glass">
                            <x-filament::input
                                type="search"
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search modules…"
                            />
                        </x-filament::input.wrapper>
                    </div>

                    <div class="flex items-center gap-6 text-sm">
                        <div>
                            <span class="font-semibold text-gray-950 dark:text-white">{{ $this->getActiveCount() }}</span>
                            <span class="text-gray-500 dark:text-gray-400">modules active</span>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-950 dark:text-white">€{{ number_format($this->getActiveMonthlyCents() / 100, 2) }}</span>
                            <span class="text-gray-500 dark:text-gray-400">/ month at current team size</span>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            {{-- Per-domain switchboards (design §20): collapsed dropdowns with
                 "N of M on" headers; searching expands everything that matches. --}}
            @forelse ($this->getModulesByDomain() as $domain => $modules)
                @php($onCount = collect($modules)->where('is_active_for_company', true)->count())
                <x-filament::section class="mt-6" collapsible :collapsed="$search === ''">
                    <x-slot name="heading">
                        <span class="capitalize">{{ $domain }}</span>
                    </x-slot>
                    <x-slot name="description">
                        {{ $onCount }} of {{ count($modules) }} on
                        @php($subtotal = collect($modules)->where('is_active_for_company', true)->sum('per_user_monthly_price_cents'))
                        @if ($subtotal > 0)
                            · €{{ number_format($subtotal / 100, 2) }}/user
                        @endif
                    </x-slot>

                    <div class="divide-y divide-gray-200 dark:divide-white/10 -mx-6 -my-4">
                        @foreach ($modules as $i => $module)
                            <div @class([
                                'flex items-center justify-between gap-4 px-6 py-3',
                                'bg-[#FAF9F5] dark:bg-white/5' => $i % 2 === 0,
                            ])>
                                <div class="min-w-0">
                                    <span @class([
                                        'block truncate text-sm font-semibold text-gray-950 dark:text-white',
                                        'opacity-50' => ! $module->is_active_for_company && ! $module->is_free_core,
                                    ])>{{ $module->name }}</span>
                                    <span class="block font-mono text-[11px] text-gray-400">
                                        @if ($module->per_user_monthly_price_cents === 0)
                                            included
                                        @else
                                            €{{ number_format($module->per_user_monthly_price_cents / 100, 2) }}/user
                                            · +€{{ number_format($module->price_preview_cents / 100, 2) }}/month at your team size
                                        @endif
                                    </span>
                                </div>

                                <div class="flex flex-none items-center gap-3">
                                    @if ($module->is_free_core)
                                        <x-filament::badge color="success">Included</x-filament::badge>
                                    @elseif ($this->canManage())
                                        @if ($module->is_active_for_company)
                                            <x-filament::badge color="info">Active</x-filament::badge>
                                            <x-filament::button color="danger" size="xs" outlined
                                                wire:click="deactivate('{{ $module->module_key }}')"
                                                wire:confirm="Deactivate {{ $module->name }}? Access is gated, data is retained.">
                                                Switch off
                                            </x-filament::button>
                                        @else
                                            <x-filament::button size="xs" wire:click="activate('{{ $module->module_key }}')">
                                                Switch on
                                            </x-filament::button>
                                        @endif
                                    @elseif ($module->is_active_for_company)
                                        <x-filament::badge color="info">Active</x-filament::badge>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>
            @empty
                <x-filament::section class="mt-6">
                    <div class="py-8 text-center">
                        <p class="font-medium text-gray-950 dark:text-white">No modules match “{{ $search }}”</p>
                        <p class="mt-1 text-sm text-gray-500">Try a different name or domain.</p>
                    </div>
                </x-filament::section>
            @endforelse
        @endif
    </div>
</x-filament-panels::page>
