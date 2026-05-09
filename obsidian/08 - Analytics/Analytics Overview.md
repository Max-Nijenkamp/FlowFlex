---
tags: [flowflex, domain/analytics, overview, phase/6]
domain: Analytics, BI & Reporting
panel: analytics
color: "#9333EA"
status: planned
last_updated: 2026-05-07
---

# Analytics Overview

Business intelligence and reporting across all active modules. Build dashboards, run reports, track KPIs, monitor team velocity, ask AI questions in plain English, and get predictive forecasts. All 8 modules built in Phase 6.

**Filament Panel:** `analytics`
**Domain Colour:** Purple `#9333EA` / Light: `#F3E8FF`
**Domain Icon:** `heroicon-o-chart-bar`
**Phase:** 6 — complete domain, all modules

## Modules

| Module | Description |
|---|---|
| [[Custom Dashboards]] | Drag-and-drop widget builder — metrics, charts, tables, funnels across all domains |
| [[Report Builder]] | Self-serve report builder, any domain, scheduled delivery, CSV/PDF export |
| [[KPI & Goal Tracking]] | Company KPIs, check-ins (on-track/at-risk/off-track), team and individual goals |
| [[Team Velocity & Ops Metrics]] | Cycle time, throughput, burnout signals, operational metrics snapshots |
| [[Audit Log & Activity Trail]] | Immutable activity log viewer, advanced filtering, export for compliance |
| [[Data Warehouse & Export]] | BigQuery/Snowflake/S3 export jobs — Enterprise-tier feature |
| [[AI Insights Engine]] | Natural language queries → charts + summaries; proactive anomaly and trend alerts |
| [[Predictive Analytics]] | Win probability, churn risk, attrition, demand forecasting, maintenance prediction |

## Filament Panel Structure

**Navigation Groups:**
- `Dashboards` — Dashboards, Widgets
- `Reports` — Reports, Report Runs
- `Goals` — KPIs, Goals, Check-ins
- `Velocity` — Velocity Snapshots, Ops Metrics
- `Compliance` — Audit Log, Audit Exports
- `Export` — Export Jobs, Export Schemas (Enterprise)

## Key Events

| Event | Source | Consumed By |
|---|---|---|
| `ReportGenerated` | Report Builder | Email (deliver to scheduled recipients) |
| `KPIOffTrack` | KPI & Goals | Notifications (notify KPI owner) |
| `BurnoutSignalDetected` | Team Velocity | HR (notify HR manager) |
| `ExportJobCompleted` | Data Warehouse | Notifications (notify requester) |

## Data Architecture

Analytics reads from all other domain tables. It does **not** write to domain tables. Uses read replicas where available. All queries are scoped to `company_id`.

The Audit Log module reads from Spatie's `activity_log` table — no additional models created, only a UI layer and export capability.

## Permissions Prefix

`analytics.dashboards.*` · `analytics.reports.*` · `analytics.kpis.*`  
`analytics.velocity.*` · `analytics.audit.*` · `analytics.export.*`

## Database Migration Range

`800000–849999`

## Related

- [[Custom Dashboards]]
- [[Report Builder]]
- [[KPI & Goal Tracking]]
- [[Team Velocity & Ops Metrics]]
- [[Audit Log & Activity Trail]]
- [[Data Warehouse & Export]]
- [[Panel Map]]
- [[Build Order (Phases)]]
