---
type: module
domain: Procurement
panel: operations
module-key: procurement.requisitions
status: planned
color: "#4ADE80"
---

# Purchase Requisitions

Internal purchase requests with approval workflow before becoming purchase orders. Employees request, managers approve, procurement converts to PO.

## Core Features

- Requisition: requester, items/description, estimated cost, justification, department, budget line
- Status machine: `draft → submitted → approved | rejected → converted_to_po`
- Multi-level approval based on amount thresholds
- Budget check: validate against department budget (links to Finance)
- Convert approved requisition to a purchase order (Operations)
- Requisition templates for recurring requests
- Rejection with reason

## Data Model

| Table | Key Columns |
|---|---|
| `proc_requisitions` | company_id, requisition_number, requester_id, department_id, description, estimated_cost_cents, justification, status, budget_line_id |
| `proc_requisition_items` | requisition_id, company_id, description, quantity, estimated_unit_cost_cents |
| `proc_requisition_approvals` | requisition_id, company_id, approver_id, action, comment, acted_at |

## Filament

**Nav group:** Requisitions

- `RequisitionResource` — create, submit, approve/reject
- My requisitions + approval queue views

## Cross-Domain / Events

- Approved requisition → create PO (Operations)
- Budget check against Finance budgets

## Related

- [[domains/operations/purchase-orders]]
- [[domains/finance/budgets]]
