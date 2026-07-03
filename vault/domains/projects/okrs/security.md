---
domain: projects
module: okrs
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# OKRs — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.okrs.view-any` | View all OKRs |
| `projects.okrs.create` | Create objectives + KRs |
| `projects.okrs.update-own` | Check in / edit own OKRs (the check-in command verb for own KRs) |
| `projects.okrs.update-any` | Check in / edit anyone's OKRs (the check-in command verb for others' KRs) |
| `projects.okrs.delete` | Soft-delete an objective |

Seeded in `PermissionSeeder`. The **check-in** command carries no dedicated verb — it maps to `update-own` (own KRs) / `update-any` (others'). Health is derived automatically, not a user-triggered transition, so it needs no permission. The `OkrCheckinReminderCommand` is a scheduled system command (not a panel action) and needs no rate limiter.

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.okrs.view-any')
        && BillingService::hasModule('projects.okrs');
}
```

## Invariants

- Hierarchy cycles + depth >4 rejected on create/reparent *(assumed depth)*.
- Check-in by a non-owner requires `projects.okrs.update-any`.

## Tenant Isolation

All three tables carry `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. The reminder command runs under `WithCompanyContext` per company. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.okrs')`.

## Encrypted Fields

None. (OKR data is goal metadata; not sensitive PII.)
