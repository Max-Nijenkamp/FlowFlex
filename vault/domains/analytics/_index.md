---
type: domain-index
domain: Analytics & BI
panel: analytics
color: "#4ADE80"
---

# Analytics & BI

Custom dashboards, report builder, KPI tracking, cross-domain data views, and scheduled exports. **Panel:** `/analytics` (Sky) — Phase 3.

---

## Navigation Groups

- **Dashboards** — Custom Dashboards, Dashboard Builder
- **Reports** — Report Builder, Saved Reports, Scheduled Exports
- **KPIs** — KPI Tracking
- **Data Views** — Cross-Domain Views

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/analytics/dashboards\|Custom Dashboards]] | `analytics.dashboards` | planned | **P3 core** |
| [[domains/analytics/report-builder\|Report Builder]] | `analytics.reports` | planned | **P3 core** |
| [[domains/analytics/kpi-tracking\|KPI Tracking]] | `analytics.kpis` | planned | P3 |
| [[domains/analytics/data-views\|Cross-Domain Data Views]] | `analytics.data-views` | planned | P3 |
| [[domains/analytics/scheduled-exports\|Scheduled Exports]] | `analytics.exports` | planned | P3 |

---

## Key Patterns

- `leandrocfe/filament-apex-charts` — all chart widgets
- `maatwebsite/laravel-excel` + `spatie/laravel-pdf` — exports
- Heavy caching of aggregations (see [[architecture/caching]], [[architecture/performance]])
- Always enforces CompanyScope — no cross-tenant data leakage (see [[architecture/multi-tenancy]])
- Reads metrics from all active domains; respects module activation
