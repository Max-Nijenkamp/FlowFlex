---
domain: workplace
module: maintenance
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Facility Maintenance — API / DTOs

## `ReportMaintenanceData` (input)

| Field | Type | Rules |
|---|---|---|
| `location` | string | required |
| `category` | enum | in: HVAC, electrical, plumbing, cleaning, furniture, safety |
| `description` | text | required |
| `priority` | enum | urgent / high / normal / low |
| `photos` | file[] | nullable, image MIME, size cap |

## `CreateScheduleData` (input)

| Field | Type | Rules |
|---|---|---|
| `location` | string | required |
| `task` | string | required |
| `category` | enum | in set |
| `frequency` | enum | weekly / monthly / quarterly |
| `next_due_at` | date | required, future |

## Actions

- `AssignMaintenanceAction(request, assignee|contractor)` — `reported → assigned`, notify.
- `ResolveMaintenanceAction(request)` — `in_progress → resolved`, notify reporter.

## Public / Portal Endpoints

None. Internal `/workplace` panel only.
