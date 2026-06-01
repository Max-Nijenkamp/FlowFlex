---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.kpis
status: planned
color: "#4ADE80"
---

# KPI Tracking

Define key performance indicators with targets, track actuals over time, and visualise progress against goals.

## Core Features

- KPI definition: name, metric source, target value, unit, period (monthly/quarterly)
- Actual value: computed from a data source or manually entered
- Target vs actual visualisation: gauge, trend line
- Status: on-target, below-target, above-target
- KPI categories (revenue, growth, efficiency, customer)
- Trend over multiple periods
- KPI snapshots stored per period for historical comparison
- Alert when KPI falls below threshold

## Data Model

| Table | Key Columns |
|---|---|
| `bi_kpis` | company_id, name, category, metric_source (json), target_value, unit, period, owner_id |
| `bi_kpi_snapshots` | kpi_id, company_id, period_label, actual_value, target_value, captured_at |

## Filament

**Nav group:** KPIs

- `KpiResource` — define KPIs, set targets
- `KpiDashboardPage` (custom page) — gauges + trend charts
- Scheduled job captures KPI snapshots per period

## Related

- [[domains/analytics/dashboards]]
- [[domains/projects/okrs]]
