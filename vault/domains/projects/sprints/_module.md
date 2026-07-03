---
domain: projects
module: sprints
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Sprints

Sprint planning, backlog management, velocity tracking, and retrospective notes for teams using Scrum or iteration-based workflows.

## Module-key

`projects.sprints`

**Priority:** p2  
**Panel:** projects  
**Permission prefix:** `projects.sprints`  
**Tables:** `proj_sprints`, `proj_sprint_tasks`

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../tasks/_module\|projects.tasks]] | sprints contain tasks |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] | gating + permissions |

## Core Features

- Sprint record: name, start/end date, goal, project.
- Status machine `planning → active → completed`; only one active sprint per project.
- Backlog: tasks not yet in a sprint; drag from backlog into a sprint.
- Sprint board: Kanban filtered to the active sprint.
- Burndown chart (remaining points/hours over sprint days, computed from completion timestamps *(assumed — no snapshot table)*).
- Velocity: completed points per sprint, rolling 3-sprint average.
- Retrospective notes (went well / improve / actions).
- Complete sprint: incomplete tasks → backlog or next sprint (user choice).

## See features/

- [[features/sprint-lifecycle|Sprint Lifecycle & Backlog]] — plan/start/complete + backlog assignment.
- [[features/burndown-velocity|Burndown & Velocity]] — the charts + rolling average.

## Build Manifest

```
database/migrations/xxxx_create_proj_sprints_table.php
database/migrations/xxxx_create_proj_sprint_tasks_table.php
app/Models/Projects/{Sprint,SprintTask}.php
app/States/Projects/Sprint/{SprintState,Planning,Active,Completed}.php
app/Data/Projects/{CreateSprintData,CompleteSprintData,AssignTaskData}.php
app/Contracts/Projects/SprintServiceInterface.php · app/Services/Projects/SprintService.php
app/Actions/Projects/CompleteSprint.php
app/Exceptions/Projects/ActiveSprintExistsException.php
app/Filament/Projects/Resources/SprintResource.php · Pages/SprintBoardPage.php · Widgets/BurndownChartWidget.php
database/factories/Projects/SprintFactory.php
tests/Feature/Projects/{SprintTest,SprintCompletionTest}.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot see/manage company B's sprints or backlog.
- [ ] Module gating: artifacts hidden when `projects.sprints` inactive.
- [ ] Second active sprint per project rejected.
- [ ] Task can't sit in two active sprints.
- [ ] Complete with backlog vs next-sprint moves incomplete tasks correctly.
- [ ] Burndown math over fixture completions.
- [ ] Velocity rolling average over 3 sprints.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | task status + completion | projects.tasks | burndown/velocity read task completion; board reuses task moves |

**Data ownership:** `projects.sprints` writes only `proj_sprints` + `proj_sprint_tasks`. Task moves on the sprint board route through projects.tasks' `MoveTask`; burndown reads task completion timestamps read-only ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../tasks/_module|Tasks]] · [[../kanban/_module|Kanban]]
- [[../../../glossary]]
