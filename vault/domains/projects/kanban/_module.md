---
domain: projects
module: kanban
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Kanban Board

Visual Kanban board with task cards grouped by section/status; drag-and-drop cards between columns. The primary view for sprint work and day-to-day task management. **Pure view module — owns no tables.**

## Module-key

`projects.kanban`

**Priority:** p2  
**Panel:** projects  
**Permission prefix:** `projects.kanban`  
**Tables:** *(none — reads `proj_tasks`, `proj_task_sections`)*

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tasks/_module\|projects.tasks]] | the cards; moves call `MoveTask` |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |
| Soft | [[../sprints/_module\|projects.sprints]] | sprint-board variant filters to the active sprint |

## Core Features

- One column per task section or status (view toggle).
- Cards: title, assignee avatar, priority badge, due date, label chips, sub-task count.
- Drag card between columns → `MoveTask` (status/section + order).
- Quick-add task from a column header; filters (assignee/label/priority/due); collapse empty columns.
- Board metrics header: total, done this week, overdue.
- Card detail slide-over on click (without leaving the board).
- Live updates: moves broadcast to other viewers (Reverb).

## See features/

- [[features/board-view|Board View & Drag-Move]] — the board render, grouping, and drag → `MoveTask`.

## Build Manifest

```
app/Data/Projects/{BoardData,BoardFilterData}.php
app/Services/Projects/KanbanService.php
app/Events/Projects/TaskMoved.php (ShouldBroadcast — owned conceptually by tasks; reused here)
app/Filament/Projects/Pages/KanbanBoardPage.php
resources/views/filament/projects/pages/kanban-board.blade.php
app/Livewire/Projects/KanbanBoard.php
tests/Feature/Projects/KanbanBoardTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot render or move company B's cards.
- [ ] Module gating: artifacts hidden when `projects.kanban` inactive.
- [ ] Project-membership scoping: non-member cannot open the board for a project.
- [ ] Board groups correctly by section AND by status (toggle).
- [ ] Drag updates task via `MoveTask` (same validation path).
- [ ] `TaskMoved` broadcast on company channel.
- [ ] Filters restrict cards; metrics header correct.
- [ ] Single-query board (no N+1).

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | `MoveTask` action | projects.tasks | all card moves route through the tasks action (single validation path) |
| Broadcast | `TaskMoved` (ShouldBroadcast) | viewers | UI sync only, not a domain event |
| Reads | active sprint filter | projects.sprints | sprint-board variant |

**Data ownership:** Kanban owns **no tables**. It reads `proj_tasks` + `proj_task_sections` and mutates them **only** through `MoveTask` (owned by projects.tasks) — never a direct write ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../tasks/_module|Tasks]] · [[../sprints/_module|Sprints]] · [[../../../architecture/websockets]]
- [[../../../glossary]]
