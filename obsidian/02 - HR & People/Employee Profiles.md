---
tags: [flowflex, domain/hr, employee-profiles, phase/2]
domain: HR & People
panel: hr
color: "#7C3AED"
status: complete
last_updated: 2026-05-07
---

# Employee Profiles

The central record for every person in the organisation. All other HR modules reference and extend this record.

**Who uses it:** HR team, managers, all employees (self-service view)
**Filament Panel:** `hr`
**Depends on:** Core (Auth, RBAC, Tenancy)
**Phase:** 2
**Build complexity:** Medium — 1 resource, 1 page, 5 tables

## Implementation (Phase 2 — Built)

**Filament Resources:**
- `DepartmentResource` — nav group: People, sort: 1
- `EmployeeResource` — nav group: People, sort: 2, with `DocumentsRelationManager`

**Models:** `Employee`, `Department`, `EmployeeDocument`, `EmployeeCustomField`, `EmployeeCustomFieldValue`
All use `HasUlids`, `SoftDeletes`, `BelongsToCompany`, `LogsActivity`.

**What's live:**
- Full employee form: personal details, employment details (type, status, department, manager, contracted hours), emergency contact
- Department select with company-scoped options (BelongsToCompany global scope)
- Employment type + status as backed enums with badge colours
- DocumentsRelationManager on employee edit page
- `Employee` model: `full_name` accessor, relationships to department, manager, direct reports, documents, leave balances, salary records, onboarding flows, payslips

**Permissions enforced:** `hr.employees.*`, `hr.departments.*`

**Not yet built (future phases):** self-service portal, org chart visualisation, profile photo upload UI, employee number auto-generation

## Events Fired

- `EmployeeProfileCreated`
- `EmployeeProfileUpdated`
- `EmployeeDepartmentChanged`
- `EmployeeRoleChanged`

## Events Consumed

- `CandidateHired` (from [[Recruitment & ATS]]) → automatically creates employee profile record

## Features

### Personal Information

- Name, date of birth, national ID / national insurance number (encrypted)
- Contact details (email, phone, home address)
- Profile photo
- Emergency contact details (name, relationship, phone)

### Employment Details

- Start date and employment type (full-time, part-time, contractor, zero-hours)
- Job title and department
- Location / office
- Contracted hours per week
- Probation end date
- Employment status: active / on leave / terminated

### Org Chart

- Reports to (manager link — creates org chart hierarchy)
- Direct reports (auto-populated from other employee records)
- Visual org chart from this profile

### Document Storage

- Contract upload and versioning
- Right-to-work documents
- Signed agreements
- Custom document categories

### Custom Fields

- Workspace-defined fields (the HR admin can add any field the company needs)
- Field types: text, number, date, dropdown, checkbox
- Field visibility rules (some custom fields may be restricted by role)

### Employee Number

- Auto-generated (sequential or pattern-based, configurable)
- Or manually assigned
- Unique within tenant

### Self-Service Portal

- Employees can update their own contact info, photo, emergency contacts
- Cannot edit their own employment details (HR-only)
- Can view their own pay, leave balance, training status

## Database Tables (5)

1. `employees` — core employee record
2. `employee_documents` — document storage with versioning
3. `employee_custom_fields` — dynamic field definitions per tenant
4. `employee_custom_field_values` — values per employee
5. `departments` — department structure

## Related

- [[HR Overview]]
- [[Onboarding]]
- [[Offboarding]]
- [[Leave Management]]
- [[Payroll]]
- [[Performance & Reviews]]
- [[Recruitment & ATS]]
- [[Scheduling & Shifts]]
- [[Benefits & Perks]]
- [[HR Compliance]]
