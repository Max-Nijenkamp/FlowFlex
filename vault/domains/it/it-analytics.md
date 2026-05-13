---
type: module
domain: IT & Security
panel: it
module-key: it.analytics
status: planned
color: "#4ADE80"
---

# IT Analytics

> Read-only IT metrics dashboard covering ticket volume, SLA performance, asset health, incident trends, and vulnerability backlog.

**Panel:** `it`
**Module key:** `it.analytics`

## What It Does

IT Analytics is the read-only reporting layer for the IT panel. It aggregates operational data from all other IT modules into a set of pre-built dashboards and KPI cards that give IT managers and CISOs a complete picture of IT health without querying individual modules. All views are read-only — data flows in automatically from service desk, incidents, assets, vulnerabilities, and access management. The module is designed for the weekly IT operations review and monthly board reporting on IT performance.

## Features

### Core
- Service desk KPIs: total tickets open, tickets opened vs closed this week, average first response time, average resolution time, CSAT score average
- SLA compliance: % tickets resolved within SLA by priority tier; SLA breach count by team and agent
- Incident metrics: incident count by severity, mean time to detect (MTTD), mean time to resolve (MTTR), recurring incidents (same root cause)
- Asset health: assets in each status (assigned, in repair, retired); warranty expiry upcoming count
- Vulnerability backlog: open vulnerabilities by severity; % remediated within SLA; oldest unresolved finding
- Access review completion: % access review items certified vs outstanding per campaign

### Advanced
- Trend charts: week-over-week trend for all key metrics across a configurable date range (30, 90, 180 days)
- Team performance: per-agent ticket volume, SLA compliance, and CSAT; per-team incident resolution time
- Change success rate: % of changes implemented without causing an incident
- IT cost per user: total software licence cost divided by headcount; tracked over time
- Comparative periods: compare this month to last month and same month last year for all key metrics
- Export: all dashboard views exportable to PDF for inclusion in board pack

### AI-Powered
- Narrative digest: weekly plain-language summary of IT performance highlights and concerns
- Predictive queue: forecast ticket volume for the next 4 weeks based on historical patterns and upcoming business events (new office opening, system migrations)

## Data Model

```erDiagram
    it_analytics_snapshots {
        ulid id PK
        ulid company_id FK
        string metric_key
        decimal value
        string dimension_key
        string dimension_value
        date snapshot_date
    }
```

| Table | Purpose |
|---|---|
| `it_analytics_snapshots` | Daily aggregated metric snapshots from all IT modules |

## Permissions

```
it.analytics.view-any
it.analytics.export
it.analytics.view-team-breakdown
it.analytics.view-agent-breakdown
it.analytics.manage-kpis
```

## Filament

**Resource class:** none (read-only pages only)
**Pages:** none
**Custom pages:** `ItOperationsDashboardPage` (main KPI overview), `ServiceDeskAnalyticsPage`, `IncidentAnalyticsPage`, `VulnerabilityTrendPage`
**Widgets:** `ItHealthScoreWidget`, `OpenTicketsByPriorityWidget`, `SlaComplianceSummaryWidget`, `VulnBacklogWidget`
**Nav group:** Compliance

## Displaces

| Competitor | Feature Replaced |
|---|---|
| ServiceNow Performance Analytics | IT operations metrics and dashboards |
| Freshservice Analytics | ITSM performance reporting |
| Power BI + ITSM data | Custom IT KPI dashboards |
| Splunk ITSI (SMB) | IT service intelligence dashboards |

## Related

- [[service-desk]] — ticket volume and SLA data source
- [[incident-management]] — incident metrics data source
- [[vulnerability-management]] — vulnerability backlog data source
- [[asset-management]] — asset health data source
- [[../analytics/dashboards]] — IT KPIs can be surfaced in company-wide BI dashboards
