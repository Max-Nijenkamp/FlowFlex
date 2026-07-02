---
domain: hr
module: employee-profiles
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Employee Profiles

> Build-status: **planned**. HR code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]; this spec is the rebuild blueprint. Nothing here is built, shipped, or tested yet.

## Purpose

Core employee record covering the full employment lifecycle: hire details, personal info, job position, department, manager, employment status, and termination. The anchor record that all other HR modules will reference — intended to be built first in `/hr`.

## Intended Behavior

- Will maintain the canonical employee record (personal info, contact details, emergency contacts, national ID, work email).
- Will track employment details: hire date, job title, department, self-referential manager, employment type.
- Will drive an employment-status state machine (`active → on_leave | terminated | suspended`).
- Will store employee documents and profile photos via Media Library.
- Will auto-generate sequential, per-company unique employee numbers.
- Will encrypt `national_id`, `date_of_birth`, `personal_email` at rest.
- Will index searchable name/email/title fields in Meilisearch (never encrypted fields).

## Dependency Summary

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../core/file-storage/_module\|core.files]] | documents + profile photo via Media Library |
| Soft | [[../org-chart/_module\|hr.org]] | renders manager hierarchy; without it, hierarchy exists data-only |
| Soft | [[../../core/data-import/_module\|core.import]] | bulk employee import; manual entry without it |

Fires: `EmployeeHired`, `EmployeeOffboarded` (see [[api]]). Consumes: none.

## Entity Notes

- [[architecture]] — services, actions, state machine
- [[data-model]] — tables, columns, relationships, ERD
- [[api]] — events, DTOs, service surface
- [[security]] — permissions, tenancy, encrypted fields
- [[unknowns]] — open questions and assumptions

### Features
- [[features/employee-record]]
- [[features/employment-lifecycle]]
- [[features/document-storage]]
- [[features/manager-hierarchy]]
- [[features/offboarding]]

## Related Siblings

- [[../org-chart/_module]]
- [[../leave-management/_module]]
- [[../payroll/_module]]
- [[../onboarding/_module]]

## Cross-References

- [[../../../architecture/event-bus]]
- [[../../../architecture/patterns/states]]
- [[../../../architecture/patterns/interface-service]]
- [[../../../architecture/patterns/belongs-to-company]]
- [[../../../infrastructure/search-meilisearch]]
- [[../../../glossary]]

## Cross-Domain Edges

| Direction | Event | Counterpart |
|---|---|---|
| Fires | `EmployeeHired` | hr.onboarding, hr.org, finance/payroll, IT provisioning *(P3, UNVERIFIED)* |
| Fires | `EmployeeOffboarded` | IT deprovisioning *(P3, UNVERIFIED)*, payroll final pay, hr.self-service |
| Consumes | — | none |

Owns `hr_employees`, `hr_departments`, `hr_emergency_contacts`; only `EmployeeService` writes them; other modules read via its API/events ([[../../../security/data-ownership]]).

## Intended File Layout (Build Manifest)

```
database/migrations/xxxx_create_hr_departments_table.php
database/migrations/xxxx_create_hr_employees_table.php
database/migrations/xxxx_create_hr_emergency_contacts_table.php
app/Models/HR/{Employee,Department,EmergencyContact}.php
app/States/HR/Employee/{EmployeeState,Active,OnLeave,Suspended,Terminated}.php
app/Data/HR/{CreateEmployeeData,UpdateEmployeeData,OffboardEmployeeData,EmployeeData}.php
app/Contracts/HR/EmployeeServiceInterface.php
app/Services/HR/EmployeeService.php
app/Providers/HR/HRServiceProvider.php
app/Exceptions/HR/ManagerCycleException.php
app/Events/HR/{EmployeeHired,EmployeeOffboarded}.php
app/Filament/HR/Resources/{EmployeeResource,DepartmentResource}.php
app/Filament/HR/Widgets/EmployeeProfileWidget.php
database/factories/HR/{EmployeeFactory,DepartmentFactory,EmergencyContactFactory}.php
tests/Feature/HR/{EmployeeTest,EmployeeOffboardTest,EmployeeEncryptionTest}.php
```

## Filament (intended)

**Nav group:** Employees

| Artifact | Kind (ui-strategy row) | Notes |
|---|---|---|
| `EmployeeResource` | #1 CRUD resource | searchable; filters: dept/status/type; export via pxlrbt/filament-excel |
| Employee view page | #2 detail with tabs | profile card + tabs: Personal, Employment, Documents (Media Library), History (activitylog) |
| `DepartmentResource` | #1 CRUD resource | tree via parent_department_id *(assumed: simple list v1)* |
| `OffboardAction` | modal action | termination form on view page |
| `EmployeeProfileWidget` | #6 widgets on list page | headcount, new hires this month, turnover rate |

Each artifact will gate via `canAccess()` (see [[security]]). Export action will be throttled per architecture/security.md.

## Test Checklist (intended)

- [ ] Tenant isolation: company A employees invisible to company B
- [ ] Module gating: resources hidden when `hr.profiles` inactive
- [ ] Hire fires `EmployeeHired` with contract payload
- [ ] Offboard transitions state + fires `EmployeeOffboarded`, requires reason + date
- [ ] Employee numbers sequential + unique per company under concurrent creates
- [ ] Manager cycle rejected (`ManagerCycleException`)
- [ ] Encrypted fields stored as ciphertext (raw DB check), hash lookup works
- [ ] `view-sensitive` permission gates national_id/DOB display
- [ ] Phone normalised to E.164
- [ ] Duplicate work email per company rejected with message
