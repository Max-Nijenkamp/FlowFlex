---
type: module
domain: Projects & Work
domain-key: projects
panel: projects
module-key: projects.sprints
status: planned
priority: p2
depends-on: [projects.tasks, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [states, custom-pages]
tables: [proj_sprints, proj_sprint_tasks]
permission-prefix: projects.sprints
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Sprints

Sprint planning, backlog management, velocity tracking, and sprint retrospective notes for teams using Scrum or iteration-based workflows.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/projects/tasks\|projects.tasks]] | sprints contain tasks |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Sprint record: name, start date, end date, goal, project
- Sprint status machine: `planning → active → completed`
- Backlog: task pool not yet assigned to a sprint
- Sprint planning: drag tasks from backlog into sprint
- Sprint board: Kanban view filtered to current sprint tasks
- Burndown chart: remaining story points / hours over sprint days (daily snapshot *(assumed: computed from task completion timestamps, no snapshot table)*)
- Velocity tracking: completed points per sprint, rolling 3-sprint average
- Sprint retrospective notes (what went well, what to improve, action items)
- Only one active sprint per project at a time
- Complete sprint: incomplete tasks → backlog or next sprint (user choice)

---

## Data Model

### proj_sprints

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed), project_id FK | ulid | | |
| name | string | not null | |
| goal | text | nullable | |
| start_date / end_date | date | end after start | |
| status | string | default `planning` | state machine |
| retro | jsonb | nullable | {went_well, improve, actions[]} |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, project_id, status)` — one active enforced in service

### proj_sprint_tasks

| Column | Type | Notes |
|---|---|---|
| id, sprint_id FK, task_id FK, company_id | ulid | unique `(sprint_id, task_id)`; task in one active sprint max |
| story_points | int nullable | |

---

## State Machine

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `planning` | `active` | `projects.sprints.manage` | throws `ActiveSprintExistsException` when project already has one |
| `active` | `completed` | `projects.sprints.manage` | incomplete tasks moved per choice; velocity recorded |

---

## DTOs

### CreateSprintData — project_id, name, goal?, start_date/end_date (end after start)
### CompleteSprintData — sprint_id, incomplete_action (in:backlog,next-sprint), next_sprint_id (required_if next-sprint)
### AssignTaskData — sprint_id, task_id (same project, not in another active sprint), story_points?

## Services & Actions

Interface→Service: `SprintServiceInterface` → `SprintService`.

- `start(string $sprintId)` — one-active rule
- `assignTask(AssignTaskData)` / `removeTask(...)`
- `CompleteSprint::run(CompleteSprintData)` — action; moves incomplete tasks, records velocity
- `burndown(string $sprintId): array` — per-day remaining points from completion timestamps
- `velocity(string $projectId): array` — per-sprint completed points + rolling average

---

## Filament

**Nav group:** Sprints

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `SprintResource` | #1 CRUD resource | start/complete actions, retro form on view |
| `SprintBoardPage` | #3 Kanban custom page | active-sprint board + backlog sidebar (drag in/out) |
| `BurndownChartWidget` | #6 widget (apex) | on sprint view |

---

## Permissions

`projects.sprints.view-any` · `projects.sprints.manage` · `projects.sprints.assign-tasks`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Second active sprint per project rejected
- [ ] Task can't sit in two active sprints
- [ ] Complete with backlog vs next-sprint moves incomplete tasks correctly
- [ ] Burndown math over fixture completions
- [ ] Velocity rolling average over 3 sprints

---

## Build Manifest

```
database/migrations/xxxx_create_proj_sprints_table.php
database/migrations/xxxx_create_proj_sprint_tasks_table.php
app/Models/Projects/{Sprint,SprintTask}.php
app/States/Projects/Sprint/{SprintState,Planning,Active,Completed}.php
app/Data/Projects/{CreateSprintData,CompleteSprintData,AssignTaskData}.php
app/Contracts/Projects/SprintServiceInterface.php
app/Services/Projects/SprintService.php
app/Actions/Projects/CompleteSprint.php
app/Exceptions/Projects/ActiveSprintExistsException.php
app/Filament/Projects/Resources/SprintResource.php
app/Filament/Projects/Pages/SprintBoardPage.php
app/Filament/Projects/Widgets/BurndownChartWidget.php
database/factories/Projects/SprintFactory.php
tests/Feature/Projects/{SprintTest,SprintCompletionTest}.php
```

---

## Related

- [[domains/projects/tasks]]
- [[domains/projects/kanban]]
