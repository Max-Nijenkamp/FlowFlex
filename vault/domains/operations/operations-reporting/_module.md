---
domain: operations
module: operations-reporting
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Operations Reporting

Inventory valuation, stock-movement trends, supplier performance, and purchasing-spend dashboards. **Owns no tables** — aggregates read-only across the other Operations modules.

> Operations hosts the [[../../procurement/_index|Procurement]] panel. See [[../../../decisions/decision-2026-06-01-panel-consolidation]].

---

## Module-key

`operations.reporting`

**Priority:** p3
**Panel:** operations (Orange)
**Permission prefix:** `operations.reporting`
**Tables:** none (read-only aggregation)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../inventory/_module\|operations.inventory]] | valuation + movement data |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../purchase-orders/_module\|operations.purchase-orders]] / [[../suppliers/_module\|operations.suppliers]] | purchasing + supplier sections hidden without them |

---

## Core Features

- Inventory valuation by warehouse/category
- Stock movement trends (in/out over time)
- Low-stock + out-of-stock report
- Supplier performance (on-time, accuracy)
- Purchasing spend by supplier/category/time
- Stock turnover per item; dead-stock report (no movement in N days, default 90 *(assumed)*)
- Export to Excel

See features: [[./features/valuation-report|Valuation Report]] · [[./features/spend-analytics|Spend Analytics]] · [[./features/dead-stock-report|Dead-Stock & Turnover]].

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Valuation matches stock fixtures (brick/money)
- [ ] Turnover + dead-stock math over movement fixtures
- [ ] Soft-dep sections hidden when modules inactive
- [ ] Excel export

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `StockService::valuation`, movement/PO/supplier queries | inventory, PO, suppliers (same domain) | read-only aggregation |

**Data ownership:** `operations.reporting` **owns no tables and writes nothing**. It reads across `ops_items`, `ops_stock_levels`, `ops_stock_movements`, `ops_purchase_orders`, `ops_suppliers` (all owned elsewhere) purely for aggregation, cached in Redis ([[../../../security/data-ownership]]).

---

## Related

- [[../inventory/_module|operations.inventory]]
- [[../suppliers/_module|operations.suppliers]]
- [[../../../architecture/caching]]
- [[../_index|Operations MOC]]
