---
type: module
domain: Projects & Work
panel: projects
module-key: projects.milestones
status: planned
color: "#4ADE80"
---

# Milestones

> Project milestone tracking — define key deliverable dates, link dependent tasks, monitor completion, and surface overdue milestones early.

**Panel:** `projects`
**Module key:** `projects.milestones`

## What It Does

Milestones represent key deliverable checkpoints within a project — a product launch, a client handover, a regulatory submission. Each milestone has a target date, a description, and a set of linked tasks that must be completed for the milestone to be achieved. Milestone status is computed automatically from its linked task completion. When all linked tasks are done, the milestone auto-marks as complete. Overdue milestones (target date passed, not complete) are flagged in red on the milestone list and surfaced in the Portfolios dashboard.

## Features

### Core
- Milestone record: name, description, target date, project, status
- Status: `planned` → `in_progress` → `complete` / `overdue` (computed from linked tasks)
- Link tasks: associate tasks with a milestone — milestone completion depends on all linked tasks reaching Done status
- Milestone list: filterable by status, project, and date range — with completion percentage column
- Overdue highlight: milestones past their target date without complete status shown with red date badge

### Advanced
- Milestone dependencies: milestone B cannot start until milestone A is complete — dependency chain visualised
- Progress percentage: computed as (completed linked tasks ÷ total linked tasks) × 100 — shown as a progress bar
- Upcoming milestones widget: next three milestones by target date shown on project dashboard
- Milestone summary on portfolio: all milestones across multiple projects visible in the Portfolios module overview
- Risk indicator: if the current task completion rate implies the milestone will be missed, a yellow "at risk" badge appears before the target date

### AI-Powered
- Deadline confidence score: based on current task completion velocity and remaining work, AI estimates the probability of hitting the milestone target date — shown as a percentage
- Dependency risk propagation: if a task linked to Milestone A is delayed, AI identifies which downstream milestones are at risk and notifies the project manager

## Data Model

```erDiagram
    proj_milestones {
        ulid id PK
        ulid company_id FK
        ulid project_id FK
        string name
        text description
        date target_date
        string status
        date completed_at
        timestamps created_at/updated_at
    }

    proj_milestone_tasks {
        ulid milestone_id FK
        ulid task_id FK
    }

    proj_milestone_dependencies {
        ulid milestone_id FK
        ulid depends_on_milestone_id FK
    }
```

| Column | Notes |
|---|---|
| `status` | planned / in_progress / complete / overdue |
| `completed_at` | Set when all linked tasks reach Done status |
| `proj_milestone_tasks` | Pivot — tasks linked to this milestone |

## Permissions

- `projects.milestones.view`
- `projects.milestones.create`
- `projects.milestones.edit`
- `projects.milestones.delete`
- `projects.milestones.link-tasks`

## Filament

- **Resource:** `MilestoneResource`
- **Pages:** `ListMilestones`, `CreateMilestone`, `EditMilestone`, `ViewMilestone` (with linked task list and completion bar)
- **Custom pages:** None
- **Widgets:** `UpcomingMilestonesWidget` — next three milestones by date on project dashboard
- **Nav group:** Planning (projects panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Asana Milestones | Project milestone tracking |
| Monday.com Milestones | Deadline and milestone management |
| Smartsheet | Project milestone and Gantt |
| MS Project | Milestone management |

## Related

- [[tasks]]
- [[gantt]]
- [[portfolios]]
- [[approvals]]
