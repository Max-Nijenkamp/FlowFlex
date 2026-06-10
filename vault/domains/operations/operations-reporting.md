---
type: module
domain: Operations
domain-key: operations
panel: operations
module-key: operations.reporting
status: planned
priority: p3
depends-on: [operations.inventory, core.billing, core.rbac]
soft-depends: [operations.purchase-orders, operations.suppliers]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: []
permission-prefix: operations.reporting
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Operations Reporting

Inventory valuation, stock movement trends, supplier performance, and purchasing spend dashboards. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/operations/inventory\|operations.inventory]] | valuation + movement data |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | purchase-orders / suppliers | purchasing + supplier sections hidden without them |

---

## Core Features

- Inventory valuation report: total stock value by warehouse/category
- Stock movement trends: in/out over time
- Low-stock and out-of-stock report
- Supplier performance: on-time delivery, order accuracy
- Purchasing spend: by supplier, by category, over time
- Stock turnover ratio per item
- Dead stock report (no movement in N days, default 90 *(assumed)*)
- Export to Excel

---

## Data Model

No additional tables. Aggregates from `ops_items`, `ops_stock_levels`, `ops_stock_movements`, `ops_purchase_orders`, `ops_suppliers`.

## DTOs

Output only: `OperationsMetricsData` — valuation breakdown, movement series, spend tables, turnover/dead-stock lists.

## Services & Actions

- `OperationsAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): OperationsMetricsData` — aggregate queries, brick/money, no N+1

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:operations:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Reporting

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `OperationsDashboardPage` | #6 dashboard page + apex charts | date filter; excel export; soft-dep sections conditional |

---

## Permissions

`operations.reporting.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Valuation matches stock fixtures (brick/money)
- [ ] Turnover + dead-stock math over movement fixtures
- [ ] Soft-dep sections hidden when modules inactive
- [ ] Excel export

---

## Build Manifest

```
app/Data/Operations/OperationsMetricsData.php
app/Services/Operations/OperationsAnalyticsService.php
app/Filament/Operations/Pages/OperationsDashboardPage.php
app/Filament/Operations/Widgets/{ValuationWidget,MovementTrendWidget,SpendWidget,DeadStockWidget}.php
tests/Feature/Operations/OperationsReportingTest.php
```

---

## Related

- [[domains/operations/inventory]]
- [[domains/operations/suppliers]]
- [[architecture/caching]]
