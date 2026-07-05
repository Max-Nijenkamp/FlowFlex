<x-filament-panels::page>
    @php
        $fmt = fn (int $cents): string => \Brick\Money\Money::ofMinor($cents, 'EUR')->formatToLocale('nl_NL');
    @endphp

    <div class="ff-tb">
        <div class="ff-tb-toolbar">
            <label>From <input type="date" wire:model.live="from" /></label>
            <label>Until <input type="date" wire:model.live="until" /></label>
            <span @class(['ff-tb-check', 'ff-ok' => $this->totals['debit'] === $this->totals['credit']])>
                {{ $this->totals['debit'] === $this->totals['credit'] ? 'Balanced' : 'OUT OF BALANCE' }}
            </span>
        </div>

        <div class="ff-tb-board">
            <table class="ff-tb-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Account</th>
                        <th class="ff-num">Debit</th>
                        <th class="ff-num">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->rows as $row)
                        <tr>
                            <td class="ff-tb-code">{{ $row['account']->code }}</td>
                            <td>{{ $row['account']->name }}</td>
                            <td class="ff-num">{{ $row['debit_cents'] > 0 ? $fmt($row['debit_cents']) : '—' }}</td>
                            <td class="ff-num">{{ $row['credit_cents'] > 0 ? $fmt($row['credit_cents']) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="ff-tb-empty">No postings in this range yet.</td></tr>
                    @endforelse
                </tbody>
                @if ($this->rows->isNotEmpty())
                    <tfoot>
                        <tr>
                            <td></td>
                            <td>Total</td>
                            <td class="ff-num">{{ $fmt($this->totals['debit']) }}</td>
                            <td class="ff-num">{{ $fmt($this->totals['credit']) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</x-filament-panels::page>
