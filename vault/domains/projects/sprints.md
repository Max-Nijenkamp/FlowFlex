---
type: module
domain: Projects & Work
panel: projects
module-key: projects.sprints
status: planned
color: "#4ADE80"
---

# Sprints

> Agile sprint management — sprint backlog, velocity tracking, burndown chart, and retrospective notes for teams running scrum or time-boxed iterations.

**Panel:** `projects`
**Module key:** `projects.sprints`

## What It Does

Sprints enables agile teams to work in time-boxed iterations. A sprint is a named period (typically 1–2 weeks) with a defined start and end date and a set of committed tasks pulled from the project backlog. The burndown chart tracks remaining effort over the sprint duration. At sprint end, unfinished tasks roll over to the next sprint or return to the backlog. Velocity is tracked across sprints so teams can plan future sprint capacity. Retrospective notes are attached to each completed sprint.

## Features

### Core
- Sprint creation: name, goal statement, start date, end date, project scope
- Backlog view: all tasks in the project not assigned to a sprint — drag to sprint backlog to commit
- Sprint backlog: tasks committed to the current sprint with status and assignee
- Sprint states: `planning` → `active` → `completed`
- Only one sprint can be active per project at a time

### Advanced
- Burndown chart: remaining story points or hours plotted daily — ideal line vs actual line — rendered as a read-only chart on sprint detail page
- Velocity: completed points per sprint — tracked across last 10 sprints, shown as bar chart on sprint list page
- Sprint end: complete sprint action — prompts which incomplete tasks to roll over vs return to backlog
- Capacity planning: before starting a sprint, input available hours per team member for the sprint period — compare to committed task estimates
- Sprint retrospective: structured notes page (What went well / What to improve / Actions) attached to each completed sprint

### AI-Powered
- Over-commitment detection: AI compares committed task estimates to available team capacity before sprint start and warns if sprint is likely to overrun by >20%
- Velocity trend: AI identifies whether velocity is improving, declining, or stable and adds a one-line interpretation below the velocity chart

## Data Model

```erDiagram
    proj_sprints {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        string name
        text goal
        date start_date
        date end_date
        string status
        integer committed_points
        integer completed_points
        text retrospective_notes
        timestamps created_at/updated_at
    }

    proj_sprint_tasks {
        ulid sprint_id FK
        ulid task_id FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | planning / active / completed |
| `committed_points` | Total story points at sprint start |
| `completed_points` | Points in tasks with Done status at sprint end |

## Permissions

- `projects.sprints.view`
- `projects.sprints.create`
- `projects.sprints.manage-backlog`
- `projects.sprints.start-complete`
- `projects.sprints.view-velocity`

## Filament

- **Resource:** `SprintResource`
- **Pages:** `ListSprints`, `ViewSprint` — backlog drag-to-sprint, task list, burndown chart
- **Custom pages:** None
- **Widgets:** `ActiveSprintWidget` — current sprint name, progress, and days remaining on dashboard
- **Nav group:** Work (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Jira Software | Agile sprint management |
| Linear | Sprint and cycle management |
| Azure DevOps | Sprint planning and backlog |
| Shortcut (Clubhouse) | Sprint and iteration management |

## Implementation Notes

**Filament:** `SprintResource` is a standard Resource. `ViewSprint` is the main page — it renders a `Tabs` component: Tab 1 = sprint backlog (Livewire table of committed tasks with drag-to-reorder), Tab 2 = burndown chart (chart.js line chart rendered in a custom Livewire widget embedded in the page), Tab 3 = retrospective (a `Textarea` form section on the sprint record). The backlog drag-to-sprint feature on `ListSprints` is not standard Filament table behaviour — it requires a custom Livewire component with SortableJS for the backlog-to-sprint drag interaction.

**Charts:** Burndown chart uses **chart.js** (already in the tech stack via `resources/js/`) rendered via a `<canvas>` element in a Blade partial. The chart data is hydrated via a Livewire `$chartData` public property containing daily remaining points, computed from `proj_time_entries` and `proj_sprint_tasks`. Velocity bar chart follows the same pattern on `ListSprints`.

**Real-time:** Reverb not required. The burndown chart is a daily snapshot — it does not need to tick live. Refresh on page load is sufficient.

**Missing from data model:** `proj_sprints` needs `ulid company_id FK` for `BelongsToCompany` and `CompanyScope`. The `retrospective_notes` column is a single text field — this is sufficient for MVP. A structured retrospective (three separate sections) can be stored as three columns (`retro_went_well`, `retro_improve`, `retro_actions`) or as a JSON column — decide before migration.

**AI features:** Over-commitment detection is a PHP calculation (sum committed estimates vs available hours sum) — no LLM needed. Velocity trend interpretation calls `app/Services/AI/SprintInsightService.php` with a prompt asking GPT-4o to interpret a JSON array of the last 10 sprint velocities.

## Related

- [[tasks]]
- [[kanban]]
- [[time-tracking]]
- [[portfolios]]
