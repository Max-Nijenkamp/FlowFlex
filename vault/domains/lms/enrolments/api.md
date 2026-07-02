---
domain: lms
module: enrolments
type: api
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Enrolments — API / DTOs

## `EnrolData`

| Field | Type | Rules |
|---|---|---|
| `course_id` | ulid | required, published; prerequisites met ("Complete the prerequisite courses first.") |
| `learner` | object | `{type: employee\|external, id}`; no active enrolment for the pair |
| `due_date` | date | nullable (mandatory courses) |

## `BulkEnrolData`

| Field | Type | Rules |
|---|---|---|
| `course_id` | ulid | required, published |
| `learners` | array | learner refs; per-row try/catch result |

- Bulk enrol is **rate-limited** (per-user throttle) on the Filament action.

## Learner Portal Endpoints (`/learn`, scoped learner guard)

| Route | Purpose |
|---|---|
| `GET /learn` | Learner dashboard — own enrolments + progress |
| `GET /learn/courses/{course}` | Course overview (own enrolment only) |
| `GET /learn/lessons/{lesson}` | Lesson player; marks progress via `LessonProgressService` |
| `POST /learn/quiz/{lesson}` | Submit quiz (`SubmitQuizData` → `QuizService::grade`) |

- All routes scope strictly to the authenticated learner (employee user link **or** `lms_learners.portal_token`). Cross-learner access returns 403/404.

## Output

- `EnrolmentProgressData` (widget/portal): course, progress %, status, due date, completed lessons / total.
