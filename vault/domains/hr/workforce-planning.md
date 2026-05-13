---
type: module
domain: HR & People
panel: hr
module-key: hr.workforce
status: planned
color: "#4ADE80"
---

# Workforce Planning

> Headcount planning, hire forecasts, open role pipeline, and budget vs actual tracking — aligning people strategy with business goals.

**Panel:** `hr`
**Module key:** `hr.workforce`

## What It Does

Workforce Planning gives HR and Finance a structured view of planned headcount vs current headcount, department by department. HR creates headcount plans for each planning period (typically annual with quarterly reviews). Each plan entry represents a role — either a planned hire, an open role, or a projected attrition. The plan is compared against actual headcount from Employee Profiles in real time. Planned hires that are approved convert to Recruitment requisitions automatically. Headcount plan data feeds into Finance budgets for cost forecasting.

## Features

### Core
- Headcount plans: create a plan for a period (annual, quarterly) per department with target headcount per role type
- Plan entries: each entry is a planned role — new hire, backfill, or planned attrition — with target start quarter and estimated salary
- Actual vs plan: real-time comparison of planned headcount vs current active headcount from Employee Profiles — gap view per department
- Open role pipeline: list of all approved plan entries not yet converted to a Recruitment requisition — with priority and estimated start date
- Budget estimate: planned headcount × estimated salary per role = projected payroll cost per department

### Advanced
- Scenario planning: create alternative headcount scenarios ("Base Case", "High Growth", "Restructure") — compare projected cost of each
- Requisition conversion: one-click convert an approved plan entry to a Job Requisition in the Recruitment module
- Finance integration: export headcount plan as input to Finance Budgets module — department payroll cost lines pre-populated
- Attrition modelling: HR sets expected attrition rate per department — plan adjusts required new hires accordingly
- Approval workflow: department heads submit headcount requests; HR and Finance approve; approved roles appear in open role pipeline

### AI-Powered
- Attrition prediction: AI model predicts which employees are at risk of leaving in the next 6 months — feeds into attrition planning to determine whether to pre-hire for at-risk roles
- Hiring velocity estimate: based on historical time-to-hire per role type, AI estimates when each planned hire will actually start — surfaces late-start risk on plan entries

## Data Model

```erDiagram
    headcount_plans {
        ulid id PK
        ulid company_id FK
        string name
        string period
        string status
        timestamps created_at/updated_at
    }

    headcount_plan_entries {
        ulid id PK
        ulid plan_id FK
        ulid company_id FK
        ulid department_id FK
        string job_title
        string entry_type
        string priority
        string target_quarter
        decimal estimated_salary
        string status
        ulid requisition_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `entry_type` | new_hire / backfill / attrition |
| `status` | planned / approved / converted / cancelled |
| `requisition_id` | Set when converted to Recruitment requisition |

## Permissions

- `hr.workforce.view`
- `hr.workforce.create-plan`
- `hr.workforce.approve-entries`
- `hr.workforce.convert-to-requisition`
- `hr.workforce.view-scenarios`

## Filament

- **Resource:** `HeadcountPlanResource`
- **Pages:** `ListHeadcountPlans`, `ViewHeadcountPlan` — actual vs plan table with gap indicators
- **Custom pages:** `HeadcountPlanComparisonPage` — scenario comparison view
- **Widgets:** `HeadcountGapWidget` — total open/unfilled planned roles on HR dashboard
- **Nav group:** Analytics (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Workday Workforce Planning | Strategic headcount planning |
| Anaplan | Workforce planning and modelling |
| ChartHop | Headcount planning and org design |
| Adaptive Insights | HR workforce planning |

## Related

- [[employee-profiles]]
- [[recruitment]]
- [[hr-analytics]]
- [[succession-planning]]
- [[dei-metrics]]
