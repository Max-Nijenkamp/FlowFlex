---
domain: projects
module: milestones
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Milestones — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.milestones.view-any` | View milestones |
| `projects.milestones.create` | Create milestones + link tasks |
| `projects.milestones.update` | Edit milestones |
| `projects.milestones.achieve` | Mark a milestone achieved |

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.milestones.view-any')
        && BillingService::hasModule('projects.milestones');
}
```

## Invariants

- Linked tasks must belong to the milestone's project (`LinkTasksAction` same-project check).
- 7-day reminder fires once (`reminded_at` guard) — idempotent under re-run.

## Tenant Isolation

Both tables carry `company_id` via `BelongsToCompany`; `CompanyScope` constrains queries. The scheduled command runs under `WithCompanyContext` per company. See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('projects.milestones')`.

## Encrypted Fields

None.
