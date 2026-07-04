{{-- Workspace switcher (owner decision 2026-07-04): no hub page — a menu
     entry at the top of the sidebar opens a modal for panel selection. The
     workspace you're in is always listed and marked as the current one. --}}
@php
    use App\Support\Services\WorkspacePanels;

    $canView = WorkspacePanels::canView();
    $tiles = $canView ? WorkspacePanels::tiles() : collect();
    $isOwner = $canView && WorkspacePanels::isOwner();
@endphp

@if ($canView)
<div class="ff-ws" x-data="{ open: false }" x-on:keydown.escape.window="open = false">
    <button type="button" class="ff-ws-trigger" x-on:click="open = true" title="Switch workspace">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="20" height="20"><rect x="3" y="3" width="6" height="6" rx="1.5"></rect><rect x="11" y="3" width="6" height="6" rx="1.5"></rect><rect x="3" y="11" width="6" height="6" rx="1.5"></rect><rect x="11" y="11" width="6" height="6" rx="1.5"></rect></svg>
        <span class="ff-ws-trigger-label">Workspace</span>
        <span class="ff-ws-trigger-hint">Switch</span>
    </button>

    <template x-teleport="body">
        <div class="ff-ws-overlay" x-show="open" x-cloak x-on:click.self="open = false"
            x-transition:enter="ff-pop-enter" x-transition:enter-start="ff-pop-from" x-transition:enter-end="ff-pop-to"
            x-transition:leave="ff-pop-leave" x-transition:leave-start="ff-pop-to" x-transition:leave-end="ff-pop-from">
            <div class="ff-ws-modal" role="dialog" aria-label="Switch workspace">
                <div class="ff-ws-head">
                    <h2>Switch workspace</h2>
                    <button type="button" class="ff-ws-close" x-on:click="open = false" title="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M5 5l10 10M15 5L5 15"></path></svg>
                    </button>
                </div>

                <div class="ff-ws-list">
                    {{-- The workspace you're in — always listed, always current --}}
                    <a href="{{ url('/app') }}" class="ff-ws-row ff-current">
                        <span class="ff-ws-square" style="background: #38BDF8"></span>
                        <span class="ff-ws-meta">
                            <span class="ff-ws-name">Workspace</span>
                            <span class="ff-ws-blurb">Dashboard, team, billing &amp; settings</span>
                        </span>
                        <span class="ff-ws-current-tag">Current</span>
                    </a>

                    @foreach ($tiles as $tile)
                        <a href="{{ $tile['url'] }}" class="ff-ws-row">
                            <span class="ff-ws-square" style="background: {{ $tile['color'] }}"></span>
                            <span class="ff-ws-meta">
                                <span class="ff-ws-name">{{ $tile['name'] }}</span>
                                <span class="ff-ws-blurb">{{ $tile['blurb'] }}</span>
                            </span>
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
                @endif
            </div>
        </div>
    </template>
</div>
@endif
