---
type: module
domain: Learning & Development
panel: lms
module-key: lms.enrolments
status: planned
color: "#4ADE80"
---

# Enrolments

Enrol learners in courses, track progress, and manage completion. Handles both internal employees and external learners.

## Core Features

- Enrolment record: learner, course, status, progress %, started/completed dates
- Status machine: `enrolled → in_progress → completed | dropped`
- Progress calculation: completed lessons / total lessons
- Mandatory course assignment: auto-enrol employees by role/department
- Due dates for mandatory courses
- Completion certificate generation
- Bulk enrolment
- Learner can be an employee (User) or external learner (separate learner record)
- Re-enrolment for recurring training

## Data Model

| Table | Key Columns |
|---|---|
| `lms_enrolments` | company_id, course_id, learner_id, learner_type (employee/external), status, progress_percent, due_date, started_at, completed_at |
| `lms_learners` | company_id, name, email, type (for external learners) |

## Filament

**Nav group:** Enrolments

- `EnrolmentResource` — list (filter by course/status), enrol, bulk-enrol
- `EnrolmentProgressWidget` — completion rates
- Mandatory training compliance view

## Cross-Domain / Events

- Consumes `EmployeeHired` → auto-enrol in mandatory onboarding courses
- Fires `CourseCompleted` → Certifications, HR (skills)

## Related

- [[domains/lms/courses]]
- [[domains/lms/certifications]]
- [[domains/hr/onboarding]]
