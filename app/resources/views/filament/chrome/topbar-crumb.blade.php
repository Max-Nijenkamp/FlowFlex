@php
    $panelLabel = filament()->getId() === 'admin' ? 'Admin' : 'Workspace';

    // The topbar is its own Livewire component, so derive the page from the
    // route name: filament.{panel}.pages.dashboard -> "Dashboard".
    $routeName = request()->route()?->getName() ?? '';
    $pageSlug = str_contains($routeName, '.pages.') ? str($routeName)->afterLast('.pages.') : null;
    $pageTitle = $pageSlug ? str($pageSlug)->replace(['-', '.'], ' ')->headline() : null;
@endphp

<div class="ff-crumb">
    <span>{{ $panelLabel }}</span>
    @if ($pageTitle)
        <span class="ff-crumb-sep">›</span>
        <span class="ff-crumb-here">{{ $pageTitle }}</span>
    @endif
</div>
