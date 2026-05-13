---
type: domain-index
domain: Analytics & BI
panel: analytics
panel-path: /analytics
panel-color: Sky
color: "#4ADE80"
---

# Analytics & BI

One panel for configurable dashboards, scheduled reports, KPI tracking, external data connectors, and AI-powered anomaly detection — covering the BI needs of an SMB without Tableau, Looker, or Power BI.

**Panel:** `analytics` — `/analytics`
**Filament color:** Sky

---

## Modules

| Module | Key | Description |
|---|---|---|
| [[dashboards]] | analytics.dashboards | Configurable dashboards with widgets from any FlowFlex domain |
| [[reports]] | analytics.reports | Scheduled and on-demand reports with CSV/PDF export and template library |
| [[kpi-metrics]] | analytics.kpis | KPI definitions, targets, actuals, and trend lines linked to source data |
| [[data-connectors]] | analytics.connectors | External data source connections: Google Analytics, Stripe, HubSpot, custom API |
| [[anomaly-detection]] | analytics.anomalies | AI-powered anomaly detection on key metrics with configurable alerts |
| [[scheduled-reports]] | analytics.scheduled | Schedule reports to deliver by email or Slack on a cadence |
| [[product-analytics]] | analytics.product | Event tracking, funnel analysis, retention cohorts, session heatmaps, and feature adoption — internal platform usage (V1) and embeddable SDK for company products (V2) |

---

## Nav Groups

- **Dashboards** — dashboards, kpi-metrics
- **Reports** — reports, scheduled-reports
- **Data** — data-connectors, anomaly-detection
- **Settings** — data refresh schedules, notification channels

---

## Displaces

| Tool | Replaced By |
|---|---|
| Tableau | dashboards, reports |
| Looker | dashboards, kpi-metrics, data-connectors |
| Power BI (SMB) | dashboards, reports, scheduled-reports |
| Datadog / Grafana (business metrics) | anomaly-detection, kpi-metrics |
| Fivetran + dbt (lite) | data-connectors |

---

## Related

- [[../marketing/analytics]] — marketing attribution feeds analytics domain
- [[../finance/INDEX]] — financial KPIs and revenue data
- [[../hr/INDEX]] — headcount, attendance, and performance metrics
- [[../ecommerce/analytics]] — ecommerce revenue and conversion data
- [[../operations/INDEX]] — operations KPIs and stock metrics
