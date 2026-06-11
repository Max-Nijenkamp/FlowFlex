---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.kanban
status: planned
priority: p2
depends-on: [projects.tasks, core.billing, core.rbac]
soft-depends: [projects.sprints]
fires-events: []
consumes-events: []
patterns: [custom-pages, websockets]
tables: []
permission-prefix: projects.kanban
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Kanban Board

Visual Kanban board with task cards grouped by section/status. Drag-and-drop cards between columns. Primary view for sprint work and day-to-day task management. Pure view module — owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/tasks\|projects.tasks]] | the cards; moves call `MoveTask` |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/projects/sprints\|projects.sprints]] | sprint board variant filters to active sprint |

---

## Core Features

- Kanban board with one column per task section or status (view toggle)
- Task cards: title, assignee avatar, priority badge, due date, label chips, sub-task count
- Drag-and-drop card to different column → `MoveTask` (status/section + order)
- Quick-add task from column header
- Filter board by: assignee, label, priority, due date
- Collapse empty columns
- Board-level metrics header: total tasks, done this week, overdue count
- Card detail slide-over on click (without leaving the board)
- Live updates: card moves broadcast to other viewers (Reverb — collaborative view)

---

## Data Model

No additional tables. Reads from `proj_tasks` and `proj_task_sections`.

## DTOs

Output only: `BoardData` — columns[] (id, name, task_count) + cards[] (task summary fields). Single query + grouping.

## Services & Actions

- `KanbanService::board(string $projectId, BoardFilterData $filters, string $groupBy): BoardData`
- Moves: `MoveTask` action (owned by projects.tasks)
- Broadcast: `TaskMoved` `ShouldBroadcast` on `company.{id}.projects` (task_id, from, to, moved_by) — UI sync only, not a domain event ([[architecture/websockets]])

---

## Filament

**Nav group:** Projects

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `KanbanBoardPage` | #3 Kanban custom page | Livewire + Alpine SortableJS; Reverb broadcast; project selector in header; slide-over detail |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('projects.kanban.view-any') && BillingService::hasModule('projects.kanban')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`projects.kanban.view` (+ task permissions for mutations).

---

## Test Checklist

- [ ] Tenant isolation + module gating + project-membership scoping
- [ ] Board groups correctly by section AND by status (toggle)
- [ ] Drag updates task via MoveTask (same validation path)
- [ ] `TaskMoved` broadcast on company channel
- [ ] Filters restrict cards; metrics header correct
- [ ] Single-query board (no N+1)

---

## Build Manifest

```
app/Data/Projects/{BoardData,BoardFilterData}.php
app/Services/Projects/KanbanService.php
app/Events/Projects/TaskMoved.php (ShouldBroadcast)
app/Filament/Projects/Pages/KanbanBoardPage.php
resources/views/filament/projects/pages/kanban-board.blade.php
app/Livewire/Projects/KanbanBoard.php
tests/Feature/Projects/KanbanBoardTest.php
```

---

## Related

- [[domains/projects/tasks]]
- [[domains/projects/sprints]]
- [[architecture/patterns/custom-pages]]
- [[architecture/websockets]]
