---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.requisitions
status: planned
build-status: planned
priority: p3
depends-on: [procurement.approvals, core.billing, core.rbac, core.notifications]
soft-depends: [finance.budgets, operations.purchase-orders, procurement.catalogue]
fires-events: [RequisitionApproved]
consumes-events: []
patterns: [states, money]
tables: [proc_requisitions, proc_requisition_items, proc_requisition_approvals]
permission-prefix: procurement.requisitions
encrypted-fields: []
last-reviewed: 2026-07-02
color: "#4ADE80"
---

# Purchase Requisitions

Internal purchase requests with an approval workflow before becoming purchase orders. Employees request, managers approve (via the approval matrix), procurement converts to a PO.

Hosted in **/operations** (Procurement nav → Requisitions). See [[../_index|Procurement MOC]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../approvals/_module\|procurement.approvals]] | routing via approval matrix |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, approver notifications |
| Soft | [[../../finance/budgets/_module\|finance.budgets]] | `BudgetService::remaining()` — warn over budget *(assumed: warn, not block)* |
| Soft | [[../../operations/purchase-orders/_module\|operations.purchase-orders]] | conversion target; without it a requisition ends at approved |
| Soft | [[../supplier-catalogue/_module\|procurement.catalogue]] | item picker |

---

## Core Features

- [[features/create-requisition\|Create requisition]] — requester, items, justification, department, budget line.
- [[features/approval-flow\|Approval flow]] — state machine + matrix-driven multi-level approval.
- [[features/budget-check\|Budget check]] — warn against department budget (soft).
- [[features/catalogue-picker\|Catalogue picker]] — pull items from the supplier catalogue.
- [[features/convert-to-po\|Convert to PO]] — approved requisition → Operations PO.

---

## Data Model

Full model + ERD: [[data-model]]. Owns `proc_requisitions`, `proc_requisition_items`, `proc_requisition_approvals`.

## State Machine

`draft → submitted → approved | rejected → converted_to_po`. Detail in [[data-model]] + [[features/approval-flow]].

## DTOs

`CreateRequisitionData`, `ApproveRequisitionData` — [[api]].

## Services & Actions

Interface→Service: `RequisitionServiceInterface` → `RequisitionService` (`submit`, `act`, `convertToPo`). See [[architecture]] + [[api]].

---

## Filament

**Nav group:** Requisitions

| Artifact | UI kind | Feature |
|---|---|---|
| `RequisitionResource` | simple-resource | [[features/create-requisition]] (My requisitions / Approval queue tabs; catalogue picker; convert action) |

**Access contract:** `canAccess() = Auth::user()->can('procurement.requisitions.view-any') && BillingService::hasModule('procurement.requisitions')` — [[../../../architecture/filament-patterns]] #1. See [[security]].

---

## Permissions

`procurement.requisitions.view-any` · `procurement.requisitions.create` · `procurement.requisitions.approve` · `procurement.requisitions.convert`

---

## Cross-Domain Edges

- **Consumes (read):** approval chains from [[../approvals/_module|procurement.approvals]] (`ApprovalMatrix::chainFor`); `finance.budgets` `BudgetService::remaining()` (soft, read-only); catalogue items (soft).
- **Fires:** `RequisitionApproved` (company_id scalar + requisition_id) → optionally consumed by spend/analytics + finance budget-commitment listeners. Convert-to-PO calls `operations.purchase-orders` `PurchaseOrderService::createFromRequisition` (that service writes the PO).
- **Data ownership:** writes **only** its three `proc_requisition*` tables. It **never** writes `ops_purchase_orders` or `finance_*` — the PO is created by Operations' own service; budget commitments by Finance's own listener. See [[../../../security/data-ownership]].

Detail: [[decisions]] · [[unknowns]].

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Chain resolved per matrix thresholds; levels approve in order
- [ ] Requester cannot approve own; delegate may act
- [ ] Reject requires comment; resubmit restarts chain
- [ ] Over-budget warning attached when budgets active
- [ ] Conversion creates linked PO; double conversion rejected

## Build Manifest

```
database/migrations/xxxx_create_proc_requisitions_table.php
database/migrations/xxxx_create_proc_requisition_items_table.php
database/migrations/xxxx_create_proc_requisition_approvals_table.php
app/Models/Procurement/{Requisition,RequisitionItem,RequisitionApproval}.php
app/States/Procurement/Requisition/{RequisitionState,Draft,Submitted,Approved,Rejected,ConvertedToPo}.php
app/Data/Procurement/{CreateRequisitionData,ApproveRequisitionData}.php
app/Contracts/Procurement/RequisitionServiceInterface.php
app/Services/Procurement/RequisitionService.php
app/Providers/Procurement/ProcurementServiceProvider.php
app/Events/Procurement/RequisitionApproved.php
app/Filament/Operations/Resources/RequisitionResource.php
database/factories/Procurement/{RequisitionFactory,RequisitionItemFactory}.php
tests/Feature/Procurement/{RequisitionFlowTest,RequisitionApprovalTest}.php
```

## Related

- [[../../operations/purchase-orders/_module]] · [[../../finance/budgets/_module]] · [[../approvals/_module]] · [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
