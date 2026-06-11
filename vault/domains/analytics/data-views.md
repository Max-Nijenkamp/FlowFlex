---
type: module
domain: Analytics & BI
domain-key: analytics
panel: analytics
module-key: analytics.data-views
status: planned
priority: p3
depends-on: [analytics.dashboards, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: analytics.data-views
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Cross-Domain Data Views

Pre-built combined views that join data across domains (e.g. revenue per employee, deals per marketing source) — the "BI" depth beyond single-domain dashboards. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/analytics/dashboards\|analytics.dashboards]] | MetricRegistry infrastructure |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

(Each individual view additionally requires its source modules — availability checked per view.)

---

## Core Features

- Pre-built cross-domain views maintained by FlowFlex (shipped as `DataView` classes, registered)
- v1 set *(assumed)*: revenue per sales rep (CRM+Finance), project profitability (time cost vs invoiced — Projects+Finance), marketing source → closed revenue (Marketing+CRM+Finance), revenue per employee (HR+Finance)
- Read-only aggregated query views — CompanyScope on every involved table
- Drill-down: click an aggregate to see underlying records
- Each view declares `requiredModules()` — only listed views whose modules are all active
- Export view data (Excel)

---

## Data Model

No persistent tables — query-time aggregations across domain tables, always company-filtered. Heavy queries cached.

## DTOs

Output only: per-view `DataViewResult` (columns, rows, drill targets).

## Services & Actions

- `DataViewRegistry::register(class-string $view)` / `available(): Collection` (module check)
- `DataView` contract: `requiredModules(): array`, `run(DateRange $r): DataViewResult`, `drillDown(...)`

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:bi:view:{view}:{range}` | 1 h | TTL only |

---

## Filament

**Nav group:** Data Views

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DataViewsPage` | #6 gallery custom page | available views (module-filtered) |
| Per-view page | #9 report page | charts + drill-down table, export |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('analytics.data-views.view-any') && BillingService::hasModule('analytics.data-views')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Cite a rate limiter (e.g. throttle on the export action) for the data-view export endpoint per architecture/security.md.

---

## Permissions

`analytics.data-views.view`

---

## Test Checklist

- [ ] Tenant isolation: every shipped view returns only current-company aggregates (per-view test)
- [ ] View hidden when any required module inactive
- [ ] Drill-down rows match aggregate
- [ ] Revenue-per-rep + project-profitability math over fixtures
- [ ] Indexed queries (no full scans on fixtures — explain check *(assumed: manual)*)

---

## Build Manifest

```
app/Support/Analytics/{DataViewRegistry,DataViewContract,DataViewResult}.php
app/Analytics/Views/{RevenuePerRepView,ProjectProfitabilityView,MarketingSourceRevenueView,RevenuePerEmployeeView}.php
app/Filament/Analytics/Pages/DataViewsPage.php
tests/Feature/Analytics/DataViewsTest.php
```

---

## Related

- [[domains/analytics/dashboards]]
- [[architecture/performance]]
- [[architecture/caching]]
