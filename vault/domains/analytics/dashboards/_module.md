---
domain: analytics
module: dashboards
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Custom Dashboards

Drag-and-drop dashboard builder. Compose widgets that pull pre-aggregated metrics from any active domain into custom views. The flagship of Analytics — build first in `/analytics`, because it ships the **`MetricRegistry`** every other Analytics module reads from.

> This module is planned for build. Any "shipped/built" language in older notes reflects the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]].

---

## Module-key

`analytics.dashboards`

**Priority:** p3
**Panel:** analytics
**Permission prefix:** `analytics.dashboards`
**Tables:** `bi_dashboards`, `bi_widgets`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | every active domain | widgets read via `MetricRegistry` — only metrics of active modules are offered |

---

## Core Features

- Dashboard record: name, grid layout, owner, shared/private
- Widget library: stat cards, line/bar/pie charts, tables, KPI gauges
- Each widget bound to a data source (domain + metric key + filters) drawn from the **`MetricRegistry`** — domains register named, pre-aggregated, CompanyScope-safe metrics; widgets never run free-form queries
- Drag-and-drop grid layout (resize, reposition)
- Per-widget cached data refresh (TTL 15 min *(assumed)*)
- Dashboard-wide date-range filter
- Share with team or keep private (owner-only edit)
- Seeded templates (Sales Overview, HR Overview, Finance Overview)
- Cross-domain widgets: e.g. CRM pipeline + Finance revenue in one view — **each metric reads its owning domain's data via that domain's registered metric closure, never Analytics touching another domain's tables** ([[../../../security/data-ownership]])

See feature notes: [[./features/metric-registry|MetricRegistry]] · [[./features/dashboard-builder|Dashboard Builder]] · [[./features/dashboard-sharing|Sharing]] · [[./features/widget-rendering|Widget Rendering]].

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Widget metric must exist in registry AND its module active — else rejected/hidden
- [ ] Private dashboard invisible to others; shared visible read-only
- [ ] Deactivating a module hides its widgets (no errors)
- [ ] Widget data cached; date range recomputes
- [ ] Templates seed working dashboards

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `MetricRegistry` closures | every active domain | Each domain registers CompanyScope-safe, pre-aggregated metric closures in its provider; Analytics invokes them read-only. No events. |

**Data ownership:** `analytics.dashboards` writes only `bi_dashboards`, `bi_widgets`. Cross-domain data is read **exclusively** through each owning domain's registered metric closure (its own service/query) — Analytics never reads or writes another domain's tables directly ([[../../../security/data-ownership]]). This is the domain-defining boundary for all of Analytics.

---

## Related

- [[../kpi-tracking/_module|analytics.kpis]]
- [[../data-views/_module|analytics.data-views]]
- [[../report-builder/_module|analytics.reports]]
- [[../../../architecture/caching]]
- [[../../../architecture/patterns/custom-pages]]
- [[../../../security/data-ownership]]
