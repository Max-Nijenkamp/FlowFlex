---
domain: procurement
module: requisitions
type: data-model
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Requisitions — Data Model

Owns `proc_requisitions`, `proc_requisition_items`, `proc_requisition_approvals`.

## ERD

```mermaid
erDiagram
    proc_requisitions ||--o{ proc_requisition_items : has
    proc_requisitions ||--o{ proc_requisition_approvals : records
    proc_requisitions {
        ulid id PK
        ulid company_id FK
        string requisition_number "unique per company"
        ulid requester_id FK
        ulid department_id "nullable"
        text description
        text justification "required"
        bigint estimated_cost_cents "sum of items"
        string currency
        string status "state machine"
        int current_level "chain pointer"
        ulid budget_line_id "nullable"
        ulid po_id "nullable - conversion link"
        timestamp deleted_at
    }
    proc_requisition_items {
        ulid id PK
        ulid requisition_id FK
        ulid company_id FK
        ulid catalogue_item_id "nullable"
        text description
        decimal quantity "&gt; 0"
        bigint estimated_unit_cost_cents
    }
    proc_requisition_approvals {
        ulid id PK
        ulid requisition_id FK
        ulid company_id FK
        int level
        ulid approver_id
        string action "approved|rejected"
        text comment "required on reject"
        timestamp acted_at
    }
```

## State machine

| State | → | Trigger | Side effects |
|---|---|---|---|
| `draft` | `submitted` | requester | chain resolved from matrix; level-1 approver notified |
| `submitted` | `approved` | final level approves | requester notified; fires `RequisitionApproved` |
| `submitted` | `rejected` | any level rejects | reason to requester; resubmit creates new chain |
| `approved` | `converted_to_po` | `procurement.requisitions.convert` | PO created via `PurchaseOrderService::createFromRequisition`; `po_id` set |

Approver ≠ requester. All transitions audited.

## proc_requisition_items
id, requisition_id FK, company_id, catalogue_item_id nullable, description, quantity decimal (> 0), estimated_unit_cost_cents.

## proc_requisition_approvals
id, requisition_id FK, company_id, level, approver_id, action (approved/rejected), comment nullable (required on reject), acted_at. This is the **only** place approval actions for requisitions are written (not in the approvals module).

## Related

- [[_module]] · [[architecture]] · [[api]] · [[../../../security/data-ownership]]
