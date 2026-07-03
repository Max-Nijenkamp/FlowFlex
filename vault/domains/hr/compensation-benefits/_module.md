---
domain: hr
module: compensation-benefits
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Compensation & Benefits

> **Rebuild blueprint.** HR domain code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing below is built, shipped, or tested — this spec is the intended target for the rebuild. `build-status: planned`.

Compensation bands, salary benchmarking, benefits enrollment, and comp review cycles. Intended to include an append-only salary history audit trail.

---

## Module-key

`hr.compensation`

**Priority:** v1  
**Panel:** hr  
**Permission prefix:** `hr.compensation`  
**Tables:** `hr_compensation_bands`, `hr_benefits`, `hr_employee_benefits`, `hr_salary_history`  
**Nav group:** Payroll

---

## Core Features

- Compensation bands: min/mid/max salary per job grade, per department — [[features/compensation-bands]]
- Compa-ratio: employee salary vs band midpoint (via `brick/money`, never float)
- Benefits catalog + enrollment (health, pension, gym, lunch) — [[features/benefits-catalog]], [[features/benefit-enrollment]]
- Comp review cycle: bulk salary adjustment during annual review
- Salary history: append-only trail (who/when/old→new/reason) — [[features/salary-history]]

**Intended behavior:**

- Every salary change writes an `hr_salary_history` row and updates the payroll profile atomically in one transaction.
- Money is stored as integer minor units (cents, `bigint`) and arithmetic goes through `brick/money` — never raw float. See [[../../../architecture/packages]].
- Salary amounts are encrypted at rest; the `salary_band` column exposes only a coarse band. See [[../../../security/encryption]].

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../employee-profiles/_module]] | bands/benefits attach to employees |
| Hard | [[../payroll/_module]] | salary lives on payroll profile; benefit costs feed payslip deductions |
| Hard | core.billing + core.rbac | module gating + permissions |

---

## Data Ownership

Owns tables `hr_compensation_bands`, `hr_benefits`, `hr_employee_benefits`, `hr_salary_history` ([[data-model]]) — all `company_id`-scoped. Salary changes also update the payroll profile (`hr_payroll_employees`, hr.payroll) atomically; no other domain's tables are written (cross-domain only via events — [[../../../security/data-ownership]]).

## Cross-Domain Edges

| Direction | Event | Counterpart | Effect |
|---|---|---|---|
| Fires | comp-change event *(assumed — not yet specified)* | hr.payroll | payroll reflects new salary/benefit cost at run time |
| Reads | (no event) | hr.profiles | employees/departments for bands, enrollments, compa-ratio |

> [!warning] UNVERIFIED
> Whether comp/benefit changes propagate to payroll via an event or by payroll reading the updated profile directly is not specified. Bands and benefit costs are otherwise read-only inputs.

---

## Notes in this module

- [[architecture]] — services, actions, salary-change flow
- [[data-model]] — 4 tables + ERD
- [[api]] — DTOs and service contract
- [[security]] — permissions, tenancy, encrypted fields
- [[unknowns]] — assumptions and open items

## Feature slices

- [[features/compensation-bands]]
- [[features/benefits-catalog]]
- [[features/benefit-enrollment]]
- [[features/salary-history]]

## Related

- [[../payroll/_module]]
- [[../employee-profiles/_module]]
- [[../../../glossary]]
- [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]

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

## Test Checklist

- [ ] Tenant isolation: company A cannot see or adjust company B bands, benefits, enrollments, or salary history
- [ ] Module gating: all artifacts hidden when `hr.compensation` inactive
- [ ] Salary display (history + salary columns) hidden without `hr.payroll.view-sensitive`
- [ ] `adjustSalary` updates payroll profile and appends exactly one `hr_salary_history` row in one transaction; concurrent adjust rejected via row lock
- [ ] `hr_salary_history` is append-only — no update/delete path exists
- [ ] Compa-ratio computed via `brick/money` (no float); `null` when no matching band
- [ ] Band constraint `min ≤ mid ≤ max` enforced; unique `(company_id, job_grade, department_id)`
- [ ] Benefit enroll honours unique-active `(employee_id, benefit_id)`; double-enroll rejected
- [ ] `amount_raw` stored encrypted (never plaintext salary in the DB)
