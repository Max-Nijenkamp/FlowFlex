<x-filament-panels::page>
    <div class="ff-mkt">
        <input
            type="search"
            class="ff-mkt-search"
            placeholder="Search modules…"
            wire:model.live.debounce.300ms="search"
        />

        @forelse ($this->modules as $domain => $entries)
            <section class="ff-mkt-domain">
                <h2 class="ff-mkt-domain-label">
                    <span class="ff-mkt-square" style="background: {{ \App\Filament\App\Pages\ModuleMarketplacePage::DOMAIN_COLORS[$domain] ?? '#64748B' }}"></span>
                    {{ str($domain)->headline() }}
                </h2>
                <div class="ff-mkt-grid">
                    @foreach ($entries as $module)
                        <article class="ff-mkt-card {{ $module->is_subscribed ? 'ff-on' : '' }}">
                            <header>
                                <h3>{{ $module->name }}</h3>
                                <span class="ff-mkt-pill {{ $module->is_free ? 'ff-incl' : ($module->is_subscribed ? 'ff-active' : '') }}">
                                    {{ $module->is_free ? 'Included' : ($module->is_subscribed ? 'ON' : 'OFF') }}
                                </span>
                            </header>
                            <p class="ff-mkt-key">{{ $module->module_key }}</p>
                            <p class="ff-mkt-price">{{ $module->price_preview }}</p>
                            @unless ($module->is_free)
                                <div class="ff-mkt-actions">
                                    @if ($module->is_subscribed)
                                        {{ ($this->deactivateAction)(['key' => $module->module_key, 'name' => $module->name]) }}
                                    @else
                                        {{ ($this->activateAction)(['key' => $module->module_key, 'name' => $module->name]) }}
                                    @endif
                                </div>
                            @endunless
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <p class="ff-mkt-empty">No modules match “{{ $search }}” — try a different name or domain.</p>
        @endforelse
    </div>
</x-filament-panels::page>
