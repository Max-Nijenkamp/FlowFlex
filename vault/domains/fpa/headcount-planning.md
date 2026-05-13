---
type: module
domain: Financial Planning & Analysis
panel: fpa
module-key: fpa.headcount
status: planned
color: "#4ADE80"
---

# Headcount Planning

> Headcount budget — open roles, personnel cost projections, hiring timeline, and workforce cost modelling.

**Panel:** `fpa`
**Module key:** `fpa.headcount`

---

## What It Does

Headcount Planning connects workforce planning with financial planning. FP&A and HR leaders jointly manage the headcount budget — the list of approved positions, their fully-loaded cost (salary, employer NI, benefits, equipment), and the planned hire date. The module projects the cumulative personnel cost impact month by month for the financial year and compares it against the people cost budget. When a role is filled in HR, the actual hire date and salary update the model automatically.

---

## Features

### Core
- Headcount budget: list of all planned and approved positions for the financial year
- Role details: department, level, location, planned hire date, budgeted salary, and fully-loaded cost multiplier
- Personnel cost projection: month-by-month cumulative cost based on hire dates and salaries
- Budget vs actual: compare budgeted headcount cost against actual payroll from the finance ledger
- Open role tracking: positions budgeted but not yet hired shown as open roles with target hire date
- Export: headcount plan export to CSV for board reporting

### Advanced
- Role prioritisation: rank open roles by business impact for hiring sequencing decisions
- Attrition modelling: factor in expected natural attrition rate for a more realistic cost projection
- Contractor vs FTE modelling: plan a mix of permanent employees and contractor engagements
- Location-based cost adjustments: adjust budgeted cost for roles planned in different geographies
- Scenario headcount: model the cost impact of different hiring paces (aggressive, conservative)

### AI-Powered
- Hiring lag adjustment: AI adjusts the month of first cost based on historical time-to-fill for each role type
- Cost overrun risk: flag if the current headcount plan would exhaust the people cost budget before year end
- Optimal hire sequencing: recommend the order to hire open roles for maximum revenue and productivity impact

---

## Data Model

```erDiagram
    headcount_plans {
        ulid id PK
        ulid company_id FK
        ulid budget_cycle_id FK
        string name
        integer financial_year
        string status
        timestamps created_at_updated_at
    }

    headcount_positions {
        ulid id PK
        ulid plan_id FK
        ulid company_id FK
        string job_title
        string department
        string location
        string employment_type
        decimal budgeted_salary
        decimal fully_loaded_multiplier
        date planned_start_date
        date actual_start_date
        decimal actual_salary
        boolean is_filled
        integer priority
        timestamps created_at_updated_at
    }

    headcount_plans ||--o{ headcount_positions : "contains"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `headcount_plans` | Plan containers | `id`, `company_id`, `budget_cycle_id`, `financial_year`, `status` |
| `headcount_positions` | Planned positions | `id`, `plan_id`, `job_title`, `department`, `budgeted_salary`, `fully_loaded_multiplier`, `planned_start_date`, `is_filled` |

---

## Permissions

```
fpa.headcount.view-own-department
fpa.headcount.view-all
fpa.headcount.manage-positions
fpa.headcount.approve
fpa.headcount.export
```

---

## Filament

- **Resource:** `App\Filament\Fpa\Resources\HeadcountPositionResource`
- **Pages:** `ListHeadcountPositions`, `CreateHeadcountPosition`, `EditHeadcountPosition`
- **Custom pages:** `HeadcountPlanPage`, `PersonnelCostProjectionPage`, `OpenRolesPage`
- **Widgets:** `TotalHeadcountCostWidget`, `OpenRolesWidget`, `HiringTimelineWidget`
- **Nav group:** Planning

---

## Displaces

| Feature | FlowFlex | Anaplan | Adaptive Insights | HiBob (HC planning) |
|---|---|---|---|---|
| Headcount budget positions | Yes | Yes | Yes | Yes |
| Personnel cost projection | Yes | Yes | Yes | No |
| Budget vs actual payroll | Yes | Yes | Yes | No |
| AI hiring lag adjustment | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[budget-planning]] — headcount budget is part of the overall people cost budget
- [[financial-forecasting]] — headcount costs feed the cost forecast
- [[variance-analysis]] — people cost variance tracked from headcount plan
- [[hr/INDEX]] — actual hire dates and salaries sync from HR records
