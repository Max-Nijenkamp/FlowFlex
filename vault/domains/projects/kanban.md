---
type: module
domain: Projects & Work
panel: projects
module-key: projects.kanban
status: planned
color: "#4ADE80"
---

# Kanban

> Visual kanban board — drag task cards between configurable status columns, enforce WIP limits, and see your project's flow at a glance.

**Panel:** `projects`
**Module key:** `projects.kanban`

## What It Does

The Kanban module is a custom Filament page that renders project tasks as draggable cards in status columns. It is a view layer on top of the Tasks module — no separate data model is needed. Each column represents a task status. Dragging a card between columns updates the task's status. WIP (work-in-progress) limits can be set per column; the board shows a red indicator when a column exceeds its limit. The board filters to the current project and sprint if a sprint is active.

## Features

### Core
- Columns: one column per task status — order and names match the project's configured statuses
- Draggable cards: drag a task card between columns to update its status instantly
- Card information shown: title, assignee avatar, priority badge, due date, subtask count, comment count
- Filter: filter board by assignee, label, priority, or due date range
- Project scope: board shows all tasks in the selected project; sprint scope shows only sprint tasks

### Advanced
- WIP limits: HR sets a maximum card count per column — exceeded column header turns red as a visual warning
- Swimlanes: optional grouping of cards by assignee or label — rows within each column
- Quick-create: click the `+` button in any column to create a new task in that status without leaving the board
- Collapsed columns: long-done columns can be collapsed to focus on active workflow columns
- Fullscreen mode: board expands to fill the browser window for wall-display or team ceremony use

### AI-Powered
- Flow bottleneck detection: AI identifies columns where cards are accumulating without moving — surfaces "cards in 'In Review' are taking 3.2× longer than average" as an insight card above the board
- Priority reordering suggestion: within a column, AI suggests reordering cards to surface highest-impact blocked items

## Data Model

```erDiagram
    proj_board_configs {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        json column_order
        json wip_limits
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `column_order` | JSON array of status strings in display order |
| `wip_limits` | JSON map of `{status: max_cards}` |
| Task data | All read from `proj_tasks` — no separate kanban records |

## Permissions

- `projects.kanban.view`
- `projects.kanban.move-cards`
- `projects.kanban.configure-board`
- `projects.kanban.set-wip-limits`
- `projects.kanban.quick-create`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `KanbanBoardPage` — interactive board at `/projects/{project}/kanban`
- **Widgets:** None
- **Nav group:** Work (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Trello | Kanban board management |
| Monday.com (board view) | Kanban-style work management |
| Asana (board view) | Kanban and visual board |
| Linear | Kanban issue board |

## Implementation Notes

**Filament:** Requires a custom `Page` class (`KanbanBoardPage extends Page`) — not a standard Resource. The Livewire component renders columns and cards; Alpine.js handles drag initiation; card drop posts to a Livewire action that calls `TaskService::updateStatus()`. Standard `ListRecords` cannot render a board view. Use `protected string $view = 'filament.projects.pages.kanban-board';` (non-static, per Filament 5 pattern #2).

**Real-time:** Reverb broadcasting required. Broadcast a `CardMoved` event on the `kanban.{project_id}` private channel whenever a card's status changes. Other users watching the same board update their column counts via a Livewire `$listeners` array. Presence channel (`presence-kanban.{project_id}`) shows who is currently viewing the board — useful for collaborative teams and avoids conflicting moves.

**Drag-and-drop library:** Use SortableJS (CDN or npm) injected via `@push('scripts')` in the Blade view. Alpine.js wires the `onEnd` callback to a Livewire `moveCard($taskId, $newStatus)` action. WIP limit check runs server-side inside that action before persisting.

**External dependency:** None — no third-party kanban SDK needed.

**AI features:** Bottleneck detection runs as a scheduled query (daily, or on-demand button) comparing avg days-in-column for each status against 90-day baseline. No external AI call required — pure SQL window function on `proj_tasks`. Priority reorder suggestion calls `app/Services/AI/KanbanInsightService.php` which wraps OpenAI GPT-4o with a prompt template at `resources/prompts/kanban-priority.txt`.

**Missing from data model:** `proj_board_configs` needs `ulid company_id FK` declared explicitly so `BelongsToCompany` trait applies and `CompanyScope` activates. Also needs a unique constraint on `(company_id, project_id)` — one config per project.

## Related

- [[tasks]]
- [[sprints]]
- [[gantt]]
- [[approvals]]
