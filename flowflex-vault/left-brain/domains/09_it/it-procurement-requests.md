---
type: module
domain: IT & Security
panel: it
cssclasses: domain-it
phase: 4
status: planned
migration_range: 500000–549999
last_updated: 2026-05-09
---

# IT Procurement & Hardware Requests

Employee self-service portal for requesting IT equipment. Structured approval flow, budget visibility, auto-link delivered asset to IT Asset record. Replaces email-to-IT-team, Jira Service Management hardware requests.

**Panel:** `it`  
**Phase:** 4 — needed alongside IT Asset Management

---

## Features

### Hardware Request Catalog
- Pre-defined catalog items: laptop, monitor, keyboard, mouse, headset, phone, desk accessories
- Per-item: description, photo, estimated cost, lead time, approved vendors
- Configure: which items require manager approval, which are self-approve
- "Custom request" option for non-catalog items

### Request Submission
- Employee selects item(s) + quantity + justification + urgency (standard/urgent)
- Attach business case (required for items above cost threshold)
- Suggested alternatives shown (if cheaper option available)
- Auto-assign to IT team queue

### Approval Workflow
- Level 1: Line manager approval (for items above X cost)
- Level 2: Finance approval (for items above Y cost — budget check)
- IT review: confirm item in stock / arrange purchase
- Rejection with reason + resubmission option

### Budget Integration
- Department hardware budget tracking (link to Finance Budgeting module)
- Show remaining budget on request form
- Block submission if budget exhausted (or require CFO override)

### Fulfillment Tracking
- Status: Submitted → Approved → Ordered → Dispatched → Delivered
- Estimated delivery date shown on request
- Tracking number + carrier link when dispatched
- "Mark as received" by employee → auto-creates IT Asset record

### IT Asset Auto-Creation
- On delivery confirmed → IT Asset record created with: asset type, serial (if entered), assigned to, purchase date, purchase cost
- Link request → asset for full history

---

## Data Model

```erDiagram
    it_request_catalog {
        ulid id PK
        ulid company_id FK
        string name
        string category
        decimal estimated_cost
        integer lead_time_days
        boolean requires_manager_approval
        decimal manager_approval_threshold
    }

    it_procurement_requests {
        ulid id PK
        ulid company_id FK
        ulid requested_by FK
        ulid catalog_item_id FK
        integer quantity
        string justification
        string status
        ulid manager_approved_by FK
        ulid finance_approved_by FK
        decimal actual_cost
        string tracking_number
        ulid asset_id FK
        timestamp delivered_at
    }
```

---

## Permissions

```
it.procurement.submit-request
it.procurement.approve-manager
it.procurement.approve-finance
it.procurement.fulfill
it.procurement.view-all
```

---

## Related

- [[MOC_IT]]
- [[entity-employee]]
- [[MOC_Finance]] — budget check integration
