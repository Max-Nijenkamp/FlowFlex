<div class="ff-board" x-data="{ dragging: null }">
    <div class="ff-board-toolbar">
        <select wire:model.live="ownerFilter" class="ff-board-filter">
            <option value="">All owners</option>
            @foreach ($owners as $owner)
                <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
            @endforeach
        </select>
        <span class="ff-board-hint">Drag a card to move it between stages</span>
    </div>

    <div class="ff-board-columns">
        @foreach ($columns as $column)
            @php
                /** @var \App\Models\Crm\PipelineStage $stage */
                $stage = $column['stage'];
            @endphp
            <div
                class="ff-board-col {{ $stage->is_won ? 'ff-won' : '' }} {{ $stage->is_lost ? 'ff-lost' : '' }}"
                x-on:dragover.prevent
                x-on:drop.prevent="
                    if (dragging) { $wire.moveDeal(dragging, '{{ $stage->id }}'); dragging = null; }
                "
            >
                <div class="ff-board-col-head">
                    <span class="ff-board-col-name">{{ $stage->name }}</span>
                    <span class="ff-board-col-meta">{{ $column['count'] }} · {{ $column['total']->formatToLocale('nl_NL') }}</span>
                </div>

                @if (! $stage->is_won && ! $stage->is_lost)
                    <form class="ff-board-quickadd" wire:submit.prevent="quickAddDeal('{{ $stage->id }}')">
                        <input
                            type="text"
                            placeholder="+ Quick add deal"
                            wire:model="quickAdd.{{ $stage->id }}"
                        />
                    </form>
                @endif

                <div class="ff-board-cards">
                    @forelse ($column['deals'] as $deal)
                        <div
                            class="ff-board-card"
                            draggable="true"
                            x-on:dragstart="dragging = '{{ $deal->id }}'"
                            x-on:dragend="dragging = null"
                            wire:key="deal-{{ $deal->id }}"
                        >
                            <span class="ff-board-card-name">{{ $deal->name }}</span>
                            <span class="ff-board-card-value">{{ \Brick\Money\Money::ofMinor($deal->value_cents, $deal->currency)->formatToLocale('nl_NL') }}</span>
                            <span class="ff-board-card-meta">
                                {{ $deal->account?->name ?? '—' }} · {{ $deal->owner?->full_name }}
                            </span>
                            <span class="ff-board-card-days">{{ $deal->stage_entered_at->diffInDays(now()) >= 1 ? floor($deal->stage_entered_at->diffInDays(now())).'d in stage' : 'new in stage' }}</span>
                        </div>
                    @empty
                        <div class="ff-board-empty">No deals</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
