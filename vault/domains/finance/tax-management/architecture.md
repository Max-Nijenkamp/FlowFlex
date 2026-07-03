---
domain: finance
module: tax-management
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** Tax *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `TaxRateResource` | #1 CRUD resource | tweaks: state-badge-column (active/reverse-charge flags) | manages rates (`rate_basis_points`, type, jurisdiction) + tax classes (`default_rate_id`); referenced rates soft-delete only |
| `TaxReturnPage` | #9 custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — per-period output/input/net VAT return prep, file action, export; realtime none | `/finance/tax/return` |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.tax.view-any') && BillingService::hasModule('finance.tax')`
per [[../../../architecture/filament-patterns]] #1. `TaxReturnPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Tax rate + class CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]); referenced rates soft-delete only |
| File period (`filePeriod` → snapshot + status `filed`, locks against recomputation) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write — status transition per [[../../../architecture/patterns/states]]; period close snapshots `output/input/net` cents |
| Period summary (`periodSummary`) | n-a | read-only computation over invoicing/AP/expenses tax data — no writes |
| VAT number validation (`ValidateVatNumberAction` → VIES) | n-a | read-only external `Http` call, failure-tolerant — writes no tax tables |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[data-model]], [[api]].
