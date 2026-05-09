---
type: module
domain: Procurement & Spend Management
panel: procurement
phase: 3
status: planned
cssclasses: domain-procurement
migration_range: 980000–980499
last_updated: 2026-05-09
---

# Purchase Requisitions

Staff submit purchase requests. Multi-level approval based on amount, category, and department budget. Approved requisitions convert to Purchase Orders.

---

## Requisition Workflow

```
Staff submits PR → Auto-route to approver(s) → Approved → Convert to PO
                                              → Rejected → Notify requester with reason
```

### Approval Routing Rules
| Condition | Route to |
|---|---|
| Amount < €500 | Line manager |
| Amount €500–2,500 | Line manager + department head |
| Amount > €2,500 | Line manager + dept head + CFO/Finance |
| IT Category | IT manager (regardless of amount) |
| Marketing spend | Marketing manager + CFO |
| Capex | Finance director |

Rules fully configurable. Multiple approvers can be sequential (all must approve) or parallel (any one approves).

---

## Requisition Form

Requester fills in:
- What: item/service description
- Why: business justification
- Supplier: preferred supplier or "not sure" → procurement team finds
- Estimated amount + currency
- Budget code / cost centre
- Required by date
- Supporting docs: quotes, spec sheets (upload)

---

## Budget Check

On submission: system checks department budget remaining (from FP&A module if connected):
- Under budget: green indicator
- Near limit (>80%): amber warning
- Over budget: red block → requires CFO approval override

---

## Data Model

### `proc_requisitions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| requested_by | ulid | FK `employees` |
| department | varchar(100) | |
| cost_centre_id | ulid | nullable FK |
| description | varchar(500) | |
| justification | text | |
| estimated_amount | decimal(14,2) | |
| currency | char(3) | |
| preferred_supplier | varchar(200) | nullable |
| required_by | date | |
| status | enum | draft/pending/approved/rejected/converted_to_po |
| purchase_order_id | ulid | nullable FK |

---

## Migration

```
980000_create_proc_requisitions_table
980001_create_proc_requisition_approvals_table
980002_create_proc_requisition_attachments_table
```

---

## Related

- [[MOC_Procurement]]
- [[purchase-orders]]
- [[spend-analytics]]
- [[MOC_FPA]] — budget check
