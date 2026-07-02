---
domain: lms
module: skills-matrix
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Skills Matrix — API / DTOs

## `AssessSkillData`

| Field | Type | Rules |
|---|---|---|
| `employee_id` | ulid | self → own id; manager → a direct report |
| `skill_id` | ulid | required, exists in company |
| `proficiency_level` | int | in: 0,1,2,3 |

- Assessor type (self/manager) is resolved from the acting user vs `employee_id` + permission.

## `SetRoleSkillData`

| Field | Type | Rules |
|---|---|---|
| `role_name` | string | required |
| `skill_id` | ulid | required, exists in company |
| `required_level` | int | in: 0,1,2,3 |

## Read APIs (output)

| Method | Output |
|---|---|
| `gapAnalysis(employeeId)` | Collection of `{skill, required_level, actual_level, gap}` |
| `recommendations(employeeId)` | Collection of courses teaching gap skills |

## Endpoints

- No public routes. All surfaces are Filament (admin/manager/self scopes governed by permissions).
