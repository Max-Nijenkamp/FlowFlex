---
domain: hr
module: compensation-benefits
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Compensation & Benefits

> **Rebuild blueprint.** HR domain code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]. Nothing below is built, shipped, or tested — this spec is the intended target for the rebuild. `build-status: planned`.

Compensation bands, salary benchmarking, benefits enrollment, and comp review cycles. Intended to include an append-only salary history audit trail.

module-key: `hr.compensation` · panel: `hr` · nav group: **Payroll** · priority: `v1`

---

## Purpose

- Compensation bands: min/mid/max salary per job grade, per department.
- Compa-ratio: employee salary vs band midpoint.
- Benefits catalog + enrollment (health, pension, gym, lunch).
- Comp review cycle: bulk salary adjustment during annual review.
- Salary history: append-only trail (who/when/old→new/reason).

## Intended Behavior

- Every salary change is intended to write an `hr_salary_history` row and update the payroll profile atomically in one transaction.
- Money is stored as integer minor units (cents, `bigint`) and arithmetic goes through `brick/money` — never raw float. See [[../../../architecture/packages]].
- Salary amounts are intended to be encrypted at rest; the `salary_band` column exposes only a coarse band. See [[../../../security/encryption]].

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
