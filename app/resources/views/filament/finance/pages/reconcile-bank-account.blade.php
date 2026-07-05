<x-filament-panels::page>
    @php
        $fmt = fn (int $cents): string => \Brick\Money\Money::ofMinor($cents, $record->currency)->formatToLocale('nl_NL');
    @endphp

    <div class="ff-recon">
        <div class="ff-recon-col">
            <h3 class="ff-recon-title">Unmatched statement rows</h3>
            <div class="ff-recon-list">
                @forelse ($open as $transaction)
                    <button
                        type="button"
                        wire:key="txn-{{ $transaction->id }}"
                        wire:click="selectTransaction('{{ $transaction->id }}')"
                        @class(['ff-recon-row', 'ff-selected' => $selectedTransactionId === $transaction->id])
                    >
                        <span class="ff-recon-desc">{{ $transaction->description }}</span>
                        <span class="ff-recon-meta">{{ $transaction->transaction_date->format('d M Y') }}</span>
                        <span @class(['ff-recon-amount', 'ff-neg' => $transaction->amount_cents < 0])>{{ $fmt($transaction->amount_cents) }}</span>
                    </button>
                @empty
                    <p class="ff-recon-empty">Everything is reconciled 🎉</p>
                @endforelse
            </div>

            @if ($matched->isNotEmpty())
                <h3 class="ff-recon-title">Recently matched</h3>
                <div class="ff-recon-list">
                    @foreach ($matched as $transaction)
                        <div class="ff-recon-row ff-done" wire:key="done-{{ $transaction->id }}">
                            <span class="ff-recon-desc">{{ $transaction->description }}</span>
                            <span class="ff-recon-amount">{{ $fmt($transaction->amount_cents) }}</span>
                            <button type="button" class="ff-recon-undo" wire:click="unreconcile('{{ $transaction->id }}')">Unmatch</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="ff-recon-col">
            <h3 class="ff-recon-title">Suggested ledger matches</h3>
            @if ($selected === null)
                <p class="ff-recon-empty">Pick a statement row to see exact-amount matches within ±5 days.</p>
            @elseif ($suggestions->isEmpty())
                <p class="ff-recon-empty">No exact-amount journal lines near {{ $selected->transaction_date->format('d M Y') }}. Post the entry first, then match.</p>
            @else
                <div class="ff-recon-list">
                    @foreach ($suggestions as $line)
                        <div class="ff-recon-row" wire:key="line-{{ $line->id }}">
                            <span class="ff-recon-desc">{{ $line->entry?->reference }} — {{ $line->entry?->description }}</span>
                            <span class="ff-recon-meta">{{ $line->entry?->entry_date->format('d M Y') }}</span>
                            <button
                                type="button"
                                class="ff-recon-match"
                                wire:click="reconcile('{{ $selected->id }}', '{{ $line->id }}')"
                            >Match</button>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
