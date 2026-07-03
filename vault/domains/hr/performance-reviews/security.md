---
domain: hr
module: performance-reviews
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Performance Reviews — Security

Intended controls. Nothing built yet. See [[_module]].

## Permissions

`hr.performance.view-any` · `hr.performance.view` · `hr.performance.submit` · `hr.performance.manage-cycles` · `hr.performance.calibrate`

Prefix: `hr.performance`. RBAC via spatie/laravel-permission (teams = company_id). See [[../../../security/authn-authz]].

## Authorization

Every Filament artifact gates on:

```
canAccess() = Auth::user()->can('hr.performance.view-any')
              && BillingService::hasModule('hr.performance')
```

Custom pages state this explicitly. Public/portal surfaces use a guest or scoped-portal guard.

## Rate Limiting

| Action | Limiter | Category |
|---|---|---|
| Finalise cycle → `GenerateReviewReportPdfJob` per employee | `exports` | file generation |

`ReviewDueReminderCommand` sends reminders via `core.notifications` (scheduled command, not a user-triggered action — no panel limiter). Named limiters per [[../../../architecture/security]] and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Visibility (reviewer / reviewee / manager / HR)

| Role | Sees |
|---|---|
| Reviewer | own assigned reviews; can only submit their own (`NotYourReviewException` otherwise) |
| Reviewee (employee) | own results **only after the cycle is finalised**; own goals; never the peer reviewer's identity *(assumed)* |
| Manager | reviews for their direct reports |
| HR | all reviews; performs calibration |

## Tenancy

All three tables carry `company_id` (indexed); global `CompanyScope` enforces isolation. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. `encrypted-fields: []`. See [[../../../security/encryption]] for the platform standard (not applied here).

## Related

- [[../../../security/authn-authz]]
- [[../../../security/encryption]]
- [[../../../security/tenancy-isolation]]
- [[_module]]
