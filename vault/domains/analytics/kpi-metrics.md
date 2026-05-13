---
type: module
domain: Analytics & BI
panel: analytics
module-key: analytics.kpis
status: planned
color: "#4ADE80"
---

# KPI Metrics

> Define KPIs, set targets, track actuals from live source data, and visualise trend lines — without maintaining a spreadsheet.

**Panel:** `analytics`
**Module key:** `analytics.kpis`

## What It Does

KPI Metrics is the target-tracking layer of the analytics panel. Rather than having targets scattered across department spreadsheets, each KPI is defined once in FlowFlex with a data source, aggregation method, target value, and target period. The system pulls the actual value automatically on each refresh so the KPI card always shows current vs target. Trend lines display the trajectory over time so teams can see whether they are improving or declining before they miss a goal.

## Features

### Core
- KPI definition: name, description, unit (currency, number, percentage, days), owner, and target refresh cadence (daily, weekly, monthly)
- Data source linking: connect KPI to a FlowFlex data source (e.g., "CRM deals won this month count", "Finance MRR", "HR headcount")
- Target setting: numeric target per period; set annually and optionally broken into monthly sub-targets
- Actual vs target display: current actual pulled automatically; green/amber/red RAG status based on % of target achieved
- KPI cards: thumbnail view of all KPIs with current value, target, and trend arrow
- KPI detail view: full trend chart, target line, and historical actual data

### Advanced
- KPI categories: group KPIs by domain or strategic objective (Revenue, Customers, Operations, People)
- Cascading targets: company-level target broken down to department or team sub-targets
- Benchmark comparison: compare a KPI actual against an industry benchmark value (manually entered)
- Forecast to target: given current trajectory, project whether the period target will be met
- KPI library: 50+ pre-defined KPI templates for common metrics (MRR, CAC, NPS, OTIF, eNPS, DSO, Churn Rate)
- Alerting: trigger an anomaly alert when a KPI misses its weekly sub-target by more than a configurable threshold

### AI-Powered
- Target suggestion: recommend a realistic target for the next period based on historical trend and growth rate
- Correlation discovery: identify which other KPIs move together so teams understand leading and lagging indicators

## Data Model

```erDiagram
    an_kpis {
        ulid id PK
        ulid company_id FK
        string name
        string description
        string unit
        string category
        ulid owner_id FK
        string data_source_key
        json query_config
        string cadence
        decimal current_value
        decimal period_target
        string rag_status
        timestamps timestamps
    }

    an_kpi_targets {
        ulid id PK
        ulid kpi_id FK
        string period_label
        date period_start
        date period_end
        decimal target_value
        timestamps timestamps
    }

    an_kpi_actuals {
        ulid id PK
        ulid kpi_id FK
        decimal actual_value
        date recorded_on
        timestamps timestamps
    }

    an_kpis ||--o{ an_kpi_targets : "has"
    an_kpis ||--o{ an_kpi_actuals : "records"
```

| Table | Purpose |
|---|---|
| `an_kpis` | KPI definitions with current value and RAG status |
| `an_kpi_targets` | Period-by-period target values |
| `an_kpi_actuals` | Historical actual values per refresh cycle |

## Permissions

```
analytics.kpis.view-any
analytics.kpis.create
analytics.kpis.update
analytics.kpis.set-targets
analytics.kpis.delete
```

## Filament

**Resource class:** `KpiResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `KpiOverviewPage` (card grid with RAG status and trend arrows)
**Widgets:** `KpiSummaryWidget` (top-level KPI health: % green, % amber, % red)
**Nav group:** Dashboards

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Klipfolio | KPI tracking and target dashboards |
| Geckoboard | Real-time KPI display boards |
| Databox | KPI goal tracking with data source connections |
| Cascade Strategy | Strategic KPI and OKR target management |

## Related

- [[dashboards]] — KPI cards embedded in dashboards
- [[anomaly-detection]] — alert when a KPI breaches expected range
- [[data-connectors]] — external KPI data pulled through connectors
- [[scheduled-reports]] — KPI summaries delivered on a cadence
