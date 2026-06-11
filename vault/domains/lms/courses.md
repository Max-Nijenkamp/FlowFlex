---
type: module
domain: Learning & Development
domain-key: lms
panel: lms
module-key: lms.courses
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files]
soft-depends: [lms.lessons, lms.certifications, lms.enrolments]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [lms_courses, lms_course_modules]
permission-prefix: lms.courses
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Course Builder

Create courses with modules and lessons. The container structure for all learning content — the LMS anchor, build first in `/lms`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, thumbnails |
| Soft | [[domains/lms/lessons\|lms.lessons]], [[domains/lms/certifications\|lms.certifications]], [[domains/lms/enrolments\|lms.enrolments]] | content, certificates, learners |

---

## Core Features

- Course record: title, slug, description, category, thumbnail, status, instructor
- Course structure: modules → lessons (nested)
- Status: draft / published / archived (publish requires ≥ 1 lesson *(assumed)*)
- Enrolment type: open, invite-only, mandatory (assigned)
- Prerequisites: courses that must be completed first (cycle-checked)
- Estimated duration
- Course categories
- Certificate on completion (links Certifications)
- Internal (employees) and external (customers/learners) audiences

---

## Data Model

### lms_courses

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| slug | string | unique per company |
| description | text | purified |
| category | string nullable | |
| instructor_id | ulid nullable FK users | |
| status | string default `draft` | draft/published/archived |
| enrolment_type | string | open / invite / mandatory |
| audience | string default `internal` | internal / external / both *(assumed)* |
| prerequisites | jsonb default `[]` | course ids, cycle-checked |
| estimated_minutes | int nullable | |
| certificate_template_id | ulid nullable | |
| deleted_at | timestamp nullable | |

### lms_course_modules — id, course_id FK, company_id, title, order

---

## DTOs

### CreateCourseData — title, description (purified), category?, enrolment_type (in set), audience, prerequisites[] (existing courses, no cycle), estimated_minutes?, certificate_template_id?

## Services & Actions

- `CourseService::publish(...)` — requires ≥ 1 lesson
- `CourseService::prerequisitesMet(learnerId, courseId): bool` — enrolments check this

---

## Filament

**Nav group:** Courses

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CourseResource` | #1 CRUD resource | structure relation (modules), publish action |
| `CourseBuilderPage` | #3-style custom page | drag-drop module/lesson ordering |

Learner-facing course pages: Vue + Inertia learner portal (ui-strategy row #15; enrolments module surfaces it).


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('lms.courses.view-any') && BillingService::hasModule('lms.courses')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`lms.courses.view-any` · `lms.courses.create` · `lms.courses.update` · `lms.courses.publish`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Publish without lessons rejected
- [ ] Prerequisite cycle rejected
- [ ] Draft invisible to learners
- [ ] Module ordering persists

---

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

---

## Related

- [[domains/lms/lessons]]
- [[domains/lms/enrolments]]
- [[domains/lms/certifications]]
