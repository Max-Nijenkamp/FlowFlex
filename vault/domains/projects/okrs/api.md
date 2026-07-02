---
domain: projects
module: okrs
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# OKRs — API / DTOs

## Input DTOs

### CreateObjectiveData
`title` (required), `owner_id`, `quarter` (1–4), `year`, `parent_objective_id?` (no cycle, depth ≤ 4), `project_id?`, `description?`.

### CheckInData
`key_result_id` (own or `projects.okrs.update-any`), `current_value` (numeric), `notes?`.

## Output

### ObjectiveData
`id, title, owner_name, quarter, year, progress_percent, health, key_results[], parent_id, depth`.

## Public / Portal Endpoints

None.
