---
type: module
domain: Projects & Work
panel: projects
module-key: projects.gantt
status: planned
color: "#4ADE80"
---

# Gantt

> Gantt chart — task bars plotted across a 90-day timeline, status colours, milestone diamonds, dependency arrows, and date adjustment by drag.

**Panel:** `projects`
**Module key:** `projects.gantt`

## What It Does

The Gantt module is a custom Filament page that renders all tasks and milestones for a project as a horizontal timeline chart. Task bars span from start date to due date and are coloured by status. Milestone diamonds appear on their target dates. Dependency arrows connect blocked tasks to their blockers. Team members can drag task bars to adjust dates — the task's `due_date` and `start_date` update on drop. The 90-day window is scrollable. The chart is the visual planning canvas for project managers; it reads task data from the Tasks module and writes date changes back to tasks.

## Features

### Core
- Task bars: horizontal bars from `start_date` to `due_date` — coloured by status (To Do = grey, In Progress = indigo, Done = green, Overdue = red)
- Milestone diamonds: displayed on `target_date` — green when complete, red when overdue
- Scrollable timeline: 90-day window, scrollable left/right, with today highlighted
- Drag to reschedule: drag a task bar to a new position — updates `start_date` and `due_date`; duration preserved
- Dependency arrows: lines connecting blocked tasks to their blocking tasks — visual representation of `proj_task_dependencies`

### Advanced
- Dependency enforcement: dragging a task earlier than its blocker's end date triggers a warning
- Resource row: optional mode shows tasks grouped by assignee in separate rows rather than grouped by status
- Critical path highlight: tasks on the critical path (longest chain of dependencies to the project end) highlighted with a bold outline
- Zoom levels: week view, month view, quarter view — adjusts granularity of time axis
- Baseline snapshot: save a baseline plan — compare current task dates vs baseline on the chart to visualise slippage

### AI-Powered
- Schedule risk summary: AI analyses the critical path and identifies the three tasks most likely to cause overall project delay — surfaced as an insight panel beside the chart
- Auto-reschedule: when a task's due date is moved, AI suggests how downstream dependent tasks should shift to maintain realistic spacing

## Data Model

```erDiagram
    proj_gantt_baselines {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        string name
        json task_snapshots
        timestamp saved_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `task_snapshots` | JSON copy of task start/due dates at baseline save time |
| All task data | Read from `proj_tasks` — Gantt is a view, not a data store |

## Permissions

- `projects.gantt.view`
- `projects.gantt.reschedule-tasks`
- `projects.gantt.save-baseline`
- `projects.gantt.view-critical-path`
- `projects.gantt.export`

## Filament

- **Resource:** None
- **Pages:** None
- **Custom pages:** `GanttChartPage` — full-width interactive Gantt at `/projects/{project}/gantt`
- **Widgets:** None
- **Nav group:** Planning (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| MS Project | Gantt chart and project scheduling |
| Smartsheet | Gantt and timeline view |
| Monday.com Timeline | Visual project timeline |
| Asana Timeline | Gantt-style project view |

## Implementation Notes

**Filament:** Requires a custom `Page` class (`GanttChartPage extends Page`). The Gantt chart itself cannot be built with standard Filament table components. Use a JavaScript Gantt library rendered inside the Livewire component's Blade view. Recommended library: **DHTMLX Gantt** (MIT for basic; commercial for advanced dependency arrows) or **Frappe Gantt** (MIT, lighter). The library renders into a `<div id="gantt-container">` and is initialised via `@push('scripts')`. Drag events call back to a Livewire `rescheduleTask($taskId, $newStart, $newEnd)` action. The `protected string $view` must be non-static (Filament 5 pattern #2).

**External dependency:** Gantt library choice must be decided before build. **Frappe Gantt** (MIT) covers the core requirements but lacks critical-path drawing. **DHTMLX Gantt** (free edition) covers all features including critical path but has a commercial licence requirement for production use. Decision must go in an ADR.

**Real-time:** Reverb not required for MVP — Gantt is a planning tool, not a live collaboration surface. However, if two users open the same project's Gantt simultaneously, a stale-read warning can be shown by storing a `last_modified_at` on the `proj_gantt_baselines` row and comparing on load.

**AI features:** Schedule risk summary calls `app/Services/AI/GanttInsightService.php` wrapping OpenAI GPT-4o. Input is a JSON summary of the critical path tasks and their current delays. Auto-reschedule suggestion is a pure PHP algorithm (shift downstream tasks by the same delta as the moved task) — no LLM required unless the "intelligent spacing" heuristic needs natural language output.

**PDF/Export:** Gantt export requires server-side chart rendering. Options: (1) Puppeteer/Browsershot to screenshot the rendered chart — requires a headless Chrome process; (2) a pure PHP SVG renderer. Browsershot (`spatie/browsershot`) is the recommended approach — add `spatie/browsershot` to `composer.json` and document the Node.js + Puppeteer dependency in the Dockerfile.

## Related

- [[tasks]]
- [[milestones]]
- [[sprints]]
- [[portfolios]]
