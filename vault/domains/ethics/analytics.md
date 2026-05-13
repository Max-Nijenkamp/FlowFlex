---
type: module
domain: Whistleblowing & Ethics
panel: ethics
module-key: ethics.analytics
status: planned
color: "#4ADE80"
---

# Reporting Analytics

> Read-only ethics program metrics — incident report volumes, case resolution time, category breakdown, and policy acknowledgment rates.

**Panel:** `ethics`
**Module key:** `ethics.analytics`

---

## What It Does

Reporting Analytics gives ethics officers and the board a quantitative view of the health and effectiveness of the company's ethics program. It aggregates data from incident reports, investigation cases, and policy acknowledgments to produce trend views, category breakdowns, and performance metrics. All data is presented at an aggregate or anonymised level to preserve reporter confidentiality. The analytics can be exported as a PDF for ethics committee and board committee reporting.

---

## Features

### Core
- Incident volume trend: monthly report submission count with year-on-year comparison
- Report category breakdown: distribution of reports across incident categories
- Average time-to-resolution: mean days from report submission to case closure by category
- Open vs closed case ratio: current pipeline of active investigations
- Policy acknowledgment rates: completion percentage per policy by department and overall
- Export: ethics program metrics report as PDF for board and committee presentation

### Advanced
- Geographic breakdown: report volumes by office location or region
- Outcome distribution: proportion of cases that are substantiated vs not substantiated
- Corrective action completion rate: percentage of recommended corrective actions completed on time
- Reporter communication rate: percentage of reporters who engaged in two-way communication
- Year-on-year comparison: all metrics compared against the prior year for trend analysis

### AI-Powered
- Emerging category detection: AI flags when a new category of reports is increasing at an unusual rate
- Resolution time benchmarking: compare resolution times against published industry benchmarks
- Program health score: composite score of the ethics program's overall health based on multiple indicators

---

## Data Model

```erDiagram
    ethics_analytics_snapshots {
        ulid id PK
        ulid company_id FK
        string metric_type
        string dimension
        string dimension_value
        decimal value
        date snapshot_date
        timestamps created_at_updated_at
    }
```

| Table | Purpose | Key Columns |
|---|---|---|
| `ethics_analytics_snapshots` | Pre-aggregated metrics | `id`, `company_id`, `metric_type`, `dimension`, `value`, `snapshot_date` |

Note: Analytics are computed from `incident_reports`, `ethics_cases`, and `policy_acknowledgments` via scheduled aggregation queries.

---

## Permissions

```
ethics.analytics.view
ethics.analytics.view-detailed
ethics.analytics.export
ethics.analytics.view-outcomes
ethics.analytics.view-policy-completion
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `EthicsAnalyticsDashboardPage`, `CategoryBreakdownPage`, `PolicyCompletionReportPage`
- **Widgets:** `ReportVolumeTrendWidget`, `ResolutionTimeWidget`, `OutcomeDistributionWidget`
- **Nav group:** Analytics

---

## Displaces

| Feature | FlowFlex | NAVEX | EthicsPoint | Vault Platform |
|---|---|---|---|---|
| Report volume trends | Yes | Yes | Yes | Yes |
| Category breakdown | Yes | Yes | Yes | Yes |
| Resolution time analytics | Yes | Yes | Yes | Yes |
| AI emerging category detection | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[incident-reports]] — source of report volume and category data
- [[case-management]] — source of resolution time and case status data
- [[resolution-outcomes]] — outcome distribution data
- [[policy-acknowledgments]] — policy completion rates
