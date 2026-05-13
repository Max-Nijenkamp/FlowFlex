---
type: module
domain: Analytics & Reporting
panel: analytics
phase: 3
status: complete
cssclasses: domain-analytics
migration_range: 503000–503499
last_updated: 2026-05-12
right_brain_log: "[[builder-log-analytics-phase6]]"
---

# Dashboard Builder

Drag-and-drop dashboard creation for any role. No SQL or BI tool needed. Publish dashboards to teams, embed in other modules, and schedule automated delivery.

---

## Widget Library

| Widget Type | Description |
|---|---|
| KPI card | Single metric with trend arrow |
| Line chart | Trends over time |
| Bar chart | Comparisons across categories |
| Pie / donut | Proportional breakdown |
| Table | Sortable, filterable data grid |
| Funnel | Conversion stages |
| Gauge | Progress toward target |
| Text/markdown | Context and commentary |
| Map | Geographic distribution |

---

## Data Sources

Connect to any FlowFlex module:
- CRM: pipeline, deals, contacts, activities
- Finance: revenue, expenses, cash position
- HR: headcount, attendance, performance
- Operations: stock levels, orders, fulfilment
- Subscriptions: MRR, churn, ARR

Multiple sources on one dashboard (e.g., revenue from Finance + pipeline from CRM).

---

## Dashboard Builder

Drag widgets onto a grid layout:
- Resize, reorder freely
- Configure each widget: data source, filters, grouping, time period
- Color themes: match brand or use role-based themes

---

## Access Control

- **Private**: creator only
- **Team**: specific team or role
- **Public (read)**: anyone in organisation
- **Embedded**: embed in another module (e.g., GM homepage)
- **External**: share secure view-only link (for board members, investors)

---

## Filters & Drill-Down

Dashboard-level filters affect all widgets:
- Date range picker
- Entity filter (one department, one region)
- Click a bar in a chart → table widget below filters to that segment

---

## Automated Delivery

Schedule dashboard snapshots:
- Daily 8am: operations dashboard → slack channel
- Weekly Monday: revenue KPIs → CFO email
- Monthly: board pack → secure link emailed to board

---

## Data Model

### `an_dashboards`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| created_by | ulid | FK |
| visibility | enum | private/team/public/external |
| layout | json | widget grid config |

### `an_widgets`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| dashboard_id | ulid | FK |
| type | varchar(50) | |
| title | varchar(200) | |
| data_source | varchar(100) | |
| config | json | query + display settings |
| position | json | x, y, w, h |

---

## Migration

```
503000_create_an_dashboards_table
503001_create_an_widgets_table
503002_create_an_dashboard_shares_table
```

---

## Related

- [[MOC_Analytics]]
- [[embedded-analytics]]
- [[scheduled-reports]]
- [[data-connectors-etl]]
