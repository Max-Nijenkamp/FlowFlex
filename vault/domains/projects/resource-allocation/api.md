---
domain: projects
module: resource-allocation
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Resource Allocation — API / DTOs

## Input

### CreateAllocationData
`user_id`, `project_id`, `allocation_percent` (1–100), `start_date`/`end_date` (end ≥ start). Over-100% across overlapping ranges = **warning flag in response, not rejection**.

## Output

### AllocationData
`id, user_name, project_name, allocation_percent, start_date, end_date, over_allocated (bool)`.

### Utilisation
`utilisation(userId, from, to)` → `{ planned: float, actual: float }` (actual from time entries when active).

## Public / Portal Endpoints

None.
