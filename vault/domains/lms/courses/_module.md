---
domain: lms
module: courses
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Course Builder

Create courses with modules and lessons — the container structure for all learning content. The LMS anchor; build first in `/lms`.

## Module-key

| Field | Value |
|---|---|
| key | `lms.courses` |
| priority | p3 |
| panel | lms |
| permission-prefix | `lms.courses` |
| tables | `lms_courses`, `lms_course_modules` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing/_module\|Billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|RBAC]] | Permissions, `canAccess()` |
| Hard | [[../../core/file-storage/_module\|File Storage]] | Course thumbnails |
| Soft | [[../lessons/_module\|Lessons]] | Content lives in course modules |
| Soft | [[../enrolments/_module\|Enrolments]] | Learners enrol in courses |
| Soft | [[../certifications/_module\|Certifications]] | Certificate template on completion |

## Core Features

- **Course record** — title, slug, description (purified), category, thumbnail, status, instructor, audience.
- **Course structure** — modules → lessons (nested), drag-drop ordered.
- **Publish workflow** — draft → published → archived; publish requires ≥ 1 lesson *(assumed)*.
- **Enrolment type** — open / invite-only / mandatory (assigned).
- **Prerequisites** — courses that must complete first, cycle-checked.
- **Audiences** — internal (employees) and/or external (customers/learners).

## See features/

- [[features/course-management|Course Management]] — the course CRUD + publish resource (simple-resource).
- [[features/course-builder|Course Builder]] — drag-drop module/lesson ordering (custom-page).

## Build Manifest

```
database/migrations/xxxx_create_lms_courses_table.php
database/migrations/xxxx_create_lms_course_modules_table.php
app/Models/LMS/{Course,CourseModule}.php
app/Data/LMS/CreateCourseData.php
app/Services/LMS/CourseService.php
app/Providers/LMS/LmsServiceProvider.php
app/Filament/LMS/Resources/CourseResource.php
app/Filament/LMS/Pages/CourseBuilderPage.php
database/factories/LMS/{CourseFactory,CourseModuleFactory}.php
tests/Feature/LMS/CourseTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Publish without lessons rejected.
- [ ] Prerequisite cycle rejected.
- [ ] Draft invisible to learners.
- [ ] Module ordering persists.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | *(none)* | — | Completion side effects live in [[../enrolments/_module\|enrolments]], not here |
| Reads | file-storage signed URLs | core.files | Course thumbnail upload/serve |
| Reads (by) | `CourseService::prerequisitesMet` | lms.enrolments | Enrolments call this at enrol time |

**Data ownership:** `lms.courses` writes only `lms_courses` + `lms_course_modules`. It reads thumbnails via core.files' service and never writes another domain's tables ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../lessons/_module|Lessons]] · [[../enrolments/_module|Enrolments]] · [[../certifications/_module|Certifications]]
- [[../_index|LMS index]]
