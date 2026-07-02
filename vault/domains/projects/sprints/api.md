---
domain: projects
module: sprints
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Sprints — API / DTOs

## Input DTOs

### CreateSprintData
`project_id`, `name` (required), `goal?`, `start_date`/`end_date` (end after start).

### CompleteSprintData
`sprint_id`, `incomplete_action` (in: backlog, next-sprint), `next_sprint_id` (required_if next-sprint).

### AssignTaskData
`sprint_id`, `task_id` (same project, not in another active sprint), `story_points?`.

## Output

### SprintData
`id, name, goal, status, start_date, end_date, task_count, points_total, points_done, velocity`.

## Public / Portal Endpoints

None.
