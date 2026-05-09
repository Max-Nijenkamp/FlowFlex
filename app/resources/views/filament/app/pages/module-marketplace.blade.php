<x-filament-panels::page>
    {{-- Summary bar --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Active modules</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->getActiveCount() }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Est. monthly cost</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $this->getMonthlyEstimate() }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">based on current user count</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Included free</p>
            <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">Core platform</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Auth, RBAC, notifications, audit log</p>
        </div>
    </div>

    {{-- Module sections --}}
    <div class="space-y-8">
        @forelse ($this->getModules() as $domain => $domainModules)
            @php
                $isCore = $domain === 'core';
                $activeInDomain = $domainModules->filter(fn ($m) => in_array($m->module_key, $activeKeys))->count();
                $domainLabel = match($domain) {
                    'core'       => 'Core Platform',
                    'hr'         => 'HR & People',
                    'crm'        => 'CRM & Sales',
                    'finance'    => 'Finance & Accounting',
                    'projects'   => 'Projects & Work',
                    'operations' => 'Operations',
                    'marketing'  => 'Marketing & Content',
                    'analytics'  => 'Analytics & BI',
                    default      => ucfirst($domain),
                };
            @endphp

            <div>
                <div class="mb-3 flex items-center gap-3">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">{{ $domainLabel }}</h2>
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                        {{ $domainModules->count() }} {{ Str::plural('module', $domainModules->count()) }}
                    </span>
                    @if (! $isCore && $activeInDomain > 0)
                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
                            {{ $activeInDomain }} active
                        </span>
                    @endif
                    @if ($isCore)
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                            Always included
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($domainModules as $module)
                        @php
                            $isActive = in_array($module->module_key, $activeKeys);
                            $isCoreModule = str_starts_with($module->module_key, 'core.');
                        @endphp

                        <div @class([
                            'relative flex flex-col rounded-xl border p-4 transition-all',
                            'border-green-200 bg-green-50/50 dark:border-green-800/50 dark:bg-green-900/10' => $isActive && !$isCoreModule,
                            'border-blue-200 bg-blue-50/30 dark:border-blue-800/50 dark:bg-blue-900/10' => $isCoreModule,
                            'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900' => !$isActive && !$isCoreModule,
                        ])>
                            {{-- Status dot --}}
                            @if ($isCoreModule)
                                <span class="absolute right-3 top-3 h-2 w-2 rounded-full bg-blue-500"></span>
                            @elseif ($isActive)
                                <span class="absolute right-3 top-3 h-2 w-2 rounded-full bg-green-500"></span>
                            @else
                                <span class="absolute right-3 top-3 h-2 w-2 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                            @endif

                            <div class="flex-1">
                                <p class="pr-5 text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $module->name }}
                                </p>
                                <p class="mt-0.5 font-mono text-xs text-gray-400 dark:text-gray-500">
                                    {{ $module->module_key }}
                                </p>
                                <p class="mt-2 text-sm font-medium text-gray-600 dark:text-gray-300">
                                    @if ($module->isFree())
                                        <span class="text-green-600 dark:text-green-400">Free</span>
                                    @else
                                        €{{ number_format((float) $module->per_user_monthly_price, 2) }}<span class="text-xs text-gray-400"> / user / mo</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mt-3">
                                @if ($isCoreModule)
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 dark:text-blue-400">
                                        <x-heroicon-s-check-circle class="h-4 w-4" />
                                        Included
                                    </span>
                                @elseif ($isActive)
                                    <x-filament::button
                                        wire:click="disableModule('{{ $module->module_key }}')"
                                        wire:confirm="Your data will be preserved but this module will be hidden from all users. Continue?"
                                        wire:loading.attr="disabled"
                                        wire:target="disableModule('{{ $module->module_key }}')"
                                        color="gray"
                                        size="sm"
                                        outlined
                                    >
                                        Disable
                                    </x-filament::button>
                                @else
                                    <x-filament::button
                                        wire:click="enableModule('{{ $module->module_key }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="enableModule('{{ $module->module_key }}')"
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
            </div>
        @empty
            <div class="rounded-xl border border-dashed border-gray-300 p-12 text-center dark:border-gray-600">
                <x-heroicon-o-puzzle-piece class="mx-auto h-10 w-10 text-gray-400 dark:text-gray-500" />
                <p class="mt-3 text-sm font-medium text-gray-600 dark:text-gray-400">No modules available yet.</p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
