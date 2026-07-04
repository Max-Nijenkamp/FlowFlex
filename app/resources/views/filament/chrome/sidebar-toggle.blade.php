{{-- Collapse/expand toggle in the sidebar header, next to the brand
     (owner decision 2026-07-04). Drives Filament's Alpine sidebar store.
     On mobile the sidebar is an overlay, so the open state shows a plain X
     (the vendor topbar close button hides underneath the overlay). --}}
<div class="ff-side-toggle-wrp" x-data="{}">
    <button
        type="button"
        class="ff-side-toggle"
        x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
        x-bind:title="$store.sidebar.isOpen ? 'Collapse sidebar' : 'Expand sidebar'"
    >
        <span class="ff-toggle-desktop">
            <svg x-show="$store.sidebar.isOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M12.5 6 8.5 10l4 4M4.5 4v12"></path></svg>
            <svg x-show="! $store.sidebar.isOpen" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M7.5 6l4 4-4 4M15.5 4v12"></path></svg>
        </span>
        <span class="ff-toggle-mobile">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="16" height="16"><path d="M5 5l10 10M15 5L5 15"></path></svg>
        </span>
    </button>
</div>
