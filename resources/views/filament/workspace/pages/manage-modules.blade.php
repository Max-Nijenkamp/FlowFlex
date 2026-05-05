<x-filament-panels::page>
    <div class="space-y-10">
        @foreach ($modulesByDomain as $domain => $modules)
            @php $label = $domainLabels[$domain] ?? ucfirst($domain); @endphp

            <div>
                {{-- Domain section label — overline treatment per branding --}}
                <div class="mb-5 flex items-center gap-3">
                    <span class="text-xs font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500 whitespace-nowrap">
                        {{ $label }}
                    </span>
                    <div class="h-px flex-1 bg-gray-200 dark:bg-white/10" role="separator" aria-hidden="true"></div>
                </div>

                {{-- Module grid — 3 cols desktop, 2 tablet, 1 mobile --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($modules as $module)
                        @php $active = in_array($module->id, $this->enabledModuleIds); @endphp

                        <div @class([
                            'relative flex flex-col rounded-lg border bg-white p-5 shadow-sm transition-shadow duration-150 dark:bg-gray-900',
                            'border-gray-200 dark:border-white/10' => ! $active,
                            'border-primary-500 ring-1 ring-primary-500 dark:border-primary-400 dark:ring-primary-400' => $active,
                        ])>
                            {{-- Header row: domain icon + toggle --}}
                            <div class="mb-4 flex items-start justify-between gap-3">
                                {{-- Domain-coloured icon — background at 10% opacity via inline style only --}}
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                                    style="background-color: {{ $module->color ?? '#6B7280' }}1A;"
                                    aria-hidden="true"
                                >
                                    <x-filament::icon
                                        :icon="$module->icon ?? 'heroicon-o-puzzle-piece'"
                                        class="h-5 w-5"
                                        style="color: {{ $module->color ?? '#6B7280' }};"
                                    />
                                </div>

                                {{-- Toggle switch — role=switch per WAI-ARIA --}}
                                <button
                                    wire:click="toggleModule('{{ $module->id }}')"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="opacity-50 cursor-wait"
                                    wire:target="toggleModule('{{ $module->id }}')"
                                    type="button"
                                    role="switch"
                                    aria-checked="{{ $active ? 'true' : 'false' }}"
                                    aria-label="{{ $active ? 'Disable' : 'Enable' }} {{ $module->name }}"
                                    class="relative inline-flex h-5 w-10 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                                    style="background-color: {{ $active ? ($module->color ?? '#2199C8') : '#D1D5DB' }};"
                                >
                                    <span @class([
                                        'inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow-sm transition-transform duration-150',
                                        'translate-x-5' => $active,
                                        'translate-x-1' => ! $active,
                                    ])></span>
                                </button>
                            </div>

                            {{-- Module name — text-h6 weight --}}
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $module->name }}
                            </h3>

                            {{-- Description --}}
                            @if ($module->description)
                                <p class="mt-1 text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($module->description, 90) }}
                                </p>
                            @endif

                            {{-- Status badge — badge-success pattern: no dot, semantic colours --}}
                            @if ($active)
                                <div class="mt-3">
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-950 dark:text-green-400">
                                        Active
                                    </span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Empty state — per branding.md §9.9 --}}
    @if ($modulesByDomain->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <x-filament::icon
                icon="heroicon-o-puzzle-piece"
                class="mb-4 h-12 w-12 text-gray-300 dark:text-gray-600"
                aria-hidden="true"
            />
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">No modules available</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Contact your administrator to enable modules for your workspace.</p>
        </div>
    @endif
</x-filament-panels::page>
