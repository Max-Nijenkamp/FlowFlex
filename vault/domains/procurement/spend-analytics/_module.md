---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.spend
status: planned
build-status: planned
priority: p3
depends-on: [procurement.requisitions, operations.purchase-orders, core.billing, core.rbac]
soft-depends: [procurement.catalogue, finance.budgets]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: []
permission-prefix: procurement.spend
encrypted-fields: []
last-reviewed: 2026-07-02
color: "#4ADE80"
---

# Spend Analytics

Procurement spend analysis: by supplier, category, department, and time. Identify savings opportunities and maverick spend. **Owns no tables** — a pure aggregation/reporting surface.

Hosted in **/operations** (Procurement nav → Reporting). See [[../_index|Procurement MOC]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../requisitions/_module\|procurement.requisitions]] + [[../../operations/purchase-orders/_module\|operations.purchase-orders]] | spend sources |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../supplier-catalogue/_module\|procurement.catalogue]] | maverick + savings detection |
| Soft | [[../../finance/budgets/_module\|finance.budgets]] | budget vs actual section |

---

## Core Features

- [[features/spend-breakdown\|Spend breakdown]] — by supplier, category, department; trend over time; top suppliers.
- [[features/maverick-spend\|Maverick spend detection]] — off-catalogue / non-approved-supplier lines.
- [[features/savings-tracking\|Savings tracking]] — agreed price vs actual PO price.
- [[features/committed-vs-actual\|Committed vs actual]] — plus budget-vs-actual (finance soft dep).
- [[features/export\|Export]] — rate-limited report export.

---

## Data Model

No owned tables. Aggregates read-only from `proc_requisitions`, `ops_purchase_orders`, `ops_po_lines`, `proc_catalogue_items`. Detail: [[data-model]].

## DTOs

Output-only `SpendMetricsData` — [[api]].

## Services & Actions

`SpendAnalyticsService::metrics(from, to)` — brick/money aggregates, no N+1; soft-dep sections conditional. See [[architecture]] + [[api]].

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:procurement:spend:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Reporting (Procurement)

| Artifact | UI kind | Feature |
|---|---|---|
| `SpendAnalyticsDashboard` | custom-page (dashboard + apex charts) | [[features/spend-breakdown]] |
| `SpendBySupplierWidget` / `MaverickSpendWidget` / `SavingsWidget` | widget | [[features/spend-breakdown]] / [[features/maverick-spend]] / [[features/savings-tracking]] |

**Access contract:** `canAccess() = Auth::user()->can('procurement.spend.view') && BillingService::hasModule('procurement.spend')` — [[../../../architecture/filament-patterns]] #1. Export rate-limited. See [[security]].

---

## Permissions

`procurement.spend.view`

---

## Cross-Domain Edges

- **Consumes (read):** requisitions (`proc_requisitions`), Operations POs (`ops_purchase_orders`, `ops_po_lines`), catalogue (`proc_catalogue_items`, soft), budgets (`finance.budgets`, soft) — **all read-only aggregation**.
- **Fires:** nothing.
- **Data ownership:** owns **no** tables; writes nothing anywhere. Reads happen through owning modules' data/read APIs, not by mutating them. See [[../../../security/data-ownership]].

Detail: [[decisions]] · [[unknowns]].

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Spend sums by supplier/category/department over fixtures
- [ ] Maverick detection flags off-catalogue lines
- [ ] Savings = (agreed − actual) × qty
- [ ] Soft-dep sections hidden when inactive

## Build Manifest

```
app/Data/Procurement/SpendMetricsData.php
app/Services/Procurement/SpendAnalyticsService.php
app/Filament/Operations/Pages/SpendAnalyticsDashboard.php
app/Filament/Operations/Widgets/{SpendBySupplierWidget,MaverickSpendWidget,SavingsWidget}.php
tests/Feature/Procurement/SpendAnalyticsTest.php
```

## Related

- [[../requisitions/_module]] · [[../../finance/budgets/_module]] · [[../../../architecture/caching]] · [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
