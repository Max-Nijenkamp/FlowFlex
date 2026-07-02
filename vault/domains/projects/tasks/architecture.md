---
domain: projects
module: tasks
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `TaskResource` | Tasks | #1 CRUD | filters project/assignee/status/priority |
| Task view | Tasks | #2 detail | comments, sub-tasks, dependencies, time log, attachments |
| `MyTasksPage` | Tasks | #1-style custom page | cross-project, grouped by due date, own scope |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.tasks.view-any')
        && BillingService::hasModule('projects.tasks');
}
```

## Jobs & Scheduling

None (mention notifications ride the notifications queue via its service).

## Search & Realtime

`TaskMoved` broadcast for collaborative board sync ([[../../../architecture/websockets]]). Full-text search on task title/description is a candidate — see [[unknowns]].
