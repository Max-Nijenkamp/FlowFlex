---
type: module
domain: HR & People
panel: hr
module-key: hr.workforce
status: planned
color: "#4ADE80"
---

# Workforce Planning

Headcount planning, hire forecasts, and open role pipeline. Plan future team structure against budget and growth targets.

## Core Features

- Headcount plan: target headcount per department per period (quarter/year)
- Planned vs actual headcount tracking
- Hire forecast: planned new roles with target start dates and budgeted cost
- Open role pipeline: roles approved but not yet filled (links to Recruitment)
- Attrition forecast: expected departures factored into net headcount
- Budget impact: planned headcount × average salary vs department budget
- Scenario planning: best/expected/worst-case growth
- Org growth visualisation over time

## Data Model

| Table | Key Columns |
|---|---|
| `hr_headcount_plans` | company_id, department_id, period, target_headcount, budgeted_cost_cents |
| `hr_planned_roles` | company_id, plan_id, title, target_start_date, budgeted_salary_cents, status (planned/approved/filled) |

```mermaid
erDiagram
    hr_headcount_plans {
        ulid id PK
        ulid company_id FK
        ulid department_id FK
        string period
        int target_headcount
        int budgeted_cost_cents
    }
    hr_planned_roles {
        ulid id PK
        ulid plan_id FK
        ulid company_id FK
        string title
        date target_start_date
        int budgeted_salary_cents
        string status
    }
    hr_headcount_plans ||--o{ hr_planned_roles : "plans"
```

## Filament

**Nav group:** Analytics

- `HeadcountPlanResource` — plan headcount per department/period
- `PlannedRoleResource` — manage open role pipeline
- `WorkforcePlanningDashboard` (custom page) — planned vs actual headcount charts

## Cross-Domain

- Planned roles feed [[domains/hr/recruitment]] requisitions
- Budget check against [[domains/finance/budgets]]

## Related

- [[domains/hr/recruitment]]
- [[domains/hr/hr-analytics]]
- [[domains/finance/budgets]]
