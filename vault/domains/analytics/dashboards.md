---
type: module
domain: Analytics & BI
domain-key: analytics
panel: analytics
module-key: analytics.dashboards
status: planned
priority: p3
depends-on: [core.billing, core.rbac]
soft-depends: [analytics.kpis, analytics.reports]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [bi_dashboards, bi_widgets]
permission-prefix: analytics.dashboards
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Custom Dashboards

Drag-and-drop dashboard builder. Compose widgets pulling data from any domain into custom views. The flagship of the Analytics domain — build first in `/analytics` (it ships the `MetricRegistry` other modules use).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | every active domain | widgets read via `MetricRegistry` — only metrics of active modules offered |

---

## Core Features

- Dashboard record: name, layout (grid of widgets), owner, shared/private
- Widget library: stat cards, line/bar/pie charts, tables, KPI gauges
- Each widget bound to a data source (domain + metric + filters) from the **`MetricRegistry`** — domains register named, pre-aggregated, CompanyScope-safe metrics; widgets never run free-form queries
- Drag-and-drop grid layout (resize, reposition widgets)
- Widget data refresh: cached per widget, TTL 15 min *(assumed)*
- Date range filter applies dashboard-wide
- Share dashboard with team or keep private (owner-only)
- Dashboard templates for common views (Sales Overview, HR Overview, Finance Overview) — seeded
- Cross-domain widgets: combine CRM pipeline + Finance revenue in one view

---

## Data Model

### bi_dashboards — id, company_id (indexed), name, layout (jsonb grid), owner_id FK, is_shared (bool), deleted_at
### bi_widgets — id, dashboard_id FK, company_id, type (in registry), data_source (jsonb: metric_key + filters, validated against MetricRegistry), position (jsonb)

---

## DTOs

### CreateDashboardData — name (required), is_shared
### AddWidgetData — dashboard_id (own or shared-editable *(assumed: owner edits only)*), type (in set), metric_key (registered + module active), filters (validated per metric)

## Services & Actions

- `MetricRegistry::register(string $key, MetricDefinition $def)` — domains register in providers; each metric = closure returning aggregates under CompanyScope
- `MetricRegistry::available(): Collection` — filtered by `hasModule`
- `WidgetDataService::resolve(Widget $w, DateRange $range): array` — cached

---

## Filament

**Nav group:** Dashboards

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DashboardBuilderPage` | #6 dashboard custom page | drag-drop grid (Livewire + Alpine), widget picker |
| `DashboardResource` | #1 CRUD resource | list/manage, share toggle |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('analytics.dashboards.view-any') && BillingService::hasModule('analytics.dashboards')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`analytics.dashboards.view-any` · `analytics.dashboards.create` · `analytics.dashboards.update-own` · `analytics.dashboards.manage-shared`

---

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:bi:widget:{widget}:{range}` | 15 min | TTL only |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Widget metric must exist in registry AND its module active — else rejected/hidden
- [ ] Private dashboard invisible to others; shared visible read-only
- [ ] Deactivating a module hides its widgets (no errors)
- [ ] Widget data cached; date range recomputes
- [ ] Templates seed working dashboards

---

## Build Manifest

```
database/migrations/xxxx_create_bi_dashboards_table.php
database/migrations/xxxx_create_bi_widgets_table.php
app/Models/Analytics/{Dashboard,Widget}.php
app/Data/Analytics/{CreateDashboardData,AddWidgetData}.php
app/Support/Analytics/{MetricRegistry,MetricDefinition}.php
app/Services/Analytics/WidgetDataService.php
app/Providers/Analytics/AnalyticsServiceProvider.php
app/Filament/Analytics/Pages/DashboardBuilderPage.php
app/Filament/Analytics/Resources/DashboardResource.php
database/seeders/DashboardTemplatesSeeder.php
database/factories/Analytics/DashboardFactory.php
tests/Feature/Analytics/{DashboardBuilderTest,MetricRegistryTest}.php
```

---

## Related

- [[domains/analytics/report-builder]]
- [[domains/analytics/kpi-tracking]]
- [[architecture/caching]]
- [[architecture/patterns/custom-pages]]
