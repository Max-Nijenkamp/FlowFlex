---
type: module
domain: Projects & Work
panel: projects
phase: 2
status: planned
cssclasses: domain-projects
migration_range: 201000–201499
last_updated: 2026-05-09
---

# Gantt & Timeline

Visual project planning. Tasks on a timeline, dependencies as arrows, milestones as diamonds. The planning view for project managers who need to see the full picture.

---

## Timeline View

Tasks rendered as horizontal bars on a date axis:
- Bar width = task duration (start to end date)
- Bar position = when it happens
- Grouped by: project, assignee, milestone, phase

Zoom levels: day / week / month / quarter

---

## Dependencies

Drag between tasks to create dependency arrows:
- **Finish-to-Start** (FS): Task B can't start until Task A is done (most common)
- **Start-to-Start** (SS): Task B starts at same time as A
- **Finish-to-Finish** (FF): both must finish together

Critical path highlighted: the longest chain of dependencies that determines project end date. Delaying any critical path task delays the whole project.

---

## Milestones

Key project dates (deadlines, deliverables, reviews):
- Rendered as diamonds on timeline
- Can depend on tasks (milestone only reached when preceding tasks done)
- Share milestones with external stakeholders (link)

---

## Auto-Scheduling

When task duration or dependency changes → auto-shift downstream tasks:
- "If this task takes 3 extra days, when does the project end?"
- Manual override available (lock task to fixed date)

---

## Baseline

Save a baseline snapshot when project starts:
- Compare current timeline vs baseline
- See which tasks slipped, which are ahead
- Variance report: total days delayed by phase

---

## Resource View

Overlay: who is assigned what and when?
- Identify over-allocation (person has 40h of tasks in a 40h week = no slack)
- Reassign from gantt view directly

---

## Data Model

Uses `proj_tasks` (has start/end dates, dependencies). Gantt is a rendering layer.

### `proj_milestones`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| project_id | ulid | FK |
| name | varchar(300) | |
| due_date | date | |
| status | enum | upcoming/reached/missed |

### `proj_baselines`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| project_id | ulid | FK |
| saved_at | timestamp | |
| snapshot | json | tasks at save time |

---

## Migration

```
201000_create_proj_milestones_table
201001_create_proj_baselines_table
```

---

## Related

- [[MOC_Projects]]
- [[task-management]]
- [[kanban-boards]]
- [[sprint-agile]]
- [[project-budget-costs]]
