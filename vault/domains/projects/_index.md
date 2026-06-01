---
type: domain-index
domain: Projects & Work
panel: projects
color: "#4ADE80"
---

# Projects & Work

Task management, sprints, Kanban board, Gantt chart, OKRs, time tracking, and milestones. **Panel:** `/projects` (Indigo) — Phase 2.

**Displaces**: Asana, Monday.com, Jira

---

## Navigation Groups

- **Projects** — Projects, Kanban Board, Gantt Chart, Milestones, Workload
- **Tasks** — Tasks, My Tasks
- **Sprints** — Sprints, Sprint Board
- **Time** — Time Tracking, Timesheets
- **OKRs** — Objectives & Key Results
- **Settings** — Project Templates, Resource Allocation

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/projects/projects\|Projects]] | `projects.projects` | planned | **P2 core** |
| [[domains/projects/tasks\|Tasks]] | `projects.tasks` | planned | **P2 core** |
| [[domains/projects/kanban\|Kanban Board]] | `projects.kanban` | planned | **P2 core** |
| [[domains/projects/sprints\|Sprints]] | `projects.sprints` | planned | P2 |
| [[domains/projects/time-tracking\|Time Tracking]] | `projects.time` | planned | P2 |
| [[domains/projects/milestones\|Milestones]] | `projects.milestones` | planned | P2 |
| [[domains/projects/gantt\|Gantt Chart]] | `projects.gantt` | planned | P2 |
| [[domains/projects/okrs\|OKRs]] | `projects.okrs` | planned | P3 |
| [[domains/projects/templates\|Project Templates]] | `projects.templates` | planned | P3 |
| [[domains/projects/workload\|Workload]] | `projects.workload` | planned | P3 |
| [[domains/projects/resource-allocation\|Resource Allocation]] | `projects.resources` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-model-states` — project status, task status, sprint status
- Custom Filament pages — Kanban, Gantt, Sprint Board, Workload, Timesheet (see [[architecture/patterns/custom-pages]])
- `lorisleiva/laravel-actions` — `MoveTask`, `StartTimer`, `CompleteSprint`
- Cross-domain: `TimeEntryApproved` → Finance (billable hours), `TaskCompleted` → Milestones
