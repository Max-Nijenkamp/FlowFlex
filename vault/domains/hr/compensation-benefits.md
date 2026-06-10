---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.compensation
status: planned
priority: v1
depends-on: [hr.profiles, hr.payroll, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [money, encryption]
tables: [hr_compensation_bands, hr_benefits, hr_employee_benefits, hr_salary_history]
permission-prefix: hr.compensation
encrypted-fields: ["hr_salary_history.amount_raw"]
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Compensation & Benefits

Compensation bands, salary benchmarking, benefits enrollment, and comp review cycles. Includes the salary history audit trail per [[build/decisions/decision-2026-06-01-salary-history]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | bands/benefits attach to employees |
| Hard | [[domains/hr/payroll\|hr.payroll]] | salary lives on payroll profile; benefit costs feed payslip deductions |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Compensation bands: min/mid/max salary per job grade, per department
- Compa-ratio calculation: employee salary vs band midpoint (via `salary_band` derived column + decrypted reads over bounded sets — [[architecture/patterns/encryption]])
- Benefits catalog: define available benefits (health insurance, pension, gym, lunch)
- Benefits enrollment: employee selects benefits during onboarding or open enrollment
- Comp review cycle: HR adjusts salaries in bulk during annual review — every change writes `hr_salary_history`
- Pay band builder: set min/max per role, compare to current employee salaries
- Salary history: append-only trail (who changed, when, old → new, reason) per ADR

---

## Data Model

### hr_compensation_bands

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| job_grade | string | unique `(company_id, job_grade, department_id)` |
| department_id | ulid nullable FK | null = company-wide |
| min_salary_cents / mid_salary_cents / max_salary_cents | bigint | min ≤ mid ≤ max |
| currency | string(3) | |
| deleted_at | timestamp nullable | |

### hr_benefits

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| type | string | insurance / pension / allowance |
| cost_per_month_cents | bigint | employee cost |
| employer_contribution_cents | bigint | |
| deleted_at | timestamp nullable | |

### hr_employee_benefits

| Column | Type | Notes |
|---|---|---|
| id, company_id, employee_id FK, benefit_id FK | ulid | unique active `(employee_id, benefit_id)` where unenrolled_at null |
| enrolled_at | timestamp | |
| unenrolled_at | timestamp nullable | |

### hr_salary_history *(per ADR decision-2026-06-01-salary-history)*

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), employee_id FK | ulid | |
| 🔐 amount_raw | text | encrypted integer cents (new salary) |
| salary_band | string | derived coarse band |
| effective_date | date | |
| reason | string | hire / promotion / comp-review / correction |
| changed_by | ulid FK users | |
| created_at | timestamp | append-only — no update/delete |

---

## DTOs

### CreateBandData — job_grade (required), department_id (nullable), min/mid/max_salary_cents (required, min ≤ mid ≤ max — cross-field), currency
### AdjustSalaryData — employee_id, new_salary_cents (min:0), effective_date, reason (in set)
### EnrollBenefitData — employee_id, benefit_id (active, not already enrolled)

Message: "Band values must satisfy min ≤ mid ≤ max."

## Services & Actions

Interface→Service: `CompensationServiceInterface` → `CompensationService`.

- `adjustSalary(AdjustSalaryData $data): void` — updates payroll profile salary + appends history row (single transaction)
- `bulkAdjust(array<AdjustSalaryData> $rows): BulkResult` — comp review; per-row try/catch
- `compaRatio(string $employeeId): ?float` — null when no matching band
- `enroll(EnrollBenefitData $data)` / `unenroll(string $employeeBenefitId)` — benefit cost reflected in next payroll run *(assumed: payroll reads active enrollments at run time)*

---

## Filament

**Nav group:** Payroll

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CompensationBandResource` | #1 CRUD resource | band builder; band-vs-actual comparison column (band-level, not exact salaries) |
| `BenefitResource` | #1 CRUD resource | catalog |
| `BenefitEnrollmentResource` | #1 CRUD resource | enrollments, enroll/unenroll actions |
| `SalaryHistoryRelationManager` | on employee view | view-sensitive gated, read-only |

---

## Permissions

`hr.compensation.view-any` · `hr.compensation.manage-bands` · `hr.compensation.adjust-salary` · `hr.compensation.manage-benefits` · `hr.compensation.enroll` · (salary display additionally behind `hr.payroll.view-sensitive`)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Salary adjust writes history row + payroll profile atomically
- [ ] History is append-only (update/delete forbidden — arch/feature test)
- [ ] Band cross-field validation (min ≤ mid ≤ max)
- [ ] Compa-ratio computed via brick/money, null without band
- [ ] Double enrollment in same benefit rejected
- [ ] Salary amounts ciphertext in DB; band column coarse only

---

## Build Manifest

```
database/migrations/xxxx_create_hr_compensation_bands_table.php
database/migrations/xxxx_create_hr_benefits_table.php
database/migrations/xxxx_create_hr_employee_benefits_table.php
database/migrations/xxxx_create_hr_salary_history_table.php
app/Models/HR/{CompensationBand,Benefit,EmployeeBenefit,SalaryHistory}.php
app/Data/HR/{CreateBandData,AdjustSalaryData,EnrollBenefitData}.php
app/Contracts/HR/CompensationServiceInterface.php
app/Services/HR/CompensationService.php
app/Filament/HR/Resources/{CompensationBandResource,BenefitResource,BenefitEnrollmentResource}.php
database/factories/HR/{CompensationBandFactory,BenefitFactory,SalaryHistoryFactory}.php
tests/Feature/HR/{CompensationBandTest,SalaryHistoryTest,BenefitEnrollmentTest}.php
```

---

## Related

- [[domains/hr/payroll]]
- [[domains/hr/employee-profiles]]
- [[build/decisions/decision-2026-06-01-salary-history]]
- [[architecture/patterns/encryption]]
