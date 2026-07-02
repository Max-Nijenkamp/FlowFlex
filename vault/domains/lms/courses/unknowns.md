---
domain: lms
module: courses
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Courses — Unknowns

## Assumed Items

- `audience = both` value in the enum is *(assumed)* — source lists internal/external and marks "both" assumed.
- Publish gate "≥ 1 lesson" is *(assumed)* — not explicitly ratified.
- Status is a plain string field, not a `spatie/laravel-model-states` machine *(assumed)*.

## Open Questions

- Should completing a course fire a cross-domain event so **HR** (e.g. [[../../hr/performance-reviews/_module|performance]] or a training record) can react? Currently completion is LMS-internal only — no `CourseCompleted` event. (See [[decisions]].)
- Can a course belong to more than one category (tags vs single string)?
- Should archived courses auto-unenrol in-progress learners, or let them finish?
- Is there a per-course "recertification interval" that forces re-enrolment (compliance), or is that purely a certifications concern?
