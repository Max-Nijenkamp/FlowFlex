---
domain: hr
module: employee-self-service
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Security — Employee Self-Service

> Intended controls. Nothing built or tested (see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]]).

## Permissions

`hr.self-service.view` · `hr.self-service.update-own`

Intended to be granted to the `employee` role by default in `PermissionSeeder`. Every employee gets `view` by default.

## Authorization

Every artifact gates on:

```
canAccess() = Auth::user()->can('hr.self-service.view-any')
              && BillingService::hasModule('hr.self-service')
```

per filament-patterns #1 — custom pages state this explicitly. See [[../../../security/authn-authz]].

## Self-Scoped Access (critical)

The defining security property: **an employee sees and edits only their own record.** Every query adds `where('employee_id', $self->id)` / `whereBelongsTo(auth()->user()->employee)` as a **second isolation layer** on top of tenant `CompanyScope`. Employee A must never read employee B's profile, payslips, or leave via any self-service route — this is the primary test target.

## Tenancy Isolation

Standard tenant `CompanyScope` applies beneath the self-scope. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None owned by this module (`encrypted-fields: []`) — it reads sensitive fields (e.g. bank details, national_id) that are encrypted at their owning module. Sensitive fields render read-only here. See [[../../../security/encryption]].

## Related

- [[_module]] · [[api]] · [[unknowns]]
