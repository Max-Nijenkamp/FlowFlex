---
domain: lms
module: enrolments
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Enrolments

Enrol learners in courses, track progress, and manage completion. Handles both internal employees and external learners. Owns the learner portal surface.

## Module-key

| Field | Value |
|---|---|
| key | `lms.enrolments` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.enrolments` |
| tables | `lms_enrolments`, `lms_learners` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../courses/_module\|Courses]] + [[../lessons/_module\|Lessons]] | What learners enrol in; progress from lesson completion |
| Hard | [[../../core/billing/_module\|Billing]] + [[../../core/rbac/_module\|RBAC]] + [[../../core/notifications/_module\|Notifications]] | Gating, permissions, due-date reminders |
| Soft | [[../../hr/employee-profiles/_module\|HR Profiles]] | Employee learners + auto-enrol on hire |
| Soft | [[../certifications/_module\|Certifications]] / [[../skills-matrix/_module\|Skills]] | Completion side effects (direct same-domain calls) |

## Core Features

- **Enrolment record** — learner, course, status, progress %, started/completed dates.
- **State machine** — `enrolled → in_progress → completed | dropped`.
- **Progress** — completed lessons / total lessons, recomputed on lesson completion.
- **Mandatory assignment** — auto-enrol by role/department; `EmployeeHired` → mandatory onboarding courses.
- **Due dates + reminders** for mandatory courses.
- **Completion side effects** — certificate issue + skill raise (direct calls when modules active).
- **Learner types** — employee (User) or external (`lms_learners`, portal login via scoped guard).
- **Re-enrolment** for recurring training (new row, history kept).

## See features/

- [[features/enrolment-management|Enrolment Management]] — admin enrol/track/compliance (simple-resource).
- [[features/learner-portal|Learner Portal]] — `/learn` my-courses + lesson player (public-vue).
- [[features/auto-enrol-on-hire|Auto-Enrol on Hire]] — `EmployeeHired` listener (background).

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Learner-portal scope: learner sees own enrolments only (token + user paths).
- [ ] Prerequisite-unmet enrol rejected; duplicate active rejected.
- [ ] Progress math = completed/total lessons; 100% → completed + side effects (when modules active).
- [ ] `EmployeeHired` auto-enrols mandatory courses once.
- [ ] Due reminder once at 7d window.
- [ ] Re-enrolment after completion allowed.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Consumes | `EmployeeHired` | hr.profiles | `AutoEnrolOnHireListener` enrols mandatory internal courses (once) |
| Commands | `CertificateService::issue` | lms.certifications | On completion (same-domain direct call) |
| Commands | `SkillService::raiseFromCourse` | lms.skills | On completion (same-domain direct call) |
| Commands | `PathService::onCourseCompleted` | lms.paths | On completion (same-domain direct call) |
| Reads | `CourseService::prerequisitesMet` | lms.courses | At enrol time |
| Reads | `NotificationService` | core.notifications | Due-date reminders |

**Data ownership:** `lms.enrolments` writes only `lms_enrolments` + `lms_learners`. It **reads** HR profiles (never writes hr tables) and reacts to `EmployeeHired` by writing its own enrolment rows. Completion side effects call each sibling module's own service ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../courses/_module|Courses]] · [[../certifications/_module|Certifications]] · [[../../hr/onboarding/_module|HR Onboarding]]
- [[../../../architecture/event-bus]] · [[../_index|LMS index]]
