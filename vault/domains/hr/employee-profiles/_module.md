---
domain: hr
module: employee-profiles
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Employee Profiles

Core employee record covering the full employment lifecycle: hire details, personal info, job position, department, manager, employment status, and termination. The anchor record that all other HR modules reference — intended to be built first in `/hr`.

> Build-status: **planned**. HR code was stripped per [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]; this spec is the rebuild blueprint. Nothing here is built, shipped, or tested yet.

---

## Module-key

`hr.profiles`

**Priority:** v1-core
**Panel:** hr
**Permission prefix:** `hr.employees`
**Tables:** `hr_employees`, `hr_departments`, `hr_emergency_contacts`
**Nav group:** Employees

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../core/file-storage/_module\|core.files]] | documents + profile photo via Media Library |
| Soft | [[../org-chart/_module\|hr.org]] | renders manager hierarchy; without it, hierarchy exists data-only |
| Soft | [[../../core/data-import/_module\|core.import]] | bulk employee import; manual entry without it |

Fires: `EmployeeHired`, `EmployeeOffboarded` (see [[api]]). Consumes: none.

---

## Core Features

- Canonical employee record: personal info, contact details, emergency contacts, national ID, work email — [[features/employee-record|Employee Record]]
- Employment details: hire date, job title, department, self-referential manager, employment type
- Employment-status state machine (`active → on_leave | suspended | terminated`) — [[features/employment-lifecycle|Employment Lifecycle]]
- Employee documents and profile photos via Media Library — [[features/document-storage|Document Storage]]
- Self-referential manager hierarchy (direct reports, upward chain for approval routing) — [[features/manager-hierarchy|Manager Hierarchy]]
- Offboarding: termination date, reason, downstream signals — [[features/offboarding|Offboarding]]
- Auto-generated sequential, per-company unique employee numbers
- Encryption of `national_id`, `date_of_birth`, `personal_email` at rest
- Meilisearch indexing of searchable name/email/title fields (never encrypted fields)

---

## Build Manifest

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

Filament artifacts (resources, offboard action, widget) and per-write-path concurrency tiers: [[architecture]].

---

## Test Checklist

- [ ] Tenant isolation: company A employees invisible to company B
- [ ] Module gating: artifacts hidden when `hr.profiles` inactive
- [ ] Hire fires `EmployeeHired` with contract payload
- [ ] Offboard transitions state + fires `EmployeeOffboarded`, requires reason + date
- [ ] Employee numbers sequential + unique per company under concurrent creates
- [ ] Manager cycle rejected (`ManagerCycleException`)
- [ ] Encrypted fields stored as ciphertext (raw DB check), hash lookup works
- [ ] `view-sensitive` permission gates national_id/DOB display
- [ ] Phone normalised to E.164
- [ ] Duplicate work email per company rejected with message

---

## Cross-Domain Edges

| Direction | Event | Counterpart |
|---|---|---|
| Fires | `EmployeeHired` | hr.onboarding, hr.org, finance/payroll, IT provisioning *(P3, UNVERIFIED)* |
| Fires | `EmployeeOffboarded` | IT deprovisioning *(P3, UNVERIFIED)*, payroll final pay, hr.self-service |
| Consumes | — | none |

Owns `hr_employees`, `hr_departments`, `hr_emergency_contacts`; only `EmployeeService` writes them; other modules read via its API/events ([[../../../security/data-ownership]]).

---

## Related

- Entity notes: [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[unknowns]]
- Sibling modules: [[../org-chart/_module]] · [[../leave-management/_module]] · [[../payroll/_module]] · [[../onboarding/_module]]
- [[../../../architecture/event-bus]]
- [[../../../architecture/patterns/states]]
- [[../../../architecture/patterns/interface-service]]
- [[../../../architecture/patterns/belongs-to-company]]
- [[../../../infrastructure/search-meilisearch]]
- [[../../../glossary]]
