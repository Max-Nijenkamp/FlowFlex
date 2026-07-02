---
domain: projects
module: milestones
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Milestones — API / DTOs

## Input DTOs

### CreateMilestoneData
`project_id`, `title` (required), `description?`, `target_date` (required), `task_ids[]` (same project).

### AchieveMilestoneData
`milestone_id`, `achieved_date` (≤ today), `notes?`.

## Output

### MilestoneData
`id, title, target_date, achieved_date, status, progress_percent, linked_task_count, project_id`.

## Public / Portal Endpoints

None.
