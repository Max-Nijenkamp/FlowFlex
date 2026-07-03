---
domain: projects
module: tasks
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Tasks — Architecture

## State Machine

```
todo → in_progress → in_review → done | cancelled
         ↑______________↓ (reopen: done → in_progress)
```

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `todo` | `in_progress` / `cancelled` | assignee or `projects.tasks.update` | |
| `in_progress` | `in_review` / `todo` / `cancelled` | | |
| `in_review` | `done` / `in_progress` | | `done` stamps `completed_at`; milestone progress updated (same-domain call) |
| `done` | `in_progress` (reopen) | | clears `completed_at` |

Audited (status-only, lightweight *(assumed)*). See [[../../../architecture/patterns/states]].

> Done may be blocked while open `blocks` dependencies exist — **warn-not-block, configurable** *(assumed)*.

## Services & Actions

Actions (lorisleiva/laravel-actions):

- `CreateTaskAction` / `UpdateTaskAction`.
- `MoveTask::run(taskId, sectionId|status, order)` — board drags route here (single validation path).
- `AddDependencyAction` — cycle check via graph walk → `DependencyCycleException`.
- `CommentOnTaskAction` — parses @mentions → `NotificationService`.
- `CompleteTaskAction` — transition + `MilestoneProgress::for()` update.

## Events

- Fires broadcast-only `TaskMoved` (`ShouldBroadcast`) on `company.{id}.projects` — consumed by board/gantt/workload views for live sync. **Not** a cross-domain domain event.
- No `ShouldQueue` domain events in v1.

## Filament Artifacts

**Nav group:** Tasks

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `TaskResource` | #1 CRUD resource | tweaks: state-badge-column, view-page-tabs, relation-manager-timeline | list filters project/assignee/status/priority; view page (#2 detail) tabs: comments, sub-tasks, dependencies, time log, attachments — comments tab rendered as a timeline relation manager |
| `MyTasksPage` | #17 gallery/directory custom page *(assumed — no dedicated grouped-list kind)* | [[../../../architecture/patterns/page-blueprints#Gallery / Directory Grid]] | cross-project own-scope list grouped by due bucket (overdue / today / week / later); inline quick-complete; no `view-any` required |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.tasks.view-any') && BillingService::hasModule('projects.tasks')`
per [[../../../architecture/filament-patterns]] #1. `MyTasksPage` is a custom page and MUST state this explicitly —
Filament does not auto-gate custom pages; it gates on
`canAccess() = Auth::user()->can('projects.tasks.view') && BillingService::hasModule('projects.tasks')`
(own scope — no `view-any` required). Board/gantt surfaces that mutate tasks live in their own modules and route
writes through `MoveTask` / `UpdateTaskAction`.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Task CRUD (form, API), comment / sub-task / dependency writes | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Status transition (`todo → in_progress → in_review → done` / `cancelled`, reopen) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write per [[../../../architecture/patterns/states]] — `MoveTask` / `CompleteTaskAction` own the write |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None (mention notifications ride the notifications queue via its service).

## Search & Realtime

`TaskMoved` broadcast for collaborative board sync ([[../../../architecture/websockets]]). Full-text search on task title/description is a candidate — see [[unknowns]].
