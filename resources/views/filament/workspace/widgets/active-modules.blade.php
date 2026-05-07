<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Active Modules</x-slot>
        <x-slot name="description">{{ $totalActive }} {{ Str::plural('module', $totalActive) }} enabled in your workspace</x-slot>
        <x-slot name="headerEnd">
            <a
                href="{{ route('filament.workspace.pages.manage-modules') }}"
                style="font-size:13px;font-weight:500;color:var(--fi-primary-500,#2199C8);text-decoration:none;"
                onmouseenter="this.style.textDecoration='underline'"
                onmouseleave="this.style.textDecoration='none'"
            >Manage →</a>
        </x-slot>

        @if ($modulesByDomain->isEmpty())
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 0;text-align:center;">
                <x-filament::icon icon="heroicon-o-puzzle-piece" class="h-12 w-12 text-gray-300 dark:text-gray-600" style="margin-bottom:12px;" />
                <p style="font-size:14px;font-weight:600;color:var(--fi-color-gray-800,#1f2937);margin-bottom:4px;">No modules active yet</p>
                <p style="font-size:13px;color:var(--fi-color-gray-500,#6b7280);">
                    <a href="{{ route('filament.workspace.pages.manage-modules') }}" style="color:var(--fi-primary-500,#2199C8);">Enable modules</a>
                    to unlock features for your workspace.
                </p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:24px;">
                @foreach ($modulesByDomain as $domain => $modules)
                    @php
                        $label = $domainLabels[$domain] ?? ucfirst($domain);
                        $color = $modules->first()->color ?? '#6B7280';
                    @endphp

                    <div>
                        {{-- Domain header --}}
                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                            <div style="width:8px;height:8px;border-radius:50%;background-color:{{ $color }};flex-shrink:0;"></div>
                            <span style="font-size:11px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--fi-color-gray-500,#6b7280);">{{ $label }}</span>
                            <span style="font-size:11px;font-weight:600;color:{{ $color }};background-color:{{ $color }}18;padding:1px 8px;border-radius:9999px;">{{ $modules->count() }}</span>
                        </div>

                        {{-- Module chips grid --}}
                        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:8px;">
                            @foreach ($modules as $module)
                                <div style="
                                    display:flex;
                                    align-items:center;
                                    gap:8px;
                                    padding:8px 12px;
                                    border-radius:8px;
                                    background-color:{{ $module->color ?? '#6B7280' }}0E;
                                    border:1px solid {{ $module->color ?? '#6B7280' }}25;
                                ">
                                    <div style="
                                        width:26px;height:26px;
                                        border-radius:6px;
                                        background-color:{{ $module->color ?? '#6B7280' }}20;
                                        display:flex;align-items:center;justify-content:center;
                                        flex-shrink:0;
                                    ">
                                        <x-filament::icon
                                            :icon="$module->icon ?? 'heroicon-o-puzzle-piece'"
                                            class="h-3.5 w-3.5"
                                            style="color:{{ $module->color ?? '#6B7280' }};"
                                            aria-hidden="true"
                                        />
                                    </div>
                                    <span style="font-size:12px;font-weight:500;color:var(--fi-color-gray-800,#1f2937);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                        {{ $module->name }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
