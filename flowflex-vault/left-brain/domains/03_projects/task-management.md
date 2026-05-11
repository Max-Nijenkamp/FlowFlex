---
type: module
domain: Projects & Work
panel: projects
phase: 2
status: in-progress
cssclasses: domain-projects
migration_range: 200000–200499
last_updated: 2026-05-10
right_brain_log: "[[builder-log-projects-phase2]]"
---

# Task Management

The foundation of all project work. Create, assign, prioritise, and track tasks. Works standalone or within projects, kanban boards, and sprints.

---

## Task Structure

| Field | Description |
|---|---|
| Title | Short action description |
| Description | Rich text: context, acceptance criteria |
| Assignee(s) | One or more people |
| Due date | Deadline + optional start date |
| Priority | Urgent / High / Medium / Low |
| Status | To Do / In Progress / In Review / Done |
| Parent | Sub-task of another task |
| Project | Belongs to a project |
| Labels/Tags | Cross-project categorisation |
| Estimate | Hours or story points |
| Time logged | Actual hours (from [[project-time-tracking]]) |
| Attachments | Files, links |
| Comments | Threaded discussion |

---

## Views

Same tasks, different presentations:
- **List**: sortable table with inline editing
- **Board**: [[kanban-boards]] view
- **Timeline**: [[gantt-timeline]] view
- **My tasks**: personal view — assigned to me, due this week

---

## Dependencies

Task A blocks Task B:
- Blocked tasks visually flagged
- Gantt shifts automatically when blocker is delayed

---

## Recurring Tasks

For repetitive work:
- Repeat daily / weekly / monthly / custom
- New task auto-created on cadence
- Previous recurrence archived

---

## Bulk Actions

Select multiple tasks → assign, move, change status, set due date in one click. Useful for sprint planning and project kick-off.

---

## Notifications

- Assigned to me
- Due date approaching (T-24h, T-1h)
- Comment mentioning me (@mention)
- Status change on tasks I'm watching
- Blocker resolved

---

## Data Model

### `proj_tasks`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| project_id | ulid | nullable FK |
| parent_id | ulid | nullable self-FK |
| title | varchar(500) | |
| description | longtext | nullable |
| assignee_id | ulid | nullable FK |
| priority | enum | urgent/high/medium/low |
| status | varchar(50) | configurable per project |
| due_date | date | nullable |
| estimate_hours | decimal(8,2) | nullable |
| sort_order | int | |

---

## Migration

```
200000_create_proj_tasks_table
200001_create_proj_task_assignments_table
200002_create_proj_task_dependencies_table
200003_create_proj_task_comments_table
```

---

## Related

- [[MOC_Projects]]
- [[kanban-boards]]
- [[gantt-timeline]]
- [[sprint-agile]]
- [[project-time-tracking]]
