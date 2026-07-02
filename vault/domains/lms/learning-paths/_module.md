---
domain: lms
module: learning-paths
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Learning Paths

Sequenced collections of courses forming a structured curriculum (e.g. "New Manager Programme"). Learners progress through courses in order.

## Module-key

| Field | Value |
|---|---|
| key | `lms.paths` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.paths` |
| tables | `lms_paths`, `lms_path_courses`, `lms_path_enrolments` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../courses/_module\|Courses]] + [[../enrolments/_module\|Enrolments]] | Paths contain courses; path enrolment creates course enrolments |
| Hard | [[../../core/billing/_module\|Billing]] + [[../../core/rbac/_module\|RBAC]] | Gating + permissions |
| Soft | [[../certifications/_module\|Certifications]] | Path-completion certificate |

## Core Features

- **Path record** — title, description, ordered list of published courses.
- **Sequential unlock** — next course unlocks when the previous completes (per-path toggle).
- **Path enrolment** — enrols learner in the first course (sequential) or all (parallel).
- **Path progress** — courses completed / total.
- **Role-based assignment** — manual bulk v1 *(assumed)*.
- **Path-completion certificate**.
- **Visual path builder** — ordered list.

## See features/

- [[features/path-builder|Path Builder]] — create/order paths + role assignment (simple-resource).
- [[features/path-progression|Path Progression]] — sequential unlock + completion hook (background).

## Build Manifest

```
database/migrations/xxxx_create_lms_paths_table.php
database/migrations/xxxx_create_lms_path_courses_table.php
database/migrations/xxxx_create_lms_path_enrolments_table.php
app/Models/LMS/{LearningPath,PathCourse,PathEnrolment}.php
app/Data/LMS/{CreatePathData,EnrolPathData}.php
app/Services/LMS/PathService.php
app/Filament/LMS/Resources/LearningPathResource.php
database/factories/LMS/LearningPathFactory.php
tests/Feature/LMS/LearningPathTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Sequential: next course enrolment created only after previous completes.
- [ ] Parallel: all enrolments at once.
- [ ] Path progress math; 100% → path certificate (when template set).
- [ ] Duplicate active path enrolment rejected.
- [ ] Unpublished course rejected from a path.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | `EnrolmentService::enrol` | lms.enrolments | Path enrol creates course enrolments (same-domain) |
| Commanded by | `PathService::onCourseCompleted` | lms.enrolments | Enrolment completion calls this to unlock/advance |
| Commands | `CertificateService::issue` | lms.certifications | Path-completion certificate (same-domain) |
| Reads | published course list | lms.courses | Only published courses allowed in a path |

**Data ownership:** `lms.paths` writes only `lms_paths`, `lms_path_courses`, `lms_path_enrolments`. Course enrolments are created through `EnrolmentService`, never by writing `lms_enrolments` directly ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../courses/_module|Courses]] · [[../enrolments/_module|Enrolments]] · [[../_index|LMS index]]
