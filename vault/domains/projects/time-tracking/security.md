---
domain: projects
module: time-tracking
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Time Tracking — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.time.log-own` | Log/edit own time entries |
| `projects.time.view-any` | View all team entries |
| `projects.time.approve` | Approve a week of entries |
| `projects.time.export` | Export entries to CSV |

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.time.view-any')
        && BillingService::hasModule('projects.time');
}
```

Own-data scope: without `view-any`, a user only sees/logs their own entries.

## Invariants

- One running timer per user (`TimerAlreadyRunningException`).
- Approver ≠ entry owner.
- Entries cannot be future-dated.

## Rate Limiting

- CSV export endpoint is throttled per user/company. See [[../../../architecture/security]].

## Tenant Isolation

`proj_time_entries` carries `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.time')`.

## Encrypted Fields

None.
