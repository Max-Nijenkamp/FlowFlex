{{-- Workspace switcher (owner decision 2026-07-04): no hub page — a modal for
     panel selection. Moved 2026-07-05 from SIDEBAR_NAV_START into the sidebar
     footer, replacing the "Your panels" chips (two switchers is one too many).
     The workspace you're in is always listed and marked as the current one. --}}
@php
    use App\Support\Services\WorkspacePanels;

    $canView = WorkspacePanels::canView();
    $tiles = $canView ? WorkspacePanels::tiles() : collect();
    $isOwner = $canView && WorkspacePanels::isOwner();
    $currentPanel = filament()->getId();
    $triggerLabel = WorkspacePanels::DOMAINS[$currentPanel]['name'] ?? 'Workspace';
    $triggerColor = WorkspacePanels::DOMAINS[$currentPanel]['color'] ?? '#38BDF8';
    $count = $tiles->count() + 1;
@endphp

@if ($canView)
<div class="ff-ws" x-data="{ open: false, switching: false }" x-on:keydown.escape.window="open = false"
    x-on:pageshow.window="switching = false">
    <span class="ff-ws-label">Your workspaces</span>
    <button type="button" class="ff-ws-trigger" x-on:click="open = true; switching = false" title="Switch workspace">
        <span class="ff-ws-dot" style="background: {{ $triggerColor }}"></span>
        <span class="ff-ws-trigger-label">{{ $triggerLabel }}</span>
        <svg class="ff-ws-caret" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><path d="M6.5 8 10 4.5 13.5 8M6.5 12 10 15.5 13.5 12"></path></svg>
    </button>

    <template x-teleport="body">
        <div class="ff-ws-overlay" x-show="open" x-cloak x-on:click.self="open = false"
            x-transition:enter="ff-pop-enter" x-transition:enter-start="ff-pop-from" x-transition:enter-end="ff-pop-to"
            x-transition:leave="ff-pop-leave" x-transition:leave-start="ff-pop-to" x-transition:leave-end="ff-pop-from">
            <div class="ff-ws-modal" role="dialog" aria-label="Switch workspace">
                <div class="ff-ws-head">
                    <div class="ff-ws-head-meta">
                        <h2>Switch workspace</h2>
                        <span class="ff-ws-sub">{{ $count }} {{ str('workspace')->plural($count) }} active</span>
                    </div>
                    <button type="button" class="ff-ws-close" x-on:click="open = false" title="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M5 5l10 10M15 5L5 15"></path></svg>
                    </button>
                </div>

                {{-- Clicking a row starts the leave-feedback instantly; the
                     navigation request runs behind it (perceived-performance
                     rule 3). Anchor clicks bubble up to here. --}}
                <div class="ff-ws-list" x-on:click="switching = true" x-bind:class="{ 'ff-leaving': switching }">
                    {{-- The core workspace — always listed --}}
                    <a href="{{ url('/app') }}" style="--ws-c: #38BDF8" @class(['ff-ws-row', 'ff-current' => $currentPanel === 'app'])>
                        <span class="ff-ws-tile"><span class="ff-ws-square"></span></span>
                        <span class="ff-ws-meta">
                            <span class="ff-ws-name">Workspace</span>
                            <span class="ff-ws-blurb">Dashboard, team, billing &amp; settings</span>
                        </span>
                        @if ($currentPanel === 'app')
                            <span class="ff-ws-current-tag">Current</span>
                        @else
                            <svg class="ff-ws-go" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M7.5 5 12.5 10 7.5 15"></path></svg>
                        @endif
                    </a>

                    @foreach ($tiles as $tile)
                        <a href="{{ $tile['url'] }}" style="--ws-c: {{ $tile['color'] }}" @class(['ff-ws-row', 'ff-current' => $currentPanel === $tile['key']])>
                            <span class="ff-ws-tile"><span class="ff-ws-square"></span></span>
                            <span class="ff-ws-meta">
                                <span class="ff-ws-name">{{ $tile['name'] }}</span>
                                <span class="ff-ws-blurb">{{ $tile['blurb'] }}</span>
                            </span>
                            @if ($currentPanel === $tile['key'])
                                <span class="ff-ws-current-tag">Current</span>
                            @else
                                <svg class="ff-ws-go" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M7.5 5 12.5 10 7.5 15"></path></svg>
                            @endif
                        </a>
                    @endforeach
                </div>

                @if ($tiles->isEmpty())
                    <div class="ff-ws-empty">
                        @if ($isOwner)
                            <p>Activate a module and its workspace shows up here.</p>
                            <a href="{{ url('/app/module-marketplace-page') }}" class="ff-ws-cta">Open the marketplace</a>
                        @else
                            <p>Ask your workspace admin to switch on the modules your team needs.</p>
                        @endif
                    </div>
                @elseif ($isOwner)
                    <div class="ff-ws-foot">
                        <span>Need another workspace?</span>
                        <a href="{{ url('/app/module-marketplace-page') }}">Open the marketplace</a>
                    </div>
                @endif
            </div>
        </div>
    </template>
</div>
@endif
