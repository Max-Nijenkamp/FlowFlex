{{-- Journal entry detail modal (finance.ledger) — audit-modal styling. --}}
@php
    /** @var \App\Models\Finance\JournalEntry $record */
    $lines = $record->lines()->with('account')->get();
    $fmt = fn (int $cents): string => \Brick\Money\Money::ofMinor($cents, 'EUR')->formatToLocale('nl_NL');
@endphp

<div class="ff-audit">
    <div class="ff-audit-top">
        <span class="ff-audit-event">{{ $record->reference }}</span>
        <span class="ff-audit-when">{{ $record->entry_date->format('d M Y') }}</span>
    </div>

    <div class="ff-audit-meta">
        <div class="ff-audit-cell">
            <span class="ff-audit-label">Description</span>
            <span class="ff-audit-value">{{ $record->description }}</span>
        </div>
        <div class="ff-audit-cell">
            <span class="ff-audit-label">Source</span>
            <span class="ff-audit-value">{{ $record->source_type ?? 'manual' }}</span>
        </div>
        <div class="ff-audit-cell">
            <span class="ff-audit-label">Status</span>
            <span class="ff-audit-value">{{ ucfirst($record->status) }}</span>
        </div>
    </div>

    <div class="ff-audit-block">
        <span class="ff-audit-block-title">Lines</span>
        <div class="ff-audit-rows">
            @foreach ($lines as $line)
                <div class="ff-audit-row">
                    <span class="ff-audit-key">{{ $line->account?->code }} · {{ $line->account?->name }}</span>
                    <span class="ff-audit-val">
                        @if ($line->debit_cents > 0)
                            <strong>{{ $fmt($line->debit_cents) }} D</strong>
                        @else
                            {{ $fmt($line->credit_cents) }} C
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</div>
