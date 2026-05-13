---
type: module
domain: Procurement & Spend Management
panel: procurement
module-key: procurement.requisitions
status: planned
color: "#4ADE80"
---

# Purchase Requisitions

> Internal purchase requests — item description, justification, budget check, and multi-level approval workflow.

**Panel:** `procurement`
**Module key:** `procurement.requisitions`

---

## What It Does

Purchase Requisitions is the entry point of the procurement workflow. Any employee can submit a purchase request for goods or services needed for their work. The request captures what is needed, why, the estimated cost, the budget code to charge, and the preferred supplier. Budget availability is checked automatically against the department's budget in the FPA module. The request is then routed through a configurable approval chain — manager, finance controller, or a procurement team member depending on the value threshold.

---

## Features

### Core
- Requisition form: item description, quantity, estimated unit cost, category, preferred supplier, justification
- Budget code selection: allocate the request to a cost centre or project code
- Automated budget check: compare the estimated cost against the available budget for the selected code
- Approval routing: automatic routing based on value thresholds (e.g. under £1,000 to line manager; above to finance)
- Approval notifications: approvers notified by email and in-app; requesters notified of decisions
- Rejection with reason: approver provides a written reason; requester can revise and resubmit

### Advanced
- Multi-item requisitions: include multiple line items on a single requisition
- Preferred supplier lookup: search the supplier catalog and pre-fill supplier details
- Recurring requisitions: clone a past requisition for regular recurring purchases
- Priority flag: urgent requisitions bypass the standard queue with senior approver notification
- Bulk approval: approvers can approve multiple low-value requisitions simultaneously

### AI-Powered
- Duplicate detection: flag when a similar requisition was submitted recently for the same item
- Budget impact warning: alert when a requisition would exhaust more than 80% of the remaining period budget
- Supplier suggestion: recommend approved suppliers from the catalog based on item category

---

## Data Model

```erDiagram
    purchase_requisitions {
        ulid id PK
        ulid company_id FK
        ulid requested_by FK
        ulid approver_id FK
        string title
        string category
        json line_items
        decimal total_estimated_cost
        string currency
        ulid cost_centre_id FK
        string status
        text rejection_reason
        boolean is_urgent
        timestamp approved_at
        timestamps created_at_updated_at
    }

    requisition_approvals {
        ulid id PK
        ulid requisition_id FK
        ulid approver_id FK
        integer approval_level
        string decision
        text notes
        timestamp decided_at
    }

    purchase_requisitions ||--o{ requisition_approvals : "routed through"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `purchase_requisitions` | Requisition records | `id`, `company_id`, `requested_by`, `total_estimated_cost`, `cost_centre_id`, `status`, `is_urgent` |
| `requisition_approvals` | Approval decisions | `id`, `requisition_id`, `approver_id`, `approval_level`, `decision`, `decided_at` |

---

## Permissions

```
procurement.requisitions.submit
procurement.requisitions.view-own
procurement.requisitions.approve
procurement.requisitions.view-all
procurement.requisitions.admin
```

---

## Filament

- **Resource:** `App\Filament\Procurement\Resources\PurchaseRequisitionResource`
- **Pages:** `ListPurchaseRequisitions`, `CreatePurchaseRequisition`, `ViewPurchaseRequisition`
- **Custom pages:** `ApprovalQueuePage`, `MyRequisitionsPage`
- **Widgets:** `PendingApprovalsWidget`, `RequisitionSpendWidget`
- **Nav group:** Requests

---

## Displaces

| Feature | FlowFlex | Coupa | Procurify | SAP Ariba |
|---|---|---|---|---|
| Multi-level approval | Yes | Yes | Yes | Yes |
| Budget check | Yes | Yes | Yes | Yes |
| AI duplicate detection | Yes | No | No | No |
| Native HR approval chain | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[purchase-orders]] — approved requisitions convert to POs
- [[supplier-catalog]] — preferred supplier looked up at requisition
- [[fpa/budgets]] — budget availability checked against FPA budget data
