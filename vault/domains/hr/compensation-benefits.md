---
type: module
domain: HR & People
panel: hr
module-key: hr.compensation
status: planned
color: "#4ADE80"
---

# Compensation & Benefits

> Compensation bands, salary benchmarking, pay grade assignment, and benefits enrollment — structured pay management from band definition to individual offer.

**Panel:** `hr`
**Module key:** `hr.compensation`

## What It Does

Compensation & Benefits manages the pay framework that underpins every employee's salary. HR defines pay grades and compensation bands (salary ranges per level per department). Each employee is assigned a pay grade, and their salary is recorded against that band so HR can see who is above, within, or below range. Market benchmarking data can be imported for comparison. Benefits enrollment tracks which benefit plans each employee is enrolled in. This module feeds into Payroll (salary input) and DEI Metrics (pay equity analysis).

## Features

### Core
- Pay grades: create named grades (e.g. L1, L2, Senior, Staff) with department and location scope
- Compensation bands: min, midpoint, and max salary per pay grade per currency
- Employee pay assignment: record each employee's current salary, pay grade, and effective date — full salary history retained
- Compa-ratio: employee salary ÷ band midpoint — shown on employee profile and in band analysis view
- Benefits catalog: define available benefit plans (health, dental, pension, gym, childcare)

### Advanced
- Salary change requests: manager submits a pay change request with justification; HR reviews and approves — triggers update to employee pay record
- Market data import: upload external salary benchmark data (e.g. from Radford, Mercer) as CSV; compare band midpoints to market P50/P75
- Band analysis view: scatter plot of all employee salaries within a grade — shows distribution vs band min/max
- Benefits enrollment: employees enrol via Self-Service; enrollment period open/close dates configurable
- Total compensation statement: PDF showing employee's salary + benefits + employer contributions per year

### AI-Powered
- Pay equity scan: automatically flag statistically significant pay gaps across gender or ethnicity within the same pay grade — surfaced in DEI Metrics module
- Band recalibration suggestions: when market benchmark data is updated, AI recommends which bands need adjustment to remain competitive

## Data Model

```erDiagram
    pay_grades {
        ulid id PK
        ulid company_id FK
        string name
        string department
        string location
        timestamps created_at/updated_at
    }

    compensation_bands {
        ulid id PK
        ulid pay_grade_id FK
        ulid company_id FK
        string currency
        decimal min_salary
        decimal midpoint_salary
        decimal max_salary
        date effective_from
        timestamps created_at/updated_at
    }

    employee_compensation {
        ulid id PK
        ulid employee_id FK
        ulid company_id FK
        ulid pay_grade_id FK
        decimal salary
        string currency
        date effective_date
        string change_reason
        ulid approved_by FK
        timestamps created_at/updated_at
    }

    benefit_plans {
        ulid id PK
        ulid company_id FK
        string name
        string category
        text description
        decimal employer_contribution
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `employee_compensation` | One row per salary change — history table |
| `compa_ratio` | Computed: salary ÷ midpoint_salary (not stored) |
| `benefit_plans.category` | health / dental / pension / gym / other |

## Permissions

- `hr.compensation.view-bands`
- `hr.compensation.view-salaries`
- `hr.compensation.manage-bands`
- `hr.compensation.approve-changes`
- `hr.compensation.view-benchmarks`

## Filament

- **Resource:** `PayGradeResource`, `CompensationBandResource`, `BenefitPlanResource`
- **Pages:** `ListPayGrades`, `BandAnalysisPage` — scatter plot view of salaries vs bands
- **Custom pages:** `BandAnalysisPage`
- **Widgets:** `CompaRatioDistributionWidget` — histogram of compa-ratios across company
- **Nav group:** Payroll (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Radford / Mercer | Compensation benchmarking |
| Workday Compensation | Pay grade and band management |
| HiBob Compensation | Salary management and benchmarking |
| Compa | Compensation management platform |

## Related

- [[payroll]]
- [[employee-profiles]]
- [[dei-metrics]]
- [[employee-benefits]]
- [[succession-planning]]
