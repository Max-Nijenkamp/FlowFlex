---
domain: finance
module: accounts-payable
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Accounts Payable — DTOs, Services & Events

## DTOs

### CreateBillData
| Field | Type | Validation |
|---|---|---|
| supplier_id | string | required |
| bill_number | string | required; unique `(supplier, bill_number)` |
| bill_date / due_date | date | due ≥ bill |
| lines | array<{description, account_id, amount_cents}> | min:1 |
| attachment | file | pdf, optional |
| po_id | string | nullable |

Cross-field: sum(lines) defines the bill amount; a duplicate `(supplier, bill_number)` is rejected — "This supplier bill number already exists."

### CreatePaymentRunData
| Field | Type | Validation |
|---|---|---|
| run_date | date | required |
| bill_ids | array | each `approved`, same currency *(assumed)* |

### Output DTOs
`BillData`, `PaymentRunData`, `ApAgingData`.

DTOs use `spatie/laravel-data` per [[../../../architecture/patterns/dto-pattern]].

## Services & Actions

Interface→Service: `ApServiceInterface` → `ApService`.

- `createBill(CreateBillData $data): BillData`.
- `approveBill(string $billId): BillData` — 3-way match check (`MatchFailedException` on PO/GRN mismatch when procurement active); posts GL liability.
- `createPaymentRun(CreatePaymentRunData $data): PaymentRunData`.
- `executeRun(string $runId): PaymentRunData` — marks bills paid, posts GL cash entries, applies early discounts.
- `aging(): ApAgingData`.

## Events

### Consumes: `GoodsReceived` (from operations.goods-receipt)
Drafts a bill and seeds the 3-way match. Queued, `WithCompanyContext`, per the [[../../../architecture/event-bus]] contract. Manual bills are used until Operations/Procurement build.

AP fires no events.

See [[security]], [[../general-ledger/_module]], [[../../operations/purchase-orders/_module]].
