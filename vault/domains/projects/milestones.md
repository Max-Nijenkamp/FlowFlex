---
type: module
domain: Projects & Work
panel: projects
module-key: projects.milestones
status: planned
color: "#4ADE80"
---

# Milestones

Key project checkpoints with target dates. Visible on the Gantt chart and project timeline. Trigger notifications when overdue.

## Core Features

- Milestone record: title, description, target date, project, status (open/achieved/missed)
- Milestone achievement: mark as achieved with actual date + notes
- Link tasks to a milestone (task completion contributes to milestone progress)
- Milestone progress: % of linked tasks complete
- Overdue detection: target date passed and status still open
- Milestone view: list of all milestones across projects, filterable by status and date
- Notification when milestone is 7 days away and incomplete

## Data Model

| Table | Key Columns |
|---|---|
| `proj_milestones` | company_id, project_id, title, description, target_date, achieved_date, status |
| `proj_milestone_tasks` | milestone_id, task_id, company_id |

## Filament

**Nav group:** Projects

- `MilestoneResource` — list, create, edit, achieve action
- Milestone timeline widget on project view page

## Related

- [[domains/projects/tasks]]
- [[domains/projects/gantt]]
