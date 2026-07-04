<x-filament-panels::page>
    <div class="ff-mkt">
        <input
            type="search"
            class="ff-mkt-search"
            placeholder="Search modules…"
            wire:model.live.debounce.300ms="search"
        />

        @forelse ($this->modules as $domain => $entries)
            {{-- One switchboard card per domain: header + zebra module rows --}}
            <section class="ff-mkt-board">
                <header class="ff-mkt-board-head">
                    <span class="ff-mkt-square" style="background: {{ \App\Filament\App\Pages\ModuleMarketplacePage::DOMAIN_COLORS[$domain] ?? '#64748B' }}"></span>
                    <h2>{{ str($domain)->headline() }}</h2>
                    <span class="ff-mkt-count">{{ $entries->count() }} {{ str('module')->plural($entries->count()) }}</span>
                </header>
                @foreach ($entries as $module)
                    <div class="ff-mkt-row {{ $module->is_subscribed ? '' : 'ff-off' }}">
                        <div class="ff-mkt-row-main">
                            <span class="ff-mkt-name">{{ $module->name }}</span>
                            <span class="ff-mkt-key">{{ $module->module_key }}</span>
                        </div>
                        @unless ($module->is_free)
                            <span class="ff-mkt-price">{{ $module->price_preview }}</span>
                        @endunless
                        <span class="ff-mkt-pill {{ $module->is_free ? 'ff-incl' : ($module->is_subscribed ? 'ff-active' : '') }}">
                            {{ $module->is_free ? 'Included' : ($module->is_subscribed ? 'ON' : 'OFF') }}
                        </span>
                        <span class="ff-mkt-action">
                            @unless ($module->is_free)
                                @if ($module->is_subscribed)
                                    {{ ($this->deactivateAction)(['key' => $module->module_key, 'name' => $module->name]) }}
                                @else
                                    {{ ($this->activateAction)(['key' => $module->module_key, 'name' => $module->name]) }}
                                @endif
                            @endunless
                        </span>
                    </div>
                @endforeach
            </section>
        @empty
            <p class="ff-mkt-empty">No modules match “{{ $search }}” — try a different name or domain.</p>
        @endforelse
    </div>
</x-filament-panels::page>
