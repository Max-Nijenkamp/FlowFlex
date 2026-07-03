---
domain: projects
module: sprints
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Sprints

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SprintResource` | #1 CRUD resource | tweaks: state-badge-column (status machine), custom-header-actions (start / complete) | retro form on view page; start/complete require `projects.sprints.manage` |
| `SprintBoardPage` | #3 Kanban custom page | [[../../../architecture/patterns/page-blueprints#Kanban]] | active-sprint board + backlog sidebar (drag in/out); card moves route through projects.tasks `MoveTask`; Reverb broadcast (reuses the Kanban pattern) *(assumed)* |
| `BurndownChartWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | apex line chart on the sprint view; `canView()`-guarded; polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.sprints.view-any') && BillingService::hasModule('projects.sprints')`
per [[../../../architecture/filament-patterns]] #1. `SprintBoardPage` is a custom page and MUST state this
explicitly — Filament does not auto-gate custom pages. Board card mutations additionally require
`projects.tasks.update` (enforced inside `MoveTask`, which owns the write); the widget `canView()`-guards.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Sprint CRUD (form, API, retro) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Task assign / remove (backlog ↔ sprint) | Optimistic | unique `(sprint_id, task_id)` + service one-active-sprint check; `updated_at` stale-check ([[../../../architecture/patterns/optimistic-locking]]) |
| Sprint start / complete (status transition) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write per [[../../../architecture/patterns/states]] — serialises the one-active-sprint-per-project invariant (`ActiveSprintExistsException`) |
| Board card move (via projects.tasks `MoveTask`) | Optimistic | Delegates to projects.tasks — card moves on drop, action runs after, board re-renders from server on exception ([[../../../architecture/patterns/optimistic-locking]]) |
| Burndown / velocity read (`SprintService::burndown` / `velocity`) | n/a | Read-only derived computation — no writable state |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None.

## Search & Realtime

Sprint board reuses the Kanban broadcast pattern for live moves *(assumed)*. No search.
