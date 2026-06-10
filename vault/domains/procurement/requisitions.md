---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.requisitions
status: planned
priority: p3
depends-on: [procurement.approvals, core.billing, core.rbac, core.notifications]
soft-depends: [finance.budgets, operations.purchase-orders, procurement.catalogue]
fires-events: []
consumes-events: []
patterns: [states, money]
tables: [proc_requisitions, proc_requisition_items, proc_requisition_approvals]
permission-prefix: procurement.requisitions
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Purchase Requisitions

Internal purchase requests with approval workflow before becoming purchase orders. Employees request, managers approve (via approval matrix), procurement converts to PO.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/procurement/approvals\|procurement.approvals]] | routing via approval matrix |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, approver notifications |
| Soft | [[domains/finance/budgets\|finance.budgets]] | `BudgetService::remaining()` check — warn over budget *(assumed: warn, not block)* |
| Soft | [[domains/operations/purchase-orders\|operations.purchase-orders]] | conversion target; without it requisition ends at approved |
| Soft | [[domains/procurement/supplier-catalogue\|procurement.catalogue]] | item picker |

---

## Core Features

- Requisition: requester, items/description, estimated cost, justification, department, budget line
- Status machine: `draft → submitted → approved | rejected → converted_to_po`
- Multi-level approval based on amount thresholds (matrix in procurement.approvals)
- Budget check: validate against department budget (warn over)
- Convert approved requisition to a purchase order (Operations)
- Requisition templates for recurring requests *(assumed: duplicate action v1)*
- Rejection with reason; resubmit allowed

---

## Data Model

### proc_requisitions

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| requisition_number | string | unique per company |
| requester_id | ulid FK users | |
| department_id | ulid nullable | HR dept when active |
| description / justification | text | justification required |
| estimated_cost_cents | bigint | Σ items |
| currency | string(3) | |
| status | string default `draft` | state machine |
| current_level | int default 0 | approval chain pointer |
| budget_line_id | ulid nullable | finance link |
| po_id | ulid nullable | conversion link |
| deleted_at | timestamp nullable | |

### proc_requisition_items — id, requisition_id FK, company_id, catalogue_item_id nullable, description, quantity decimal (> 0), estimated_unit_cost_cents
### proc_requisition_approvals — id, requisition_id FK, company_id, level, approver_id, action (approved/rejected), comment nullable (required on reject), acted_at

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `draft` | `submitted` | requester | chain resolved from matrix; level-1 approver notified |
| `submitted` | `approved` | final level approves | requester notified |
| `submitted` | `rejected` | any level rejects | reason to requester; resubmit creates new chain |
| `approved` | `converted_to_po` | `procurement.requisitions.convert` | PO created via `PurchaseOrderService::createFromRequisition` |

Approver ≠ requester. Audited.

---

## DTOs

### CreateRequisitionData — description, justification (required, max:2000), department_id?, items[{description or catalogue_item_id, quantity > 0, estimated_unit_cost_cents}] min:1, budget_line_id?
### ApproveRequisitionData — requisition_id, action (in:approved,rejected), comment (required_if rejected)

## Services & Actions

Interface→Service: `RequisitionServiceInterface` → `RequisitionService`.

- `submit(...)` — resolves chain via `ApprovalMatrix::chainFor('requisition', amount, category)`; budget warning attached
- `act(ApproveRequisitionData)` — current-level approver (or delegate) only; advances/completes/rejects
- `convertToPo(string $requisitionId): PoData`

---

## Filament

**Nav group:** Requisitions

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `RequisitionResource` | #1 CRUD resource | My requisitions / Approval queue tabs; catalogue picker; convert action |

---

## Permissions

`procurement.requisitions.view-any` · `procurement.requisitions.create` · `procurement.requisitions.approve` · `procurement.requisitions.convert`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Chain resolved per matrix thresholds; levels approve in order
- [ ] Requester cannot approve own; delegate may act
- [ ] Reject requires comment; resubmit restarts chain
- [ ] Over-budget warning attached when budgets active
- [ ] Conversion creates linked PO; double conversion rejected

---

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
app/Filament/Operations/Resources/RequisitionResource.php (Procurement nav group)
database/factories/Procurement/{RequisitionFactory,RequisitionItemFactory}.php
tests/Feature/Procurement/{RequisitionFlowTest,RequisitionApprovalTest}.php
```

---

## Related

- [[domains/operations/purchase-orders]]
- [[domains/finance/budgets]]
- [[domains/procurement/approvals]]
