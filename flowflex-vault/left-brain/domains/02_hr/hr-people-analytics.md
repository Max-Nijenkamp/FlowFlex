---
type: module
domain: HR & People
panel: hr
cssclasses: domain-hr
phase: 2
status: complete
migration_range: 100000–149999
last_updated: 2026-05-12
---

# HR & People Analytics

Real-time workforce metrics for HR managers. Headcount trend, attrition, time-to-hire, absence rate, cost-per-hire, diversity metrics. Available from Phase 2 — HR managers need these metrics as soon as they have employees, not in Phase 6.

**Panel:** `hr`  
**Phase:** 2 — HR-scoped analytics, distinct from the full Analytics domain (Phase 6)

> Note: Phase 6 Analytics domain provides cross-domain report builder and data warehouse. This module is the HR panel's own analytics section — pre-built, domain-specific.

---

## Features

### Headcount Dashboard
- Current headcount (total, by department, by location, by employment type)
- Headcount trend (chart: last 12 months)
- New hires this month / this quarter
- Leavers this month / this quarter
- Net headcount change
- Headcount plan vs actual (if workforce planning targets set)
- Contractor vs permanent ratio

### Attrition & Retention
- Attrition rate: voluntary vs involuntary, by department, by tenure band
- Retention rate: % of employees who stayed vs started date cohort
- Average tenure (total, by role, by department)
- Exit interview reasons (categorised — top 5 reasons for leaving)
- Flight risk indicator (if Employee Feedback module active: burnout signal → flag)
- Regrettable vs non-regrettable turnover split

### Recruitment Metrics (when Recruitment ATS active)
- Open positions: count, days open
- Time-to-fill: average days from job posted to offer accepted
- Time-to-hire: average days from application to offer accepted
- Cost-per-hire: (recruiter cost + job board cost + time) ÷ hires
- Source of hire: which channel brings best candidates
- Offer acceptance rate
- Funnel: applications → screened → interviews → offers → accepted

### Absence & Leave Analytics
- Absence rate: % of scheduled days missed (industry benchmark: ~3-4%)
- Bradford Factor: measure of frequent short absences (formula: S² × D where S=separate absences, D=total days)
- Leave balance utilisation: % of annual leave used to date (flag employees burning out — not taking leave)
- Leave patterns by department, season

### Payroll & Compensation
- Total payroll cost per month, trend
- Average salary by department, role, tenure
- Gender pay gap (mean and median)
- Overtime hours and cost trend
- Benefits cost per employee

### Diversity & Inclusion (when DEI module active)
- Gender distribution by level
- Age distribution
- Nationality/ethnicity distribution (where self-declared)
- Promotion rate by demographic
- Pay equity analysis

---

## Data Model

No new tables — this module reads HR tables with pre-aggregated queries. Key: metrics are pre-computed nightly and stored in:

```erDiagram
    hr_metric_snapshots {
        ulid id PK
        ulid company_id FK
        date snapshot_date
        string metric_key
        decimal metric_value
        json breakdown
        timestamp calculated_at
    }
```

---

## Permissions

```
hr.analytics.view-headcount
hr.analytics.view-attrition
hr.analytics.view-recruitment
hr.analytics.view-payroll-cost
hr.analytics.view-dei
hr.analytics.export
```

---

## Related

- [[MOC_HR]]
- [[entity-employee]]
- [[MOC_Analytics]] — Phase 6 full analytics builds on same data, adds custom report builder
