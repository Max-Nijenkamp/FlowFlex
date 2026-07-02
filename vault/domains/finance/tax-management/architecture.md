---
domain: finance
module: tax-management
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tax Management — Architecture

`TaxService` is the period/reporting service; `TaxCalculator` is the line-math helper. Both are intended to live in the finance domain and be called directly by consuming modules (same-domain rule — no events).

## Tax math

- `TaxCalculator::forLine(int $amountCents, TaxRate $rate): Money` is the single tax-math entry point for all consuming modules (invoicing, AP, expenses). Rounding is line-level and consistent with invoicing.
- Rates are stored as integer **basis points** (`rate_basis_points`, e.g. `2100` = 21%) — no float math. Tax amount = `amountCents × rate_basis_points / 10000`, computed with `brick/money`.
- Reverse-charge rates yield zero tax and carry a flag onto the invoice/bill line plus a ledger note.

## Period summary & filing

- `TaxService::periodSummary(string $period): TaxReturnData` sums invoice output tax + bill/expense input tax for the period.
- `TaxService::filePeriod(string $period): void` snapshots the period and sets status `filed` (locked against rate-affecting recomputation).

## VAT number validation

- `ValidateVatNumberAction::run(string $vatNumber): bool` calls VIES over `Http` (mocked in tests). Network failure is treated as "unverified" and never blocks a save *(assumed)*.

## Money handling

All monetary amounts are integer **minor units** (cents) handled with `brick/money` — never raw float math. See [[../../../architecture/packages]] (brick/money) and [[../../../architecture/performance]].

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[data-model]], [[api]].
