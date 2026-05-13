---
type: module
domain: HR & People
panel: hr
module-key: hr.benefits
status: planned
color: "#4ADE80"
---

# Employee Benefits

> Benefits catalog, enrollment windows, employee coverage tracking, and provider management — all benefits visible in one place for HR and employees.

**Panel:** `hr`
**Module key:** `hr.benefits`

## What It Does

Employee Benefits gives HR a complete catalog of all company-offered benefit plans and tracks which employees are enrolled in each. HR creates benefit plans (health insurance, pension, gym membership, childcare vouchers, etc.) and opens enrollment periods. Employees enrol via the Self-Service portal during the enrollment window. HR sees enrollment rates per plan and total company cost. The module differs from Compensation & Benefits — that module focuses on pay grades and salary bands, while Employee Benefits focuses on non-salary benefits enrollment, coverage tracking, and provider management.

## Features

### Core
- Benefits catalog: HR creates benefit plans with name, category, provider name, and description
- Enrollment periods: HR opens an enrollment window with start and end dates — employees can change elections only during this window
- Employee enrollment: employee selects which plans they want to join via Self-Service; HR confirms enrollment
- Coverage tracking: active enrollments per employee with start date, coverage level, and next renewal date
- Enrollment summary: HR dashboard showing enrollment count and rate per benefit plan

### Advanced
- Provider management: each benefit plan linked to a provider record with contact info and contract renewal date — alert HR when contract is due for renewal
- Dependent tracking: employees can register dependents (spouse, children) on applicable plans (e.g. family health plan)
- Life events: outside of enrollment windows, employees can trigger a life event (marriage, new child) to update specific elections — subject to HR approval
- Benefits cost report: total employer cost per plan per month — number of enrolled employees × employer contribution per head
- Total compensation statement: employee-facing statement showing salary + benefits market value — reinforces retention value

### AI-Powered
- Enrollment nudge: AI identifies employees who have not enrolled in any plan (especially pension) and sends a targeted reminder explaining the financial benefit
- Plan utilisation analysis: if gym membership enrollment is high but claims are low (if claim data is available), AI flags potential over-provision and suggests renegotiating with provider

## Data Model

```erDiagram
    benefit_plans {
        ulid id PK
        ulid company_id FK
        string name
        string category
        string provider_name
        string provider_contact
        date contract_renewal_date
        decimal employer_contribution
        decimal employee_contribution
        timestamps created_at/updated_at
    }

    benefit_enrollments {
        ulid id PK
        ulid benefit_plan_id FK
        ulid employee_id FK
        ulid company_id FK
        string coverage_level
        date start_date
        date end_date
        string status
        json dependents
        timestamps created_at/updated_at
    }

    enrollment_periods {
        ulid id PK
        ulid company_id FK
        string name
        date open_date
        date close_date
        string status
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `coverage_level` | individual / couple / family |
| `benefit_enrollments.status` | active / cancelled / pending |
| `dependents` | JSON array of dependent records |

## Permissions

- `hr.benefits.view-catalog`
- `hr.benefits.enroll-self`
- `hr.benefits.manage-plans`
- `hr.benefits.manage-enrollment-periods`
- `hr.benefits.view-company-enrollment`

## Filament

- **Resource:** `BenefitPlanResource`, `EnrollmentPeriodResource`
- **Pages:** `ListBenefitPlans`, `ManageEnrollmentPeriod` (with enrollment summary table)
- **Custom pages:** None
- **Widgets:** `BenefitEnrollmentRateWidget` — enrollment rate per plan on HR dashboard
- **Nav group:** Payroll (hr panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Bswift | Benefits administration |
| Zenefits | Benefits enrollment and management |
| Rippling Benefits | Employee benefits management |
| BambooHR Benefits | Benefits tracking and enrollment |

## Related

- [[compensation-benefits]]
- [[employee-self-service]]
- [[employee-profiles]]
- [[payroll]]
