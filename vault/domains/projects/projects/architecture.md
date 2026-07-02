---
domain: projects
module: projects
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Projects — Architecture

## State Machine

```
planning → active → on_hold → completed | cancelled
              └──────────────→ completed | cancelled
```

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `planning` | `active` | `projects.projects.update` | |
| `active` | `on_hold` / `completed` / `cancelled` | `projects.projects.update` (complete requires all tasks done or explicit confirm *(assumed: confirm modal)*) | `completed` stamps `completed_at` |
| `on_hold` | `active` / `cancelled` | | |

Audited (spatie/laravel-model-states). See [[../../../architecture/patterns/states]].

## Services & Actions

Interface→Service: `ProjectServiceInterface` → `ProjectService` ([[../../../architecture/patterns/interface-service]]).

| Method | Responsibility |
|---|---|
| `create(CreateProjectData): ProjectData` | creator auto-added as `owner` member |
| `health(string $projectId): string` | completion vs elapsed math (on-track/at-risk/off-track) |
| `actuals(string $projectId): array{hours, cost_cents}` | from time entries; 0 when time module inactive |
| `addMember / removeMember` | membership management |

## Events

None fired or consumed in v1. Health/actuals are read via same-domain and soft-dep read APIs, not events.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `ProjectResource` | Projects | #1 CRUD | member-scoped listing; status filters |
| Project view page | Projects | #2 detail w/ tabs | Overview, Tasks, Sprints, Milestones, Files, Time (soft-dep tabs conditional) |
| `ProjectStatsWidget` | Projects | #6 widget | active count, health pie |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.projects.view-any')
        && BillingService::hasModule('projects.projects');
}
```

## Jobs & Scheduling

None.

## Search & Realtime

None specified.
