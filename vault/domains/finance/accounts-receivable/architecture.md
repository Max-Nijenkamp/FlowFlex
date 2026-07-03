---
domain: finance
module: accounts-receivable
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Accounts Receivable — Architecture

Interface→Service binding: `ArServiceInterface` → `ArService`, registered in the finance service provider per [[../../../architecture/patterns/interface-service]].

## Service surface

- `aging(?customerId)` returns bucketed open balances computed from `fin_invoices` + `fin_payments`.
- `statement(customerId, from, to)` assembles a per-customer ledger of invoices, payments, and running balance.
- `writeOff(WriteOffData)` voids the remaining balance and posts a GL bad-debt entry; the approver is recorded.
- `allocatePayment(AllocatePaymentData)` records per-invoice payments through `InvoiceService::recordPayment` — AR does not write invoice/payment rows directly, it delegates to the owning module.
- `dso(period)` computes days-sales-outstanding.

## GL posting path

Write-offs are the only AR action that touches the ledger. `writeOff` posts a balanced bad-debt entry through the invoicing/ledger services rather than inserting journal lines itself, keeping `LedgerService` the single sanctioned write path (see [[../general-ledger/_module]]).

## Dunning job

`ProcessDunningCommand` runs daily on the notifications queue. For each overdue invoice it evaluates the active `fin_ar_dunning_rules` in `escalation_level` order and fires the next level once, guarded by `fin_invoices.last_dunning_level` *(assumed column)*. Reminder mail goes out via `DunningMail` (queued mailable). Payment resets the level — see the event flow below.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw floats. Aging totals, DSO, and write-off amounts all flow through `Money`. See [[../../../architecture/packages]] (brick/money) and [[../../../architecture/performance]].

## Event flow

Consumes `InvoicePaid` (from [[../invoicing/_module|finance.invoicing]]): `UpdateARAgingListener` recomputes the account's aging cache and resets its dunning level. Queued, `WithCompanyContext`, per the [[../../../architecture/event-bus]] contract.

## Filament Artifacts

**Nav group:** Receivables *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DunningRuleResource` | #1 CRUD resource | tweaks: — (plain resource; `is_active` toggle, unique `escalation_level` per company) | list ordered by escalation level; `finance.ar.manage-dunning` |
| `ArAgingPage` | #9 custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — aging buckets (current/1–30/31–60/61–90/90+) + DSO / turnover KPIs, drill to invoices; realtime none | `/finance/ar/aging`, read-only |
| `CustomerStatementPage` | #9 custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] — per-customer invoices / payments / running balance over a period; realtime none | `/finance/ar/statement`, read-only |
| `AllocatePaymentAction` | action (modal) hosted on `ArAgingPage` / `CustomerStatementPage` | money mutation — names a `panel-action` limiter; live sum-check `sum(allocations) === amount_cents` | records per-invoice payments via `InvoiceService::recordPayment`; see QUESTIONS (feature note labels it a custom page, absent from Build Manifest) |
| `WriteOffAction` | action (modal) hosted on the AR invoice list | money mutation — names a `panel-action` limiter; amount = invoice open balance | posts a bad-debt GL entry, records approver; see QUESTIONS (feature note labels it a custom page, absent from Build Manifest) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.ar.view-any') && BillingService::hasModule('finance.ar')`
per [[../../../architecture/filament-patterns]] #1. `ArAgingPage` and `CustomerStatementPage` are custom pages and MUST state this
explicitly — Filament does not auto-gate custom pages; the `AllocatePaymentAction` and `WriteOffAction` additionally gate on
`finance.ar.allocate-payment` / `finance.ar.write-off`. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Dunning rule CRUD (form/API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Payment allocation (records per-invoice payments) | Pessimistic | MONEY mutation — `DB::transaction()` + `lockForUpdate()`; delegates writes to `InvoiceService::recordPayment`, validates `sum(allocations) === amount_cents` and per-invoice open-balance caps |
| Write-off (posts bad-debt GL, voids remaining balance) | Pessimistic | MONEY mutation — `DB::transaction()` + `lockForUpdate()`; posts a balanced GL entry via ledger/invoicing services, records `approved_by` |
| Dunning level advance (`ProcessDunningCommand`) / reset (`InvoicePaid` listener) | Pessimistic | `lockForUpdate()` on the invoice row to guard one-send-per-level via `last_dunning_level`; queued single writer |
| Aging / statement / DSO (derived from invoicing reads) | n-a | read-only computation over cached invoice/payment sums — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[data-model]], [[api]], [[../../../architecture/queue-jobs]].
