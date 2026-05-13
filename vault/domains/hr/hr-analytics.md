---
type: module
domain: HR & People
panel: hr
module-key: hr.analytics
status: planned
color: "#4ADE80"
---

# HR Analytics

> Headcount trends, turnover rate, department breakdown, time-to-hire, and leave utilisation — a read-only analytics dashboard drawing from all HR modules.

**Panel:** `hr`
**Module key:** `hr.analytics`

## What It Does

HR Analytics is a read-only Filament custom page that aggregates data from Employee Profiles, Leave Management, Time & Attendance, Recruitment, and Payroll into a set of key workforce metrics. HR managers and executives use this page to understand headcount trends over time, monitor turnover rates, compare department sizes, and track leave utilisation. All charts are filterable by department and date range. No data is created or edited here — it is purely a reporting view.

## Features

### Core
- Headcount over time: line chart of active employee count by month — filterable by department
- Turnover rate: terminations ÷ average headcount for selected period — shown as percentage
- Department breakdown: donut chart of employee distribution across departments
- Leave utilisation: average leave days taken vs allocated per leave type — bar chart
- New hires vs terminations: side-by-side bar chart by month

### Advanced
- Absenteeism rate: unplanned absence days ÷ total working days — flagged when above company threshold
- Time-to-hire: average days from job requisition creation to offer acceptance — from Recruitment module data
- Payroll cost trend: monthly total payroll cost over trailing 12 months — from Payroll module
- Export: download any chart data as CSV
- Date range filter: trailing 30 days, 90 days, 12 months, or custom range

### AI-Powered
- Trend narratives: AI-generated one-paragraph summary of the most significant changes in the current period vs prior period ("Headcount grew 8% this quarter, driven by Engineering hires. Turnover increased in Sales — investigate further.")
- Anomaly highlights: automatically surface unexpected spikes (unusually high sick leave in one department, sudden termination cluster)

## Data Model

```erDiagram
    hr_analytics_cache {
        ulid id PK
        ulid company_id FK
        string metric_key
        string period
        json data
        timestamp calculated_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `metric_key` | e.g. `headcount_by_month`, `turnover_rate_q1` |
| `period` | ISO period string e.g. `2026-Q1`, `2026-05` |
| `data` | Pre-aggregated JSON for fast chart rendering |

## Permissions

- `hr.analytics.view`
- `hr.analytics.view-payroll-data`
- `hr.analytics.export`
- `hr.analytics.configure-thresholds`
- `hr.analytics.view-department`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `HrAnalyticsDashboardPage` — tabbed: Overview, Headcount, Leave, Payroll, Recruitment
- **Widgets:** `HeadcountTrendWidget`, `TurnoverRateWidget`, `DepartmentBreakdownWidget`
- **Nav group:** Analytics (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Workday People Analytics | HR workforce analytics |
| BambooHR Reports | HR reporting and metrics |
| HiBob Analytics | People data and HR analytics |
| Visier | Workforce intelligence platform |

## Related

- [[employee-profiles]]
- [[leave-management]]
- [[payroll]]
- [[recruitment]]
- [[dei-metrics]]
