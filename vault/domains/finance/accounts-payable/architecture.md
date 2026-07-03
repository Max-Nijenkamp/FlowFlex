---
domain: finance
module: accounts-payable
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Accounts Payable — Architecture

Interface→Service binding: `ApServiceInterface` → `ApService`, registered in the finance service provider per [[../../../architecture/patterns/interface-service]].

## Service surface

- `createBill(CreateBillData)` — bill amount is defined by the sum of its lines; duplicate `(supplier, bill_number)` is rejected.
- `approveBill(billId)` — runs the 3-way match check (throws `MatchFailedException` on PO/GRN mismatch when procurement is active), then posts the GL liability.
- `createPaymentRun(CreatePaymentRunData)` / `executeRun(runId)` — `executeRun` marks bills paid, posts GL cash entries, and applies early discounts, atomically.
- `aging()` — bucketed open bills.

## State machine

`fin_bills.status` is a spatie/laravel-model-states machine (`BillState`): `draft → approved → scheduled → paid`, with a `voided` branch from `draft`/`approved` *(assumed)*. Transitions are audited. Full transition table and side effects are in the narrative below; see also [[../../../architecture/patterns/states]].

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `approved` | `finance.ap.approve` (above threshold: `finance.ap.approve-large`) *(assumed)* | 3-way match gate when procurement active; GL liability posted |
| `approved` | `scheduled` | added to a payment run | |
| `scheduled` | `paid` | payment run executed | GL cash entry; `paid_at`; early discount applied if within window |
| `draft` / `approved` | `voided` *(assumed)* | `finance.ap.approve` | reversal if already posted |

## GL posting path

Approval posts a balanced liability entry; payment posts the cash entry. Both go through the ledger services rather than direct journal inserts, keeping `LedgerService` the single sanctioned write path (see [[../general-ledger/_module]]). Voiding an already-posted bill triggers a reversal.

## Money handling

All amounts are integer **minor units** (cents) in `bigint` columns, manipulated with `brick/money` — never raw floats. Bill-line amounts must sum to the bill amount (cross-check), and early-payment discounts compute through `Money`. See [[../../../architecture/packages]] (brick/money).

## Event flow

Consumes `GoodsReceived` (from [[../../operations/goods-receipt/_module|operations.goods-receipt]]): drafts a bill and seeds the 3-way match. Queued, `WithCompanyContext`, per the [[../../../architecture/event-bus]] contract. Until Operations/Procurement ship, bills are entered manually and the match gate is bypassed.

## Filament Artifacts

**Nav group:** Payables *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SupplierResource` | #1 CRUD resource | tweaks: custom-header-actions (manage-suppliers) | encrypted IBAN masked to `iban_last4`; full value gated on `finance.ap.view-sensitive` |
| `BillResource` | #1 CRUD resource | tweaks: state-badge-column (`BillState` badge + transition actions), custom-header-actions (approve — own permission + rate limiter), inline-relation-repeater (bill-line grid) | list filters: supplier, status, due date; approved/scheduled bills read-only |
| `ApAgingPage` | #9 custom page | [[../../../architecture/patterns/page-blueprints#Two-Panel Matcher]] — per supplier/bill aging buckets (current/30/60/90+), drill into a bill; realtime none | `/finance/ap/aging`, read-only |
| `PaymentRunPage` | #9 custom page (closest — batch worklist) | [[../../../architecture/patterns/page-blueprints#Two-Panel Matcher]] — select scheduled bills → batch preview with line-sum check → execute (SEPA/CSV export); realtime none | `/finance/ap/payment-runs`; see QUESTIONS — no exact two-stage batch-execute row in ui-strategy |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('finance.ap.view-any') && BillingService::hasModule('finance.ap')`
per [[../../../architecture/filament-patterns]] #1. `ApAgingPage` and `PaymentRunPage` are custom pages and MUST state this
explicitly — Filament does not auto-gate custom pages. No public/portal surface in this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Supplier + bill + line CRUD (draft, form/API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Bill approval (draft → approved; posts GL liability) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, run 3-way match, post — state transition per [[../../../architecture/patterns/states]] + journal posting (money) |
| Schedule (approved → scheduled; add to payment run) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the bill — state transition per [[../../../architecture/patterns/states]] |
| Payment run execute (scheduled → paid; posts GL cash, applies early discount) | Pessimistic | MONEY mutation — `DB::transaction()` + `lockForUpdate()` over the run's bills; atomic all-or-none, line-sum check, posts cash entry |
| Void / reversal (draft/approved → voided; reverses posted entry) | Pessimistic | `DB::transaction()` + `lockForUpdate()` — state transition per [[../../../architecture/patterns/states]] + GL reversal posting |
| AP aging (bucketed open bills) | n-a | read-only aggregation over own tables — no writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

See [[data-model]], [[api]], [[../../../architecture/queue-jobs]].
