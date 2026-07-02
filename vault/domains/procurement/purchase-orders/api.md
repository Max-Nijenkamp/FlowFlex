---
domain: procurement
module: purchase-orders
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Procurement PO Layer — DTOs, Service API & Events

## DTOs

### AddQuoteData
`po_id` (must be draft), `supplier_id` (not blacklisted), `quote_amount_cents` (min:0), `quote_reference?`.

### SelectQuoteData
`sourcing_id` — sets the PO supplier + reprices *(assumed: supplier swap allowed in draft only)*.

## Service API

| Method | Signature | Notes |
|---|---|---|
| `SourcingService::addQuote` | `addQuote(AddQuoteData): PoSourcingData` | adds a quote row |
| `SourcingService::selectQuote` | `selectQuote(SelectQuoteData): void` | one selected per PO; updates PO supplier via Operations (draft only) |
| `ProcurementPoApproval::submit` | `submit(string $poId): void` | resolves `chainFor('po', total, category)` |
| `ProcurementPoApproval::act` | `act(...): void` | on final approval sets `procurement_approved_at`; fires `PurchaseApproved` |
| `CommitmentReport::for` | `for(Period): array{committed: Money, actual: Money}` | committed excludes received |

## Send gate

- Hook on `PurchaseOrderService::send` (Operations): when procurement active + PO procurement-linked, send blocked unless `procurement_approved_at` is set.

## Events fired

| Event | Payload | Consumed by |
|---|---|---|
| `PurchaseApproved` | `company_id` (scalar), `po_id`, `total_cents`, `supplier_id` | finance (AP expected-bill / commitment listener), operations (fulfilment) |

Scalars/IDs only, never models — [[../../../architecture/event-bus]].

## Related

- [[_module]] · [[data-model]] · [[architecture]] · [[../approvals/api]]
