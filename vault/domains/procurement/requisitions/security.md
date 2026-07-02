---
domain: procurement
module: requisitions
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Requisitions — Security

## Access contract

`canAccess() = Auth::user()->can('procurement.requisitions.view-any') && BillingService::hasModule('procurement.requisitions')` — [[../../../architecture/filament-patterns]] #1.

## Permissions

| Permission | Grants |
|---|---|
| `procurement.requisitions.view-any` | list/see requisitions (own always visible) |
| `procurement.requisitions.create` | raise a requisition |
| `procurement.requisitions.approve` | act on an approval (still bounded by current-level/delegate rule) |
| `procurement.requisitions.convert` | convert approved requisition → PO |

## Authorization rules

- **Approver ≠ requester** enforced at the service layer, not just UI.
- `act` verifies the caller is the current-level approver (or a valid delegate) — holding the permission is necessary but not sufficient.
- Own requisitions always visible to their requester regardless of `view-any`.

## Data ownership

Writes **only** `proc_requisitions`, `proc_requisition_items`, `proc_requisition_approvals`. PO creation on convert is delegated to Operations' service (it writes `ops_purchase_orders`); budget effects flow via the `RequisitionApproved` event to Finance's own listener. No cross-domain writes — [[../../../security/data-ownership]].

## Tenancy & audit

- All rows carry `company_id` under CompanyScope — [[../../../security/tenancy-isolation]].
- Every state transition + approval action audited (activity log).

## Related

- [[_module]] · [[../../../security/data-ownership]] · [[api]]
