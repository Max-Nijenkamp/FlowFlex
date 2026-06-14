<div
    x-data="{
        open: false,
        active: 0,
        items() { return Array.from(document.querySelectorAll('.ff-spotlight-overlay .ff-spotlight-result')) },
        move(delta) {
            const count = this.items().length
            if (! count) return
            this.active = (this.active + delta + count) % count
            this.items()[this.active]?.scrollIntoView({ block: 'nearest' })
        },
        go() { this.items().find((el) => Number(el.dataset.index) === this.active)?.click() },
        show() {
            this.open = true
            this.active = 0
            $nextTick(() => $refs.input?.focus())
        },
    }"
    x-on:keydown.window.prevent.meta.k="show()"
    x-on:keydown.window.prevent.ctrl.k="show()"
    x-on:ff-spotlight-open.window="show()"
    x-on:keydown.window.escape="open = false"
>
    <template x-teleport="body">
        <div
            x-show="open"
            x-cloak
            class="ff-spotlight-overlay"
            x-on:click.self="open = false"
            x-on:keydown.down.prevent="move(1)"
            x-on:keydown.up.prevent="move(-1)"
            x-on:keydown.enter.prevent="go()"
        >
            <div class="ff-spotlight">
                <div class="ff-spotlight-input-wrp">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        x-ref="input"
                        type="text"
                        class="ff-spotlight-input"
                        placeholder="Search this panel — pages, records, actions…"
                        wire:model.live.debounce.250ms="query"
                        x-on:input="active = 0"
                        autocomplete="off"
                        spellcheck="false"
                    />
                    <span class="ff-spotlight-kbd">ESC</span>
                </div>

                <div class="ff-spotlight-results" wire:loading.class="ff-loading">
                    @php($flatIndex = 0)
                    @forelse ($this->results as $group)
                        <p class="ff-spotlight-group">{{ $group['group'] }}</p>

                        @foreach ($group['items'] as $item)
                            <a
                                href="{{ $item['url'] }}"
                                class="ff-spotlight-result"
                                data-index="{{ $flatIndex }}"
                                x-bind:class="{ 'is-active': active === {{ $flatIndex }} }"
                                x-on:mouseenter="active = {{ $flatIndex }}"
                            >
                                <span class="chip"></span>
                                <span>{{ $item['label'] }}</span>
                                @if ($item['sub'] !== '')
                                    <span class="sub">{{ $item['sub'] }}</span>
                                @endif
                            </a>
                            @php($flatIndex++)
                        @endforeach
                    @empty
                        <p class="ff-spotlight-empty">
                            Nothing here matches “{{ $query }}” yet — try a page name, a record, or “new …”.
                        </p>
                    @endforelse
                </div>

                <div class="ff-spotlight-foot">
                    <span>↑↓ navigate · ↵ open</span>
                    <span>⌘K / CTRL+K</span>
                </div>
            </div>
        </div>
    </template>
</div>
