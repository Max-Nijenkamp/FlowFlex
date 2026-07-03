---
domain: projects
module: milestones
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Milestones — Security

## Permissions

| Permission | Grants |
|---|---|
| `projects.milestones.view-any` | View milestones |
| `projects.milestones.create` | Create milestones + link tasks (`LinkTasksAction`) |
| `projects.milestones.update` | Edit milestones |
| `projects.milestones.delete` | Soft-delete a milestone |
| `projects.milestones.achieve` | `open → achieved` transition (`AchieveMilestoneAction`) |

**Verb-per-transition:** `open → achieved` = `projects.milestones.achieve`. The `open → missed` transition is
system-triggered by `MilestoneStatusCommand` (scheduled, no interactive actor) and therefore carries no user
permission. Seeded in `PermissionSeeder`.

## Rate Limiting

| Path | Limiter |
|---|---|
| Milestone reminder send (`MilestoneStatusCommand` → `NotificationService::notify`) | `panel-action` *(assumed — scheduled-job fit; see note)*. The `reminded_at` once-guard caps to one nudge per milestone, and delivery is additionally throttled by core.notifications' own send limiter. |

Comms leave this module only through the notifications service API; no direct outbound send here.

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
