<div class="ff-board" x-data="{ dragging: null, over: null }">
    <div class="ff-board-toolbar">
        @if ($pipelines->count() > 1)
            <div class="ff-board-pipes">
                @foreach ($pipelines as $pipe)
                    <button
                        type="button"
                        wire:key="pipe-{{ $pipe->id }}"
                        wire:click="selectPipeline('{{ $pipe->id }}')"
                        @class(['ff-board-pipe', 'ff-on' => $pipeline?->id === $pipe->id])
                    >{{ $pipe->name }}</button>
                @endforeach
            </div>
        @else
            <span class="ff-board-pipe ff-on ff-solo">{{ $pipeline?->name }}</span>
        @endif

        <select wire:model.live="ownerFilter" class="ff-board-filter">
            <option value="">All owners</option>
            @foreach ($owners as $owner)
                <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
            @endforeach
        </select>
        <span class="ff-board-hint">Drag a card between stages</span>
    </div>

    <div class="ff-board-columns">
        @foreach ($columns as $column)
            @php
                /** @var \App\Models\Crm\PipelineStage $stage */
                $stage = $column['stage'];
            @endphp
            <div
                wire:key="col-{{ $stage->id }}"
                @class(['ff-board-col', 'ff-won' => $stage->is_won, 'ff-lost' => $stage->is_lost])
                x-bind:class="{ 'ff-dragover': dragging && over === '{{ $stage->id }}' }"
                x-on:dragover.prevent="over = '{{ $stage->id }}'"
                x-on:dragleave.self="over = null"
                x-on:drop.prevent="
                    if (dragging) { $wire.moveDeal(dragging, '{{ $stage->id }}'); }
                    dragging = null; over = null;
                "
            >
                <div class="ff-board-col-head">
                    <span class="ff-board-col-name">{{ $stage->name }}</span>
                    <span class="ff-board-col-count">{{ $column['count'] }}</span>
                    <span class="ff-board-col-total">{{ $column['total']->formatToLocale('nl_NL') }}</span>
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
                            x-bind:class="{ 'ff-dragging': dragging === '{{ $deal->id }}' }"
                            x-on:dragstart="dragging = '{{ $deal->id }}'"
                            x-on:dragend="dragging = null; over = null"
                            wire:key="deal-{{ $deal->id }}"
                        >
                            <span class="ff-board-card-grip">⋮⋮</span>
                            <span class="ff-board-card-name">{{ $deal->name }}</span>
                            <span class="ff-board-card-value">{{ \Brick\Money\Money::ofMinor($deal->value_cents, $deal->currency)->formatToLocale('nl_NL') }}</span>
                            <span class="ff-board-card-meta">
                                @if ($deal->account?->name)
                                    <span class="ff-board-card-chip">{{ $deal->account->name }}</span>
                                @endif
                                <span class="ff-board-card-days">{{ $deal->stage_entered_at->diffInDays(now()) >= 1 ? floor($deal->stage_entered_at->diffInDays(now())).'d here' : 'new' }}</span>
                            </span>
                            <span class="ff-board-card-owner" title="{{ $deal->owner?->full_name }}">
                                {{ mb_strtoupper(mb_substr($deal->owner?->first_name ?? '?', 0, 1).mb_substr($deal->owner?->last_name ?? '', 0, 1)) }}
                            </span>
                        </div>
                    @empty
                        <div class="ff-board-empty">Drop deals here</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
