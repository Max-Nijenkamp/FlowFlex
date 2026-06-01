---
type: module
domain: HR & People
panel: hr
module-key: hr.compensation
status: planned
color: "#4ADE80"
---

# Compensation & Benefits

Compensation bands, salary benchmarking, benefits enrollment, and comp review cycles.

---

## Core Features

- Compensation bands: min/mid/max salary per job grade, per department
- Compa-ratio calculation: employee salary vs band midpoint
- Benefits catalog: define available benefits (health insurance, pension, gym, lunch)
- Benefits enrollment: employee selects benefits during onboarding or open enrollment
- Comp review cycle: HR adjusts salaries in bulk during annual review
- Pay band builder: set min/max per role, compare to current employee salaries

---

## Data Model

| Table | Key Columns |
|---|---|
| `hr_compensation_bands` | company_id, job_grade, department_id, min_salary, mid_salary, max_salary, currency |
| `hr_benefits` | company_id, name, type (insurance/pension/allowance), cost_per_month, employer_contribution |
| `hr_employee_benefits` | company_id, employee_id, benefit_id, enrolled_at, unenrolled_at |

---

## Filament

- `CompensationBandResource` — manage pay grades and salary ranges
- `BenefitResource` — define company benefits catalog
- `BenefitEnrollmentResource` — view and manage employee benefit enrollments

---

## Related

- [[domains/hr/payroll]]
- [[domains/hr/employee-profiles]]
