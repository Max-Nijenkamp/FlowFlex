---
domain: hr
module: compensation-benefits
feature: benefit-enrollment
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature — Benefit Enrollment

## Purpose

Employees select benefits during onboarding or open enrollment; HR manages enrollments.

## Behavior (intended)

- `enroll(EnrollBenefitData)` — benefit must be active and not already enrolled; double enrollment in the same benefit is rejected (unique active `(employee_id, benefit_id)` where `unenrolled_at` null).
- `unenroll(employeeBenefitId)` — sets `unenrolled_at`.
- Benefit cost is reflected in the next payroll run *(assumed: payroll reads active enrollments at run time)*.
- Managed via `BenefitEnrollmentResource` (CRUD) with enroll/unenroll actions.

## Tables

- `hr_employee_benefits` (references `hr_benefits`)

## Permissions

- `hr.compensation.view-any`, `hr.compensation.enroll`

## UI

- **Kind**: simple-resource
- **Page**: "Benefit Enrollments" (`/hr/benefit-enrollments`)
- **Layout**: Filament table (employee, benefit, enrolled_at, unenrolled badge) with enroll/unenroll row actions; an enroll form pickers active employee + active benefit
- **Key interactions**: HR enrolls an employee in an active benefit and unenrolls (sets `unenrolled_at`); double active enrollment rejected
- **States**: empty (no enrollments → prompt to enroll) · loading (table skeleton) · error (already-enrolled / inactive-benefit validation) · selected (row with unenroll action)
- **Gating**: visible with `hr.compensation.view-any`; enroll/unenroll requires `hr.compensation.enroll`

## Data

- Owns / writes: `hr_employee_benefits`
- Reads: `hr_benefits` (active catalog) — own module; `hr_employees` via `EmployeeService` (hr.profiles)
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]])

## Relations

- Consumes: none
- Feeds: none — active enrollments read by payroll at run time *(assumed: payroll pulls costs, no event)*
- Shared entity: `hr_employees` (hr.profiles), `hr_benefits` ([[benefits-catalog]])

## Related

- [[../_module]]
- [[benefits-catalog]]
- [[../data-model]]
