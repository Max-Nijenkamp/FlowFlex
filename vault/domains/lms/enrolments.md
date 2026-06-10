---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.enrolments
status: planned
priority: p3
depends-on: [lms.courses, lms.lessons, core.billing, core.rbac, core.notifications]
soft-depends: [hr.profiles, lms.certifications, lms.skills]
fires-events: []
consumes-events: [EmployeeHired]
patterns: [states, events]
tables: [lms_enrolments, lms_learners]
permission-prefix: lms.enrolments
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Enrolments

Enrol learners in courses, track progress, and manage completion. Handles both internal employees and external learners. Owns the learner portal surface.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/lms/courses\|lms.courses]] + [[domains/lms/lessons\|lms.lessons]] | what learners enrol in; progress from lesson completion |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, due-date reminders |
| Soft | [[domains/hr/employee-profiles\|hr.profiles]] | employee learners + auto-enrol on hire |
| Soft | certifications / skills | completion side effects (direct same-domain calls — `CourseCompleted` event from v1 specs dropped *(assumed)*) |

---

## Core Features

- Enrolment record: learner, course, status, progress %, started/completed dates
- Status machine: `enrolled → in_progress → completed | dropped`
- Progress calculation: completed lessons / total lessons (recomputed by lesson progress)
- Mandatory course assignment: auto-enrol employees by role/department; `EmployeeHired` → mandatory onboarding courses
- Due dates for mandatory courses + reminders
- Completion side effects: certificate issue + skill raise (direct calls when modules active)
- Bulk enrolment
- Learner = employee (User) or external learner (`lms_learners` record; portal login via signed magic link *(assumed)*)
- Re-enrolment for recurring training (new enrolment row, history kept)
- Prerequisite check at enrol (`CourseService::prerequisitesMet`)

---

## Data Model

### lms_enrolments

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), course_id FK | ulid | |
| learner_type / learner_id | string / ulid | employee / external |
| status | string default `enrolled` | state machine |
| progress_percent | decimal(5,2) default 0 | |
| due_date | date nullable | mandatory |
| started_at / completed_at | timestamp nullable | |
| reminded_at | timestamp nullable | due reminder guard |

Unique active `(course_id, learner_type, learner_id)`.

### lms_learners — id, company_id (indexed), name, email (unique per company), portal_token uuid, deleted_at

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `enrolled` | `in_progress` | first lesson activity | started_at |
| `in_progress` | `completed` | progress = 100% | certificate + skills (soft, direct); notification |
| any | `dropped` | learner/admin | |

---

## DTOs

### EnrolData — course_id (published; prerequisites met — "Complete the prerequisite courses first."), learner {type, id} (no active enrolment), due_date? (mandatory)
### BulkEnrolData — course_id, learner refs[] — per-row try/catch result

## Services & Actions

Interface→Service: `EnrolmentServiceInterface` → `EnrolmentService`.

- `enrol(EnrolData)` / `bulkEnrol(...)`
- `recomputeProgress(enrolmentId)` — called by LessonProgressService; completion triggers side effects
- Listener `AutoEnrolOnHireListener` on `EmployeeHired` — mandatory courses with audience internal (per [[architecture/event-bus]]; no-op without mandatory courses)
- `DueDateReminderCommand`

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `DueDateReminderCommand` | notifications | daily | `reminded_at` guard, 7d window |

---

## Filament

**Nav group:** Enrolments

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EnrolmentResource` | #1 CRUD resource | course/status filters, bulk enrol, compliance tab (mandatory overdue) |
| `EnrolmentProgressWidget` | #6 widget | completion rates |

Learner portal: Vue + Inertia `/learn` (ui-strategy row #15) — my courses, lesson player, progress.

---

## Permissions

`lms.enrolments.view-any` · `lms.enrolments.enrol` · `lms.enrolments.manage` (+ learner self-access via portal token/user link)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Learner-portal scope: learner sees own enrolments only (token + user paths)
- [ ] Prerequisite-unmet enrol rejected; duplicate active rejected
- [ ] Progress math = completed/total lessons; 100% → completed + side effects (when modules active)
- [ ] `EmployeeHired` auto-enrols mandatory courses once
- [ ] Due reminder once at 7d window
- [ ] Re-enrolment after completion allowed

---

## Build Manifest

```
database/migrations/xxxx_create_lms_learners_table.php
database/migrations/xxxx_create_lms_enrolments_table.php
app/Models/LMS/{Enrolment,Learner}.php
app/States/LMS/Enrolment/{EnrolmentState,Enrolled,InProgress,Completed,Dropped}.php
app/Data/LMS/{EnrolData,BulkEnrolData}.php
app/Contracts/LMS/EnrolmentServiceInterface.php
app/Services/LMS/EnrolmentService.php
app/Listeners/LMS/AutoEnrolOnHireListener.php
app/Console/Commands/LMS/DueDateReminderCommand.php
app/Http/Controllers/LearnerPortalController.php + resources/js/Pages/Learn/{Dashboard,Course,Lesson}.vue
app/Filament/LMS/Resources/EnrolmentResource.php
app/Filament/LMS/Widgets/EnrolmentProgressWidget.php
database/factories/LMS/{EnrolmentFactory,LearnerFactory}.php
tests/Feature/LMS/{EnrolmentTest,LearnerPortalScopeTest,AutoEnrolTest}.php
```

---

## Related

- [[domains/lms/courses]]
- [[domains/lms/certifications]]
- [[domains/hr/onboarding]]
- [[architecture/event-bus]]
