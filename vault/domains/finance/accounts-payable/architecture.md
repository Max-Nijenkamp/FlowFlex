---
domain: finance
module: accounts-payable
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

See [[data-model]], [[api]], [[../../../architecture/queue-jobs]].
