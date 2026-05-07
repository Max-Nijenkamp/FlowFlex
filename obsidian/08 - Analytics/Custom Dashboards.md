---
tags: [flowflex, domain/analytics, dashboards, phase/6]
domain: Analytics
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-07
---

# Custom Dashboards

Drag-and-drop dashboard builder. Every team sees the metrics relevant to them — build once, share with the team, or set as the company default.

**Who uses it:** All roles (personalised dashboards), managers, leadership
**Filament Panel:** `analytics`
**Depends on:** All active module domains (data sources), Core
**Phase:** 6
**Build complexity:** Very High — 2 resources, 2 pages, 2 tables

---

## Features

- **Drag-and-drop widget builder** — add, resize, and reorder widgets on a grid canvas; layout stored as JSON per dashboard
- **Widget types** — metric card (single number with trend), line chart, bar chart, donut chart, data table, funnel, heatmap; more added per phase
- **Data source selector** — each widget selects its source from any active module (HR metrics, Finance metrics, CRM pipeline, Projects, etc.); query built server-side
- **Filter configuration per widget** — filter by date range, status, team, department, or custom dimension without writing code
- **Multiple dashboards per tenant** — tenants create personal dashboards; one can be flagged `is_default` to open on login
- **Shared dashboards** — mark a dashboard as shared; all tenants can view (but not edit) shared dashboards; useful for company-wide OKR boards
- **Auto-refresh intervals** — each dashboard has a configurable refresh rate (off/1 min/5 min/15 min/hourly); data pulled fresh on interval
- **Role-based defaults** — admins can push a dashboard as the default for a specific role so new employees see relevant metrics on first login
- **Export to PDF** — export current dashboard view as a PDF snapshot for leadership reports or board packs
- **Embed widget** — generate an embeddable iframe URL for a specific widget for embedding in external tools (Enterprise tier)
- **Mobile-responsive** — dashboards reflow to single-column on small screens; widget ordering maintained
- **Reads live from domain tables** — no separate analytics tables; widgets query the live operational tables (with indexed columns) to ensure freshness

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `dashboards`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `description` | text nullable | |
| `tenant_id` | ulid FK | owner → tenants |
| `is_default` | boolean default false | opens on login for owner |
| `is_shared` | boolean default false | visible to all tenants in company |
| `layout` | json | grid layout config: {cols, rows, breakpoints} |
| `refresh_seconds` | integer nullable | null = manual refresh only |
| `target_role` | string nullable | role slug for role-default dashboards |

### `dashboard_widgets`
| Column | Type | Notes |
|---|---|---|
| `dashboard_id` | ulid FK | → dashboards |
| `type` | enum | `metric`, `chart`, `table`, `funnel`, `heatmap` |
| `title` | string | |
| `data_source` | string | e.g. "hr.leave_requests", "finance.invoices" |
| `filters` | json | {date_range: "30d", status: "active", ...} |
| `config` | json | chart type, colours, axis labels, aggregation |
| `position` | json | {x, y, w, h} grid coordinates |
| `sort_order` | integer default 0 | |

---

## Events Fired

None — Dashboards are read-only views.

---

## Events Consumed

None — reads live from all other domain tables.

---

## Permissions

```
analytics.dashboards.view
analytics.dashboards.create
analytics.dashboards.edit
analytics.dashboards.delete
analytics.dashboards.share
analytics.dashboards.set-default
analytics.dashboard-widgets.view
analytics.dashboard-widgets.create
analytics.dashboard-widgets.edit
analytics.dashboard-widgets.delete
```

---

## Related

- [[Analytics Overview]]
- [[Report Builder]]
- [[KPI & Goal Tracking]]
- [[Data Visualisation]]
