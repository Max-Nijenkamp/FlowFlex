---
domain: hr
module: employee-profiles
feature: employee-record
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Employee Record

> Planned vertical slice. Back to [[../_module]].

## Purpose

The canonical employee record: personal info, contact details, emergency contacts, national ID, work email, plus employment details (hire date, job title, department, manager, employment type). Anchor record all other HR modules reference.

## Behavior

- Auto-generated per-company `employee_number` (sequential, unique).
- Work `email` unique per company; duplicate rejected with a clear message.
- `phone` normalised to E.164 via `propaganistas/laravel-phone`.
- Profile photo upload via Media Library.
- Sensitive fields (`national_id`, `date_of_birth`, `personal_email`) encrypted at rest (see [[../security]]).
- Emergency contacts stored in `hr_emergency_contacts`; hard-deleted on employee erasure (GDPR).
- Searchable in Meilisearch on name/email/title/number (never encrypted fields).

## UI

- **Kind**: simple-resource
- **Page**: "Employees" (`/hr/employees`)
- **Layout**: Filament table (name, employee_number, job_title, department, status filters) + create/edit form with tabs (Personal, Employment, Emergency Contacts).
- **Key interactions**: browse/search/filter the roster; create or edit an employee; open a row to the view page.
- **States**: empty = "No employees yet" with an Add Employee CTA · loading = table skeleton · error = inline validation (duplicate email, invalid E.164) · selected = row opens the view page.
- **Gating**: visible with `hr.employees.view`; create requires `hr.employees.create`; sensitive fields (national_id / DOB) require `hr.employees.view-sensitive`.

## Data

- Owns / writes: `hr_employees`, `hr_emergency_contacts`, `hr_departments` (all HasUlids + BelongsToCompany + SoftDeletes; only `EmployeeService` writes them).
- Reads: none cross-domain.
- Cross-domain writes: via events only (never another domain's tables — [[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: `EmployeeHired` → consumed by hr.onboarding (plan-generation), hr.org (hierarchy), Finance/Payroll, and IT provisioning *(P3, soft)*.
- Shared entity: reads `hr_departments` (owned by this module).

> [!warning] UNVERIFIED
> IT provisioning as an `EmployeeHired` consumer is a P3 soft integration and not confirmed by any built spec.

## Test Checklist

### Unit
- [ ] `employee_number` generator produces sequential, per-company-unique values
- [ ] `phone` normalises to E.164 via `propaganistas/laravel-phone`; invalid numbers rejected

### Feature (Pest)
- [ ] Hire assigns a unique `employee_number` per company even under concurrent creates (advisory lock)
- [ ] Duplicate work `email` within a company is rejected with a clear message
- [ ] `national_id` / `date_of_birth` / `personal_email` persist as ciphertext; `national_id_hash` lookup resolves the record
- [ ] Tenant isolation: company A cannot read/list company B employees

### Livewire
- [ ] Create form surfaces validation errors (duplicate email, invalid E.164) inline
- [ ] Sensitive fields (national_id / DOB) hidden without `hr.employees.view-sensitive`; `canAccess()` denies without `hr.employees.view` or inactive module

## Related

- Tables: `hr_employees`, `hr_emergency_contacts`, `hr_departments` — [[../data-model]]
- DTOs: `CreateEmployeeData`, `UpdateEmployeeData`, `EmployeeData` — [[../api]]
- Permissions: `hr.employees.create` / `.update` / `.view` / `.view-sensitive` — [[../security]]
- Events: `EmployeeHired` — [[../api]]
