---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.dashboards
status: planned
color: "#4ADE80"
---

# Custom Dashboards

Drag-and-drop dashboard builder. Compose widgets pulling data from any domain into custom views. The flagship of the Analytics domain.

## Core Features

- Dashboard record: name, layout (grid of widgets), owner, shared/private
- Widget library: stat cards, line/bar/pie charts, tables, KPI gauges
- Each widget bound to a data source (domain + metric + filters)
- Drag-and-drop grid layout (resize, reposition widgets)
- Widget data refresh interval (cached, see [[architecture/caching]])
- Date range filter applies dashboard-wide
- Share dashboard with team or keep private
- Dashboard templates for common views (Sales Overview, HR Overview, Finance Overview)
- Cross-domain widgets: combine CRM pipeline + Finance revenue in one view

## Data Model

| Table | Key Columns |
|---|---|
| `bi_dashboards` | company_id, name, layout (json), owner_id, is_shared |
| `bi_widgets` | dashboard_id, company_id, type, data_source (json: domain, metric, filters), position (json) |

## Filament

**Nav group:** Dashboards

- `DashboardBuilderPage` (custom page) — drag-and-drop widget grid (Livewire + Alpine.js)
- `DashboardResource` — list, create, manage dashboards
- Widgets render via `leandrocfe/filament-apex-charts`

## Cross-Domain

- Reads aggregated metrics from all active domains via a metric registry

## Related

- [[domains/analytics/report-builder]]
- [[domains/analytics/kpi-tracking]]
- [[architecture/caching]]
- [[architecture/patterns/custom-pages]]
