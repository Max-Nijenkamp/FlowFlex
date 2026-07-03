{{-- Spotlight trigger (design .pn-search). Dispatches ff-spotlight-open; the
     listener arrives with core.spotlight — until then it is inert chrome. --}}
<button
    type="button"
    class="ff-search-trigger"
    onclick="window.dispatchEvent(new CustomEvent('ff-spotlight-open'))"
>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" width="15" height="15">
        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
    </svg>
    <span>Search</span>
    <kbd>⌘K</kbd>
</button>
