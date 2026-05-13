---
type: module
domain: Projects & Work
panel: projects
phase: 2
status: complete
cssclasses: domain-projects
migration_range: 200500–200999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-projects-phase2]]"
---

# Kanban Boards

Visual task workflow. Drag cards between columns. WIP limits prevent overload. Works for any team: engineering, marketing, support, ops.

---

## Board Structure

```
Backlog | To Do | In Progress | In Review | Done
  [card]  [card]   [card]       [card]     [card]
```

Columns fully configurable per board. Multiple boards per project.

---

## Cards

Each card = a task from [[task-management]]:
- Title, assignee avatar, due date, priority colour, labels
- Click to expand: full task detail without leaving board
- Colour-coded by: assignee / label / priority / due date
- Drag between columns → status updates automatically

---

## WIP Limits

Set maximum cards per column:
- "In Progress" limit: 3 cards per person
- Visual warning when limit exceeded (column turns amber)
- Hard block option: can't add to column when at limit

WIP limits enforce focus and expose bottlenecks.

---

## Swimlanes

Horizontal grouping within columns:
- By assignee: see each person's work in their swimlane
- By epic/project: multiple projects on one board
- By priority: urgent at top

---

## Board Templates

Common board presets:
- Simple: To Do / In Progress / Done
- Software: Backlog / Design / Development / Review / Testing / Done
- Marketing: Ideas / Brief / Production / Review / Published
- Support: New / Assigned / Pending / Resolved

---

## Filters

Filter cards on board:
- By assignee
- By label / tag
- By due date (overdue, due today, this week)
- By priority

Focused view: see only cards relevant to you.

---

## Data Model

### `proj_boards`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| project_id | ulid | FK |
| name | varchar(200) | |
| columns | json | ordered column config with WIP limits |

Boards query from `proj_tasks` filtered by project + board. No separate card table — cards are tasks.

---

## Migration

```
200500_create_proj_boards_table
```

---

## Related

- [[MOC_Projects]]
- [[task-management]]
- [[sprint-agile]]
- [[gantt-timeline]]
