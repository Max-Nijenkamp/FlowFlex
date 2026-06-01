---
type: module
domain: Procurement
panel: operations
module-key: procurement.spend
status: planned
color: "#4ADE80"
---

# Spend Analytics

Procurement spend analysis: by supplier, category, department, and time. Identify savings opportunities and maverick spend.

## Core Features

- Total spend by supplier, category, department
- Spend trend over time
- Maverick spend detection (purchases outside approved catalogue/suppliers)
- Committed vs actual spend
- Savings tracking (negotiated vs list price)
- Top suppliers by spend
- Budget vs actual procurement spend (links Finance budgets)
- Export reports

## Data Model

No additional tables. Aggregates from `proc_requisitions`, `ops_purchase_orders`, `proc_catalogue_items`.

## Filament

**Nav group:** Reporting

- `SpendAnalyticsDashboard` (custom dashboard) — chart widgets

## Related

- [[domains/procurement/requisitions]]
- [[domains/finance/budgets]]
- [[architecture/performance]]
