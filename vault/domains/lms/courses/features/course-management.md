---
domain: lms
module: courses
feature: course-management
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Course Management

Create, edit, publish, and archive courses and their category/prerequisite metadata.

## Behaviour

- Course lifecycle: `draft → published → archived` (see [[../architecture]]).
- Publish is gated: the course must have ≥ 1 lesson *(assumed)* and no prerequisite cycle.
- Draft courses are invisible to learners; only `published` courses appear in the enrolments portal.
- Prerequisites are course ids, cycle-checked on write.

## UI

- **Kind**: simple-resource
- **Page**: "Courses" (`CourseResource`, `/lms/courses`)
- **Layout**: table (title, category, status badge, instructor, enrolment count) + section form (details, audience, enrolment type, prerequisites multi-select, certificate template, thumbnail upload). Modules managed via a relation manager / the builder page.
- **Key interactions**: create/edit form; "Publish" row action (guarded, disabled if no lessons); status + category table filters; archive action.
- **States**: empty (no courses → "Create your first course" CTA teaching the course→lesson→enrol flow) · loading (skeleton table) · error (publish rejected → validation toast: "Add at least one lesson before publishing") · selected (row → edit).
- **Gating**: view `lms.courses.view-any`; create `lms.courses.create`; edit `lms.courses.update`; publish `lms.courses.publish`.

## Data

- Owns / writes: `lms_courses`, `lms_course_modules` only.
- Reads: core.files (thumbnail signed URLs).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: `CourseService::prerequisitesMet` read by [[../../enrolments/_module|enrolments]] at enrol time.
- Shared entity: certificate template owned by [[../../certifications/_module|certifications]] (read-only reference by id).

## Unknowns

- Publish "≥ 1 lesson" gate is *(assumed)* — see [[../unknowns]].

## Related

- [[../_module|Course Builder module]] · [[course-builder]] · [[../architecture]] · [[../data-model]]
