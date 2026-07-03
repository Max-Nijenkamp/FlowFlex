---
domain: hr
module: shift-scheduling
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Shift Scheduling — Security

Intended controls (not yet built). See [[_module]].

## Permissions

Prefix `hr.shifts`:

- `hr.shifts.view-any`
- `hr.shifts.view`
- `hr.shifts.create`
- `hr.shifts.update`
- `hr.shifts.publish`
- `hr.shifts.request-swap`
- `hr.shifts.accept-swap` *(assumed — recipient's accept action; decline is available to the same participant scope)*
- `hr.shifts.approve-swap`

## Authorization

Every Filament artifact gates via `canAccess()`:

```
Auth::user()->can('hr.shifts.view-any') && BillingService::hasModule('hr.shifts')
```

Custom pages (`ShiftSchedulePage`) must state this explicitly. Authz uses spatie/laravel-permission (teams = `company_id`), not Laravel policies — see [[../../../security/authn-authz]]. Public/portal surfaces use a guest or scoped-portal guard.

## Rate Limiting

| Action | Limiter | Category |
|---|---|---|
| Publish week → notify assigned employees | `panel-action` | comms |

Named limiter per [[../../../architecture/security]] and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Tenancy

Both tables carry `company_id` and are scoped by `BelongsToCompany` / `CompanyScope`. The queued `BlockShiftsOnLeaveListener` runs under `WithCompanyContext` to preserve tenant context off the request. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. No sensitive columns in this module — see [[../../../security/encryption]] for the encrypted-cast convention used elsewhere.

## Related

- [[../../../security/authn-authz]] · [[../../../security/encryption]] · [[../../../security/tenancy-isolation]]
- [[api]] · [[architecture]]
