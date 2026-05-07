<x-filament-panels::page>
    @php
        $totalEnabled = count($this->enabledModuleIds);
        $totalModules = $modulesByDomain->flatten()->count();
    @endphp

    {{-- Intro line --}}
    <p class="text-sm text-gray-500 dark:text-gray-400 -mt-2 mb-6">
        <span class="font-semibold text-gray-900 dark:text-white">{{ $totalEnabled }}</span>
        of {{ $totalModules }} modules active — click any card to enable or disable.
    </p>

    @if ($modulesByDomain->isEmpty())
        <x-filament::section>
            <div class="flex flex-col items-center py-12 text-center">
                <x-filament::icon icon="heroicon-o-puzzle-piece" class="mb-4 h-12 w-12 text-gray-300 dark:text-gray-600" />
                <p class="font-semibold text-gray-800 dark:text-gray-200">No modules available</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Contact your administrator to enable modules.</p>
            </div>
        </x-filament::section>
    @else
        @foreach ($modulesByDomain as $domain => $modules)
            @php
                $label       = $domainLabels[$domain] ?? ucfirst($domain);
                $activeCount = collect($modules)->filter(fn ($m) => in_array($m->id, $this->enabledModuleIds))->count();
                $domainColor = $modules->first()->color ?? '#6B7280';
                $badgeStyle  = $activeCount > 0
                    ? "background-color:{$domainColor}20;color:{$domainColor};padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:600;"
                    : "background-color:var(--fi-color-gray-100,#F3F4F6);color:var(--fi-color-gray-500,#6B7280);padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:500;";
            @endphp

            <x-filament::section
                :heading="$label"
                :description="$activeCount . ' of ' . $modules->count() . ' active'"
                collapsible
            >
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px;">
                    @foreach ($modules as $module)
                        @php
                            $isActive   = in_array($module->id, $this->enabledModuleIds);
                            $color      = $module->color ?? '#6B7280';
                            $cardBorder = $isActive ? "1.5px solid {$color}55" : "1.5px solid var(--fi-color-gray-200,#E5E7EB)";
                            $cardBg     = $isActive ? "{$color}10" : "var(--fi-bg,transparent)";
                            $cardLeft   = $isActive ? "3px solid {$color}" : "3px solid transparent";
                        @endphp

                        <div
                            wire:click="toggleModule('{{ $module->id }}')"
                            wire:loading.class="opacity-50"
                            wire:target="toggleModule('{{ $module->id }}')"
                            role="switch"
                            aria-checked="{{ $isActive ? 'true' : 'false' }}"
                            aria-label="{{ $isActive ? 'Disable' : 'Enable' }} {{ $module->name }}"
                            tabindex="0"
                            wire:keydown.space.prevent="toggleModule('{{ $module->id }}')"
                            style="
                                cursor: pointer;
                                border-radius: 12px;
                                border: {{ $cardBorder }};
                                border-left: {{ $cardLeft }};
                                background-color: {{ $cardBg }};
                                padding: 16px;
                                transition: box-shadow 0.15s, transform 0.15s;
                                display: flex;
                                flex-direction: column;
                                gap: 0;
                                user-select: none;
                            "
                            onmouseenter="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.10)';this.style.transform='translateY(-2px)'"
                            onmouseleave="this.style.boxShadow='none';this.style.transform='translateY(0)'"
                        >
                            {{-- Top row: icon + toggle --}}
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px;">
                                <div style="
                                    width:40px;height:40px;border-radius:10px;
                                    background-color:{{ $color }}20;
                                    display:flex;align-items:center;justify-content:center;
                                    flex-shrink:0;
                                ">
                                    <x-filament::icon
                                        :icon="$module->icon ?? 'heroicon-o-puzzle-piece'"
                                        class="h-5 w-5"
                                        style="color:{{ $color }};"
                                        aria-hidden="true"
                                    />
                                </div>

                                {{-- Toggle pill --}}
                                <div style="
                                    position:relative;
                                    width:36px;height:20px;
                                    border-radius:10px;
                                    background-color:{{ $isActive ? $color : '#D1D5DB' }};
                                    flex-shrink:0;
                                    margin-top:2px;
                                    transition:background-color 0.2s;
                                " aria-hidden="true">
                                    <div style="
                                        position:absolute;top:3px;
                                        width:14px;height:14px;
                                        border-radius:50%;
                                        background:white;
                                        box-shadow:0 1px 3px rgba(0,0,0,0.25);
                                        transform:translateX({{ $isActive ? '19px' : '3px' }});
                                        transition:transform 0.2s;
                                    "></div>
                                </div>
                            </div>

                            {{-- Module name --}}
                            <p style="
                                font-size:14px;font-weight:600;line-height:1.3;
                                color:{{ $isActive ? $color : 'var(--fi-color-gray-950,#111827)' }};
                                margin-bottom:6px;
                            ">{{ $module->name }}</p>

                            {{-- Description --}}
                            @if ($module->description)
                                <p style="
                                    font-size:12px;line-height:1.5;
                                    color:var(--fi-color-gray-500,#6B7280);
                                    display:-webkit-box;
                                    -webkit-line-clamp:2;
                                    -webkit-box-orient:vertical;
                                    overflow:hidden;
                                ">{{ $module->description }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endforeach
    @endif
</x-filament-panels::page>
