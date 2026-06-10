---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.paths
status: planned
priority: p3
depends-on: [lms.courses, lms.enrolments, core.billing, core.rbac]
soft-depends: [lms.certifications]
fires-events: []
consumes-events: []
patterns: []
tables: [lms_paths, lms_path_courses, lms_path_enrolments]
permission-prefix: lms.paths
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Learning Paths

Sequenced collections of courses forming a structured curriculum (e.g. "New Manager Programme"). Learners progress through courses in order.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/lms/courses\|lms.courses]] + [[domains/lms/enrolments\|lms.enrolments]] | paths contain courses; path enrolment creates course enrolments |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/lms/certifications\|lms.certifications]] | path completion certificate |

---

## Core Features

- Path record: title, description, ordered list of published courses
- Sequential unlock: next course unlocks when previous completed (per-path toggle)
- Path enrolment: enrols learner in first course (sequential) or all courses (parallel)
- Path progress: courses completed / total
- Role-based path assignment (manual bulk v1 *(assumed)*)
- Path completion certificate
- Visual path builder (ordered list)

---

## Data Model

### lms_paths — id, company_id (indexed), title, description, sequential (bool), certificate_template_id nullable, deleted_at
### lms_path_courses — id, path_id FK, course_id FK, company_id, order; unique `(path_id, course_id)`
### lms_path_enrolments — id, path_id FK, company_id, learner_type/learner_id, progress_percent, completed_at nullable; unique active per (path, learner)

---

## DTOs

### CreatePathData — title, course_ids[] ordered min:1 (published), sequential, certificate_template_id?
### EnrolPathData — path_id, learner ref (no active path enrolment)

## Services & Actions

- `PathService::enrol(EnrolPathData)` — sequential: first course only; parallel: all
- `PathService::onCourseCompleted(enrolment)` — hook from EnrolmentService: unlock/enrol next course, recompute path progress, issue path certificate at 100%

---

## Filament

**Nav group:** Courses

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `LearningPathResource` | #1 CRUD resource | ordered course repeater, bulk assign, progress columns |

Learner path view: portal pages.

---

## Permissions

`lms.paths.view-any` · `lms.paths.manage` · `lms.paths.enrol`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Sequential: next course enrolment created only after previous completes
- [ ] Parallel: all enrolments at once
- [ ] Path progress math; 100% → path certificate (when template set)
- [ ] Duplicate active path enrolment rejected
- [ ] Unpublished course rejected from path

---

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

---

## Related

- [[domains/lms/courses]]
- [[domains/lms/enrolments]]
