---
domain: procurement
module: purchase-orders
feature: po-approval
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# PO Approval (Final Sign-off)

A separate approval from the requisition: final sign-off on the priced, sourced PO before it can be sent to the supplier.

## Behaviour

- `ProcurementPoApproval::submit(poId)` resolves `chainFor('po', total, category)`.
- Levels approve/reject (comment on reject); approver ≠ requester; current-level/delegate only.
- Final approval → sets `procurement_approved_at`, fires `PurchaseApproved`.
- **Send gate**: `PurchaseOrderService::send` is blocked until `procurement_approved_at` is set (when module active). Inactive module → no gate.

## UI

- **Kind**: custom-page (acts surface in the shared [[../../approvals/features/pending-approvals-queue|Pending Approvals queue]]) + actions on `ProcurementPoResource`.
- **Page**: approval timeline on the PO detail; acts from the pending queue.
- **Layout**: level/approver/action/comment timeline; approve/reject for current-level user; "sendable" badge once approved.
- **Key interactions**: approve → advance (optimistic); reject → comment required; on final approve the PO becomes sendable.
- **States**: empty (not yet submitted) · loading (timeline skeleton) · error (toast) · selected (current level highlighted).
- **Gating**: `procurement.purchase-orders.approve` + current-level/delegate check.

## Data

- Owns / writes: `procurement_approved_at` on `ops_purchase_orders` (own column) — see [[../decisions]] caveat.
- Reads: `chainFor`/`resolveApprover` from [[../../approvals/_module|approvals]]; PO total from Operations.
- Cross-domain writes: none beyond the owned column; effects flow via `PurchaseApproved` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: approval matrix (approvals); PO (Operations).
- Feeds: `PurchaseApproved` → finance AP (expected bill/commitment) + operations (fulfilment); unblocks Operations' send.

## Unknowns

- Column vs separate approval table — **UNVERIFIED**, ADR pending ([[../decisions]]).

## Related

- [[../_module|Procurement PO Layer]] · [[../../approvals/features/approval-matrix]] · [[spend-commitment]]
