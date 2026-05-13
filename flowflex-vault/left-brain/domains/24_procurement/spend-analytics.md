---
type: module
domain: Procurement & Spend Management
panel: procurement
phase: 3
status: complete
cssclasses: domain-procurement
migration_range: 982500–982999
last_updated: 2026-05-12
---

# Spend Analytics

Full visibility into where money is being spent. By supplier, category, department, cost centre, and time period. Identifies savings opportunities and compliance gaps.

---

## Core Reports

### Spend by Category
Pareto analysis: top 10 categories = 80% of spend. Drill down to subcategory → individual items.

### Spend by Supplier
- Total spend per supplier (all-in: PO value + invoice totals)
- Single-source risk: suppliers with >20% share flagged
- Preferred vs non-preferred spend ratio

### Spend by Department / Cost Centre
- Actual spend vs approved budget per department
- Unauthorised spend (no PO): invoices with no matching PO
- Requisition compliance rate: % of spend raised via proper PR process

### Maverick Spend
Spend without PO or outside catalog:
- Invoice arrived with no prior PO → flagged as maverick
- Tracked by department and requester
- Target: maverick spend < 5% of total

---

## Savings Tracking

- **Negotiated savings**: difference between list price and contracted price × volume
- **Realised savings**: where negotiated prices were actually used
- **Avoided costs**: spend that was rejected/redirected by procurement review

---

## Budget vs Actual

Links to FP&A cost centre budgets:
- Real-time committed spend (open POs not yet invoiced)
- Actual spend (paid invoices)
- Remaining budget
- Forecast to year-end based on run rate

---

## Supplier Consolidation Opportunities

ML-assisted analysis:
- Same product from 3 suppliers → consolidation opportunity
- Volume threshold suggestions (e.g., consolidate to single supplier for better pricing)
- Duplicate supplier detection (same supplier registered twice)

---

## Data Model

Views/aggregates built from existing tables (`proc_purchase_orders`, `proc_supplier_invoices`, `proc_requisitions`) — no new primary tables needed.

### `proc_spend_summary` (materialised view)
| Column | Type | Notes |
|---|---|---|
| tenant_id | ulid | |
| period | date | month start |
| supplier_id | ulid | |
| category | varchar(100) | |
| department | varchar(100) | |
| po_spend | decimal(14,2) | |
| invoice_spend | decimal(14,2) | |
| maverick_spend | decimal(14,2) | |

---

## Migration

```
982500_create_proc_spend_summary_view
982501_create_proc_savings_tracker_table
```

---

## Related

- [[MOC_Procurement]]
- [[purchase-orders]]
- [[three-way-match-invoice-approval]]
- [[supplier-catalog]]
- [[MOC_FPA]] — budget vs actual integration
