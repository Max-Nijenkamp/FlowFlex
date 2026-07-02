---
domain: projects
module: sprints
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Sprints — Architecture

## State Machine

```
planning → active → completed
```

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `planning` | `active` | `projects.sprints.manage` | throws `ActiveSprintExistsException` when the project already has an active sprint |
| `active` | `completed` | `projects.sprints.manage` | incomplete tasks moved per user choice; velocity recorded |

See [[../../../architecture/patterns/states]].

## Services & Actions

Interface→Service: `SprintServiceInterface` → `SprintService` ([[../../../architecture/patterns/interface-service]]).

- `start(string $sprintId)` — enforces one-active rule.
- `assignTask(AssignTaskData)` / `removeTask(...)` — task in at most one active sprint.
- `CompleteSprint::run(CompleteSprintData)` — action; moves incomplete tasks (backlog / next sprint), records velocity.
- `burndown(string $sprintId): array` — per-day remaining points from task completion timestamps.
- `velocity(string $projectId): array` — per-sprint completed points + rolling 3-sprint average.

## Events

None cross-domain. Burndown/velocity are computed read-side (no snapshot table *(assumed)*).

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `SprintResource` | Sprints | #1 CRUD | start/complete actions; retro form on view |
| `SprintBoardPage` | Sprints | #3 Kanban custom page | active-sprint board + backlog sidebar (drag in/out) |
| `BurndownChartWidget` | Sprints | #6 widget (apex) | on sprint view |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.sprints.view-any')
        && BillingService::hasModule('projects.sprints');
}
```

## Jobs & Scheduling

None.

## Search & Realtime

Sprint board reuses the Kanban broadcast pattern for live moves *(assumed)*. No search.
