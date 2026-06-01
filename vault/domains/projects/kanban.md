---
type: module
domain: Projects & Work
panel: projects
module-key: projects.kanban
status: planned
color: "#4ADE80"
---

# Kanban Board

Visual Kanban board with task cards grouped by section/status. Drag-and-drop cards between columns. Primary view for sprint work and day-to-day task management.

## Core Features

- Kanban board with one column per task section or status
- Task cards: title, assignee avatar, priority badge, due date, label chips, sub-task count
- Drag-and-drop card to different column → updates task status/section
- Quick-add task from column header
- Filter board by: assignee, label, priority, due date
- Collapse empty columns
- Board-level metrics header: total tasks, done this week, overdue count
- Switch between project sections view and status view
- Card detail slide-over on click (without leaving the board)

## Data Model

No additional tables. Reads from `proj_tasks` and `proj_task_sections`.

## Filament

**Nav group:** Projects

- `KanbanBoardPage` (custom Filament page) — Livewire component with Alpine.js SortableJS drag-and-drop
- Blade view: `<x-filament-panels::page>` wrapper + Livewire board component
- Project selector in page header (switch board between projects)

## Related

- [[domains/projects/tasks]]
- [[domains/projects/sprints]]
- [[architecture/patterns/custom-pages]]
