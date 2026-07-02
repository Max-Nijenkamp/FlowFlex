---
domain: projects
module: kanban
feature: board-view
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Board View & Drag-Move

The Kanban board: columns per section/status, draggable cards, live sync.

## Behaviour

- Render columns (per section or per status, toggle) with task cards.
- Drag a card to another column → `MoveTask` (status/section + order), optimistic move + `TaskMoved` broadcast.
- Quick-add task from a column header; filter by assignee/label/priority/due; collapse empty columns; metrics header (total, done this week, overdue).

## UI

- **Kind**: custom-page (Kanban — [[../../../../architecture/patterns/feature-ui-spec]] custom-page kind).
- **Page**: `KanbanBoardPage` at `/app/projects/kanban` (nav group Projects).
- **Layout**: horizontal columns; draggable cards (Alpine SortableJS); project selector + filters in header; card click → slide-over detail without leaving the board.
- **Key interactions**: drag card → confirm/optimistic move → `MoveTask` → broadcast; quick-add in column; filter chips.
- **States**: empty (no tasks → "Add your first task" per column) · loading (skeleton columns) · error (move rejected → revert + toast) · selected (card highlighted, slide-over open).
- **Gating**: `projects.kanban.view`; drag requires `projects.tasks.update`.

## Data

- Owns / writes: none — moves mutate `proj_tasks` **only** via `MoveTask` (owned by projects.tasks).
- Reads: `proj_tasks` + `proj_task_sections` via `KanbanService::board` (single query).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `TaskMoved` broadcast (viewers) — UI sync only.
- Shared entity: `proj_tasks`, `proj_task_sections` (owned by projects.tasks).

## Unknowns

- WIP limits, swimlanes, persisted per-user views — all deferred *(assumed)*. See [[../unknowns]].

## Related

- [[../_module|Kanban]] · [[../../tasks/_module|Tasks]] · [[../../sprints/_module|Sprints]]
