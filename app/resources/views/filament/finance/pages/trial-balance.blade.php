<x-filament-panels::page>
    @php
        $fmt = fn (int $cents): string => \Brick\Money\Money::ofMinor($cents, 'EUR')->formatToLocale('nl_NL');
        $presets = [
            'this-month' => 'This month',
            'last-month' => 'Last month',
            'this-quarter' => 'This quarter',
            'last-quarter' => 'Last quarter',
            'this-year' => 'This year',
            'last-year' => 'Last year',
        ];
        $balanced = $this->totals['debit'] === $this->totals['credit'];
    @endphp

    <div class="ff-tb">
        <div class="ff-tb-toolbar">
            <div class="ff-chips">
                @foreach ($presets as $key => $label)
                    <button
                        type="button"
                        wire:key="preset-{{ $key }}"
                        wire:click="applyPreset('{{ $key }}')"
                        @class(['ff-chip', 'ff-on' => $preset === $key])
                    >{{ $label }}</button>
                @endforeach
            </div>
            <div class="ff-tb-range">
                <label>From <input type="date" wire:model.live="from" /></label>
                <label>Until <input type="date" wire:model.live="until" /></label>
            </div>
            <span @class(['ff-tb-check', 'ff-ok' => $balanced])>
                {{ $balanced ? '✓ Balanced' : '⚠ OUT OF BALANCE' }}
            </span>
        </div>

        <div class="ff-tb-stats">
            <div class="ff-tb-stat">
                <span class="ff-tb-stat-label">Total debits</span>
                <span class="ff-tb-stat-value">{{ $fmt($this->totals['debit']) }}</span>
            </div>
            <div class="ff-tb-stat">
                <span class="ff-tb-stat-label">Total credits</span>
                <span class="ff-tb-stat-value">{{ $fmt($this->totals['credit']) }}</span>
            </div>
            <div class="ff-tb-stat">
                <span class="ff-tb-stat-label">Accounts touched</span>
                <span class="ff-tb-stat-value">{{ $this->rows->count() }}</span>
            </div>
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
                @forelse ($this->groups as $type => $group)
                    <tbody wire:key="group-{{ $type }}">
                        <tr class="ff-tb-type">
                            <td colspan="4">{{ str($type)->plural()->headline() }}</td>
                        </tr>
                        @foreach ($group['rows'] as $row)
                            <tr>
                                <td class="ff-tb-code">{{ $row['account']->code }}</td>
                                <td>{{ $row['account']->name }}</td>
                                <td class="ff-num">{{ $row['debit_cents'] > 0 ? $fmt($row['debit_cents']) : '—' }}</td>
                                <td class="ff-num">{{ $row['credit_cents'] > 0 ? $fmt($row['credit_cents']) : '—' }}</td>
                            </tr>
                        @endforeach
                        <tr class="ff-tb-subtotal">
                            <td></td>
                            <td>{{ str($type)->plural()->headline() }} subtotal</td>
                            <td class="ff-num">{{ $fmt($group['debit']) }}</td>
                            <td class="ff-num">{{ $fmt($group['credit']) }}</td>
                        </tr>
                    </tbody>
                @empty
                    <tbody>
                        <tr><td colspan="4" class="ff-tb-empty">No postings in this range yet — send an invoice or approve an expense and the ledger fills itself.</td></tr>
                    </tbody>
                @endforelse
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
