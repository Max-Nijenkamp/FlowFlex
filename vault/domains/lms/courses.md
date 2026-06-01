---
type: module
domain: Learning & Development
panel: lms
module-key: lms.courses
status: planned
color: "#4ADE80"
---

# Course Builder

Create courses with modules and lessons. The container structure for all learning content.

## Core Features

- Course record: title, slug, description, category, thumbnail, status, instructor
- Course structure: modules → lessons (nested)
- Status: draft / published / archived
- Enrolment type: open, invite-only, mandatory (assigned)
- Prerequisites: courses that must be completed first
- Estimated duration
- Course categories
- Certificate on completion (links to Certifications)
- Internal (employees) and external (customers/learners) audiences

## Data Model

| Table | Key Columns |
|---|---|
| `lms_courses` | company_id, title, slug, description, category, instructor_id, status, enrolment_type, estimated_minutes, certificate_template_id |
| `lms_course_modules` | course_id, company_id, title, order |

## Filament

**Nav group:** Courses

- `CourseResource` — create, edit, structure builder (modules + lessons), publish
- `CourseBuilderPage` (custom page) — drag-and-drop module/lesson ordering

## Related

- [[domains/lms/lessons]]
- [[domains/lms/enrolments]]
- [[domains/lms/certifications]]
