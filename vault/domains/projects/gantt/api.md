---
domain: projects
module: gantt
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Gantt — API / DTOs

## Input

No create DTO. Reschedule/resize reuse **projects.tasks** `UpdateTaskAction` (due date / estimated hours). Gantt param: `projectId`, `zoom` (day/week/month).

## Output

### GanttData
`tasks[]` (id, title, start, end, progress, dependencies[]), `milestones[]` (id, title, date), `critical_path_ids[]`. Single query set.

## Public / Portal Endpoints

None.
