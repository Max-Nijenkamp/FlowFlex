---
domain: procurement
module: requisitions
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Requisitions — DTOs, Service API & Events

## DTOs

### CreateRequisitionData
`description`, `justification` (required, max:2000), `department_id?`, `items[]` (min:1, each `{description or catalogue_item_id, quantity > 0, estimated_unit_cost_cents}`), `budget_line_id?`.

### ApproveRequisitionData
`requisition_id`, `action` (in:approved,rejected), `comment` (required_if rejected).

## Service API (`RequisitionServiceInterface`)

| Method | Signature | Notes |
|---|---|---|
| `submit` | `submit(CreateRequisitionData): RequisitionData` | resolves chain via `ApprovalMatrix::chainFor('requisition', amount, category)`; budget warning attached; snapshots chain |
| `act` | `act(ApproveRequisitionData): RequisitionData` | current-level approver (or delegate) only; advances/completes/rejects; fires `RequisitionApproved` on final approve |
| `convertToPo` | `convertToPo(string $requisitionId): PoData` | approved only; delegates PO creation to Operations; sets `po_id`; blocks double conversion |

## Events fired

| Event | Payload | Consumed by |
|---|---|---|
| `RequisitionApproved` | `company_id` (scalar), `requisition_id`, `total_cents`, `department_id?` | spend analytics, finance budget-commitment (their own listeners) |

Events carry scalars/IDs, never models — [[../../../architecture/event-bus]].

## Related

- [[_module]] · [[data-model]] · [[architecture]] · [[../approvals/api]]
