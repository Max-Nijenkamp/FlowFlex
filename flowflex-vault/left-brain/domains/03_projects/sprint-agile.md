---
type: module
domain: Projects & Work
panel: projects
phase: 2
status: in-progress
cssclasses: domain-projects
migration_range: 201500–201999
last_updated: 2026-05-10
right_brain_log: "[[builder-log-projects-phase2]]"
---

# Sprint & Agile

Scrum-style sprint management. Sprint planning, backlog grooming, velocity tracking, burndown charts, and retrospectives. For engineering teams running agile.

---

## Sprint Cycle

```
Backlog refinement → Sprint planning → Sprint active
→ Daily standups (async check-ins)
→ Sprint review (demo)
→ Retrospective
→ Next sprint
```

Sprint length: configurable (1 week, 2 weeks, 3 weeks, 4 weeks).

---

## Backlog

Product backlog: all unassigned tasks/user stories:
- Prioritised by product manager (drag to reorder)
- Story points (Fibonacci: 1, 2, 3, 5, 8, 13)
- Epics: group stories by feature/theme
- User story format: "As a [user], I want [feature] so that [benefit]"

---

## Sprint Planning

Create a sprint, set sprint goal:
- Drag backlog items into sprint
- Total story points auto-summed
- Team velocity (average story points completed per sprint) shown → guide capacity
- Assign stories to developers

---

## Sprint Board

Kanban view for active sprint only:
- Columns: To Do / In Progress / In Review / Done
- Filtered to this sprint's tasks
- Burndown chart: story points remaining vs ideal line

---

## Burndown Chart

Visual: are we on track to complete the sprint?
- X-axis: sprint days
- Y-axis: story points remaining
- Ideal line: straight diagonal from total → 0
- Actual line: real remaining points
- Below ideal = ahead, above = behind

---

## Velocity Tracking

Sprint-over-sprint velocity chart:
- Completed story points per sprint (last 10 sprints)
- Average velocity → capacity for next sprint planning
- Trend: is the team getting faster?

---

## Retrospectives

Structured retro after each sprint:
- **Went well**: what to keep doing
- **Needs improvement**: what to change
- **Action items**: concrete changes for next sprint (assigned + tracked as tasks)

---

## Data Model

### `proj_sprints`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| project_id | ulid | FK |
| name | varchar(200) | "Sprint 14" |
| goal | text | nullable |
| starts_at | date | |
| ends_at | date | |
| status | enum | planning/active/completed |
| velocity | int | nullable — story points completed |

### `proj_sprint_items`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| sprint_id | ulid | FK |
| task_id | ulid | FK |
| story_points | int | nullable |
| added_at | timestamp | |

---

## Migration

```
201500_create_proj_sprints_table
201501_create_proj_sprint_items_table
201502_create_proj_epics_table
```

---

## Related

- [[MOC_Projects]]
- [[task-management]]
- [[kanban-boards]]
- [[project-time-tracking]]
