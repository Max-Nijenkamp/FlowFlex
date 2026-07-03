---
domain: projects
module: projects
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Projects — API / DTOs

## Input

### CreateProjectData

| Field | Type | Rules |
|---|---|---|
| `name` | string | required, max:200 |
| `description` | text | nullable |
| `start_date` / `target_date` | date | target ≥ start — "Target date must be on or after the start date." |
| `owner_id` | ulid | in company users |
| `client_account_id` | ulid | nullable, resolved via CRM read API |
| `estimated_minutes` | int | nullable, min:0 |
| `estimated_cost_cents` | bigint | nullable, min:0 |
| `member_ids` | ulid[] | company users |

## Output

### ProjectData

`id, name, status, start_date, target_date, owner_name, client_name, progress_percent, health (on-track/at-risk/off-track), estimated_minutes, actual_hours, estimated_cost_cents, actual_cost_cents`.

## Public / Portal Endpoints

None in v1. Projects is an internal panel resource. A client-facing project portal is a candidate — see [[unknowns]].
