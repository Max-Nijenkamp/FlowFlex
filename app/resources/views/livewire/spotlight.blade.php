{{-- Spotlight ⌘K palette. Plain ff-* classes only — Livewire views are not
     scanned by the panel theme build (filament-patterns item 12). Active-row
     tracking uses server-rendered stable data-index (item 14 Alpine gotcha). --}}
<div
    x-data="{
        open: false,
        active: 0,
        count: 0,
        openPalette() {
            this.open = true
            this.active = 0
            this.$nextTick(() => this.$refs.input.focus())
        },
        close() {
            this.open = false
            $wire.set('query', '', false)
        },
        rows() {
            return Array.from(this.$refs.list?.querySelectorAll('[data-index]') ?? [])
        },
        move(delta) {
            const rows = this.rows()
            this.count = rows.length
            if (! rows.length) return
            this.active = (this.active + delta + rows.length) % rows.length
            rows[this.active]?.scrollIntoView({ block: 'nearest' })
        },
        go() {
            const row = this.rows()[this.active]
            if (row?.href) window.location.assign(row.href)
        },
    }"
    x-on:ff-spotlight-open.window="openPalette()"
    x-on:keydown.window.meta.k.prevent="openPalette()"
    x-on:keydown.window.ctrl.k.prevent="openPalette()"
    x-on:keydown.escape.window="close()"
>
    <div
        class="ff-spotlight-overlay"
        x-show="open"
        x-cloak
        x-transition.opacity.duration.150ms
        x-on:click.self="close()"
    >
        <div class="ff-spotlight-panel" x-on:click.stop>
            <div class="ff-spotlight-input-row">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><circle cx="7" cy="7" r="4.5"></circle><path d="M10.5 10.5L14 14"></path></svg>
                <input
                    x-ref="input"
                    type="text"
                    placeholder="Search pages, records, actions…"
                    wire:model.live.debounce.250ms="query"
                    x-on:keydown.down.prevent="move(1)"
                    x-on:keydown.up.prevent="move(-1)"
                    x-on:keydown.enter.prevent="go()"
                    x-on:input="active = 0"
                />
                <kbd>esc</kbd>
            </div>

            <div class="ff-spotlight-list" x-ref="list" wire:key="spotlight-results">
                @php($index = 0)
                @forelse (collect($this->results)->groupBy('group') as $group => $items)
                    <div class="ff-spotlight-group">{{ $group }}</div>
                    @foreach ($items as $item)
                        <a
                            href="{{ $item['url'] }}"
                            data-index="{{ $index }}"
                            class="ff-spotlight-row"
                            x-bind:class="{ 'ff-active': active === {{ $index }} }"
                            x-on:mouseenter="active = {{ $index }}"
                        >
                            @switch($item['icon'])
                                @case('plus')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M10 4.5v11M4.5 10h11"></path></svg>
                                    @break
                                @case('record')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M5 3h7l3 3v11H5z"></path><path d="M12 3v3h3"></path></svg>
                                    @break
                                @case('list')
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><path d="M6.5 5.5H17M6.5 10H17M6.5 14.5H17M3 5.5h.01M3 10h.01M3 14.5h.01"></path></svg>
                                    @break
                                @default
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" width="15" height="15"><rect x="3" y="3" width="14" height="14" rx="2"></rect><path d="M3 7.5h14"></path></svg>
                            @endswitch
                            <span>{{ $item['label'] }}</span>
                        </a>
                        @php($index++)
                    @endforeach
                @empty
                    <div class="ff-spotlight-empty">Nothing found for “{{ $query }}”</div>
                @endforelse
            </div>

            <div class="ff-spotlight-foot">
                <span><kbd>↑</kbd><kbd>↓</kbd> navigate</span>
                <span><kbd>↵</kbd> open</span>
                <span><kbd>esc</kbd> close</span>
            </div>
        </div>
    </div>
</div>
