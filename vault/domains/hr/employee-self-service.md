---
type: module
domain: HR & People
domain-key: hr
panel: hr
module-key: hr.self-service
status: complete
priority: v1
depends-on: [hr.profiles, core.billing, core.rbac]
soft-depends: [hr.leave, hr.payroll, hr.onboarding]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: hr.self-service
encrypted-fields: []
last-reviewed: 2026-06-12
color: "#4ADE80"
---

# Employee Self-Service

Employee-facing portal for viewing personal info, submitting leave requests, downloading payslips, and completing onboarding tasks. Lives inside the `/hr` panel, scoped to the logged-in employee's own data only.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | `Auth::user()->employee` link |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/hr/leave-management\|hr.leave]] | leave tile + submission; tile hidden without it |
| Soft | [[domains/hr/payroll\|hr.payroll]] | payslip downloads; tile hidden without it |
| Soft | [[domains/hr/onboarding\|hr.onboarding]] | task completion; tile hidden without it |

---

## Core Features

- Personal info view/edit: name, address, emergency contacts, bank details (read-only sensitive fields)
- Leave: submit leave requests, view balance, request history
- Payslips: download historical payslips PDF
- Onboarding tasks: complete assigned tasks from the onboarding plan
- Documents: view and download personal documents (contract, payslips, certifications)
- Profile photo upload
- Permission: `hr.self-service.view` — every employee gets this by default

---

## Data Model

No additional tables — reads from existing HR tables scoped to `Auth::user()->employee`.

## DTOs

### UpdateOwnProfileData (input)
| Field | Type | Validation |
|---|---|---|
| phone | ?string | nullable, phone:AUTO |
| personal_email | ?string | nullable, email |
| emergency_contacts | array<{name, relationship, phone, email?}> | max 3 *(assumed)* |

Employees may NOT edit: name, email, job, salary, department, manager, national_id — HR-only fields.

## Services & Actions

- `UpdateOwnProfileAction::run(UpdateOwnProfileData $data): void` — operates strictly on `auth()->user()->employee`
- **Own-data rule**: every query in this module adds `whereBelongsTo(auth()->user()->employee)` / `where('employee_id', $self->id)` ON TOP of CompanyScope — second-layer isolation

---

## Filament

**Nav group:** (top-level "My HR", visible to all authenticated `/hr` users)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SelfServiceDashboardPage` | #6 dashboard custom page | tiles: leave balance, next payslip, pending tasks — soft-dep tiles conditional on `hasModule` |
| `MyProfilePage` | #7 custom page (form) | own-profile edit + photo + emergency contacts |
| `MyDocumentsPage` | #1-style list (own scope) | personal docs from Media Library |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('hr.self-service.view-any') && BillingService::hasModule('hr.self-service')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`hr.self-service.view` · `hr.self-service.update-own`

Granted to `employee` role by default in PermissionSeeder.

---

## Test Checklist

- [ ] **Own-data isolation: employee A cannot read employee B's profile/payslips/leave via any self-service route** (the critical test)
- [ ] Tenant isolation on top (cross-company)
- [ ] Module gating: dashboard tiles hide when soft-dep modules inactive
- [ ] Employee cannot edit HR-only fields (job_title etc. rejected)
- [ ] User without linked employee record sees friendly empty state *(assumed)*
- [ ] Payslip download streams own payslip only

---

## Build Manifest

```
app/Data/HR/UpdateOwnProfileData.php
app/Actions/HR/UpdateOwnProfileAction.php
app/Filament/HR/Pages/{SelfServiceDashboardPage,MyProfilePage,MyDocumentsPage}.php
resources/views/filament/hr/pages/{self-service-dashboard,my-profile,my-documents}.blade.php
tests/Feature/HR/{SelfServiceIsolationTest,SelfServiceProfileTest}.php
```

---

## Implementation Notes

### 2026-06-12 build sync
- My HR dashboard restyled: greeting header + icon stat cards inside Sections (same data contract, deferred load kept)

## Related

- [[domains/hr/leave-management]]
- [[domains/hr/payroll]]
- [[domains/hr/onboarding]]
- [[domains/hr/employee-profiles]]
