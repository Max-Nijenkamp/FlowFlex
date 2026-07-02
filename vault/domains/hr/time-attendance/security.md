---
domain: hr
module: time-attendance
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Security — Time & Attendance

Planned. Authorization via Spatie permissions (not Policies) per [[../../../security/authn-authz]]. Tenant isolation per [[../../../security/tenancy-isolation]].

## Permissions

`hr.time.view-any` · `hr.time.view` · `hr.time.log-own` · `hr.time.submit-own` · `hr.time.approve` · `hr.time.manage`

## Authorization

- Every Filament artifact gates on `canAccess() = Auth::user()->can('hr.time.view-any') && BillingService::hasModule('hr.time')`.
- Own-data scope: employees log/view/submit only their own entries (`log-own`, `submit-own`).
- Approval requires `hr.time.approve`; **approver ≠ owner** is enforced and audited.
- Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia).

## Tenancy

All rows carry `company_id` (indexed); scoped via `CompanyScope`. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None.

## Related

- [[../../../security/encryption]]
- [[../../../security/authn-authz]]
- [[../../../security/tenancy-isolation]]
- [[_module]]
