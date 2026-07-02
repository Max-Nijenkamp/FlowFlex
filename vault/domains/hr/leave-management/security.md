---
domain: hr
module: leave-management
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leave Management — Security

Authz model + tenancy for the module. Platform refs: [[../../../security/authn-authz]], [[../../../security/encryption]], [[../../../security/tenancy-isolation]].

## Permissions

`hr.leave.view-any` · `hr.leave.view` · `hr.leave.create` · `hr.leave.update` · `hr.leave.delete` · `hr.leave.approve` · `hr.leave.reject` · `hr.leave.manage-types`

Seeded in `PermissionSeeder`. Employees get `create` + `view` (own) via the self-service role; managers get `approve`/`reject`. RBAC via [[../../core/rbac/_module\|core.rbac]] (spatie/laravel-permission, teams = company_id).

## Authorization Contract

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('hr.leave.view-any') && BillingService::hasModule('hr.leave')
```

per [[../../../architecture/filament-patterns]] #1 — custom pages (calendar) state it explicitly. Approval/rejection table actions are individually permission-gated (`hr.leave.approve` / `hr.leave.reject`). The service enforces `CannotApproveOwnRequestException` so no approver can approve their own request even with the permission.

Public/portal surfaces (self-service submission) use a guest or scoped-portal guard (Vue + Inertia per ui-strategy), never the staff guard.

## Tenancy

All three tables carry `company_id` (not null, FK, indexed) + `BelongsToCompany`. Company A approvers must not see/approve company B requests — covered by the tenant-isolation test. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. No national ID, salary, IBAN, or other sensitive columns in this module. General platform policy: [[../../../security/encryption]].

## Related

- [[_module]]
- [[../../../security/authn-authz]]
- [[../../../security/encryption]]
