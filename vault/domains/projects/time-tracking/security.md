---
domain: projects
module: time-tracking
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Time Tracking — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.time.log-own` | Log/edit own time entries |
| `projects.time.view-any` | View all team entries |
| `projects.time.approve` | Approve a week of entries |
| `projects.time.export` | Export entries to CSV |

**Verb-per-command:** timer start/stop is authorized by `projects.time.log-own` (running your own timer is logging your own time — no separate verb). Week approval is the only status-changing command and carries `projects.time.approve` (approver ≠ owner enforced in `ApproveWeekAction`). CSV export carries `projects.time.export` and the named `exports` rate limiter (see Rate Limiting). v1 models approval as one-directional stamp — no `submit` / `reject` verbs (see [[unknowns]]).

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

- CSV export (`ProjectTimeReportPage` / `TimeEntryResource` bulk action) is throttled by the named **`exports`** rate limiter, scoped per user/company. See [[../../../architecture/security]].

## Tenant Isolation

`proj_time_entries` carries `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.time')`.

## Encrypted Fields

None.
