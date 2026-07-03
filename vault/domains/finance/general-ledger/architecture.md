---
domain: finance
module: general-ledger
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# General Ledger — Architecture

Interface→Service binding: `LedgerServiceInterface` → `LedgerService`, registered in `FinanceServiceProvider`. This service is **the only write path to the ledger**.

## Write path & immutability

- All posting goes through `LedgerService::post`, wrapped in `DB::transaction`. It validates that debits equal credits and that the entry date falls in an open fiscal period before committing.
- Posted entries are never updated or deleted. Corrections are made via `reverse`, which writes a mirrored (swapped debit/credit) entry and leaves the original untouched.
- Invoice/payment/expense postings are direct service calls **within** the finance domain (same-domain rule, no events). Only cross-domain triggers (payroll) arrive as events.

## Money handling

All amounts are integer **minor units** (cents) stored in `bigint` columns and manipulated with `brick/money` — never raw float math. Each journal line carries exactly one non-zero of `debit_cents` / `credit_cents`. See [[../../../architecture/packages]] (brick/money) and [[../../../architecture/performance]].

## Period locking

`fin_fiscal_periods` gates postings: a `closed` period rejects new entries (`ClosedPeriodException`). Closing/reopening is an owner-level, audited operation.

## Filament Artifacts

**Nav group:** Ledger

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ChartOfAccountsResource` | #1 CRUD resource | tweaks: custom-header-actions (seed default CoA) | hierarchical accounts (parent select); account with posted lines cannot be deleted |
| `JournalEntryResource` | #1 CRUD resource | tweaks: read-only-flow-owned (all writes via `LedgerService::post` — no edit/delete), custom-header-actions (post manual entry / reverse), state-badge-column (posted / reversed *(assumed)*) | manual posting gated `finance.ledger.post-manual`; reversal writes a mirror entry |
| `FiscalPeriodResource` | #1 CRUD resource | tweaks: state-badge-column (open / closed), custom-header-actions (close / reopen) | close/reopen owner-level, audited |
| `TrialBalancePage` | #9 report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — date-range selector + per-account debit/credit grid, drill-down to journal lines; realtime none | `/finance/ledger/trial-balance` |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.ledger.view-any') && BillingService::hasModule('finance.ledger')`
per [[../../../architecture/filament-patterns]] #1. `TrialBalancePage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Chart-of-accounts CRUD (form/API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Manual journal posting (`LedgerService::post`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` — money mutation; validates debits = credits and open period before the append-only insert |
| Payroll auto-posting (`PostPayrollJournalEntryListener`) | Pessimistic | same `post` path — `DB::transaction()` + `lockForUpdate()`; retries on `ClosedPeriodException` per event-bus contract |
| Reversal (`reverse` — mirror entry) | Pessimistic | `DB::transaction()` + `lockForUpdate()`; append-only swapped debit/credit insert, original untouched |
| Close / reopen period (`closePeriod` / `reopenPeriod`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` status transition per [[../../../architecture/patterns/states]] |
| Posted entries (read / immutable) · trial balance | n-a | append-only — posted entries are never updated or deleted; reports are read-only derived reads |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[../../../architecture/patterns/interface-service]], [[../../../architecture/event-bus]], [[data-model]], [[api]].
