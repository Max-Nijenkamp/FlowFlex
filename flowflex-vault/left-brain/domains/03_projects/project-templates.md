---
type: module
domain: Projects & Work
panel: projects
phase: 2
status: complete
cssclasses: domain-projects
migration_range: 202000–202499
last_updated: 2026-05-12
right_brain_log: "[[builder-log-projects-phase2]]"
---

# Project Templates

Reusable project structures. Start a new client onboarding, product launch, or software deployment with all tasks, milestones, and phases pre-built. Consistent delivery, less setup time.

---

## Template Content

A project template captures:
- Project structure: phases, milestones, task list
- Task details: description, checklists, estimated duration, dependencies
- Default assignee roles (not specific people — roles like "PM", "Developer", "Client")
- Board configuration: columns, labels
- Custom fields relevant to this project type

---

## Template Library

Out-of-box templates:
| Template | Use Case |
|---|---|
| Client Onboarding | Steps to get a new customer live |
| Software Sprint | 2-week agile sprint structure |
| Product Launch | Pre-launch, launch day, post-launch |
| Website Redesign | Discovery → design → build → launch |
| Event Planning | 12-week event production timeline |
| Office Move | All tasks for a physical office relocation |
| Audit Preparation | Internal audit readiness checklist |

---

## Creating a Template

From any existing project:
1. Project → "Save as template"
2. Choose what to include: tasks, boards, milestones, files
3. Clear assignees (keep roles only)
4. Clear dates (keep relative durations: "3 days after project start")

---

## Using a Template

New project:
1. Choose template
2. Set project start date (or target end date → back-calculate)
3. Assign team members to roles
4. All tasks and milestones auto-created with correct relative dates

---

## Data Model

### `proj_templates`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(300) | |
| description | text | nullable |
| category | varchar(100) | |
| is_public | boolean | available to all tenants |
| task_count | int | |

### `proj_template_tasks`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| template_id | ulid | FK |
| title | varchar(500) | |
| role | varchar(100) | nullable |
| offset_days | int | days from project start |
| duration_days | int | nullable |
| depends_on | ulid | nullable self-FK |

---

## Migration

```
202000_create_proj_templates_table
202001_create_proj_template_tasks_table
202002_create_proj_template_milestones_table
```

---

## Related

- [[MOC_Projects]]
- [[task-management]]
- [[gantt-timeline]]
- [[kanban-boards]]
