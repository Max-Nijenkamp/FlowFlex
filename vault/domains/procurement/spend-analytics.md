---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.spend
status: planned
priority: p3
depends-on: [procurement.requisitions, operations.purchase-orders, core.billing, core.rbac]
soft-depends: [procurement.catalogue, finance.budgets]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: []
permission-prefix: procurement.spend
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Spend Analytics

Procurement spend analysis: by supplier, category, department, and time. Identify savings opportunities and maverick spend. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/procurement/requisitions\|procurement.requisitions]] + [[domains/operations/purchase-orders\|operations.purchase-orders]] | spend sources |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/procurement/supplier-catalogue\|procurement.catalogue]] | maverick + savings detection |
| Soft | [[domains/finance/budgets\|finance.budgets]] | budget vs actual section |

---

## Core Features

- Total spend by supplier, category, department
- Spend trend over time
- Maverick spend detection (PO lines without catalogue item or non-approved supplier)
- Committed vs actual spend
- Savings tracking (catalogue agreed price vs PO price)
- Top suppliers by spend
- Budget vs actual procurement spend (links Finance budgets)
- Export reports

---

## Data Model

No additional tables. Aggregates from `proc_requisitions`, `ops_purchase_orders`, `ops_po_lines`, `proc_catalogue_items`.

## DTOs

Output only: `SpendMetricsData` — spend breakdowns, trend series, maverick list, savings table, budget comparison.

## Services & Actions

- `SpendAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): SpendMetricsData` — brick/money aggregates, no N+1; soft-dep sections conditional

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:procurement:spend:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Reporting (Procurement)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SpendAnalyticsDashboard` | #6 dashboard page + apex charts | filters: period/supplier/category; export |

---

## Permissions

`procurement.spend.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Spend sums by supplier/category/department over fixtures
- [ ] Maverick detection flags off-catalogue lines
- [ ] Savings = (agreed − actual) × qty
- [ ] Soft-dep sections hidden when inactive

---

## Build Manifest

```
app/Data/Procurement/SpendMetricsData.php
app/Services/Procurement/SpendAnalyticsService.php
app/Filament/Operations/Pages/SpendAnalyticsDashboard.php
app/Filament/Operations/Widgets/{SpendBySupplierWidget,MaverickSpendWidget,SavingsWidget}.php
tests/Feature/Procurement/SpendAnalyticsTest.php
```

---

## Related

- [[domains/procurement/requisitions]]
- [[domains/finance/budgets]]
- [[architecture/caching]]
