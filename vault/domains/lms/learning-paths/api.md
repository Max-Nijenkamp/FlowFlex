---
domain: lms
module: learning-paths
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Learning Paths — API / DTOs

## `CreatePathData`

| Field | Type | Rules |
|---|---|---|
| `title` | string | required |
| `description` | text | nullable |
| `course_ids` | array | ordered, min:1, all published |
| `sequential` | bool | required |
| `certificate_template_id` | ulid | nullable, exists in company |

## `EnrolPathData`

| Field | Type | Rules |
|---|---|---|
| `path_id` | ulid | required, exists in company |
| `learner` | object | `{type, id}`; no active path enrolment for the pair |

## Endpoints

- No public routes. Learner path view is served by the enrolments portal. Path management is `LearningPathResource`; enrolment is `PathService::enrol`.
