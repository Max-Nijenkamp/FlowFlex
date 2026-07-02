---
domain: lms
module: learning-paths
feature: path-progression
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Path Progression

Advance a learner through a path as they complete its courses, and certify on 100%.

## Behaviour

- On enrol: sequential paths enrol only the first course; parallel paths enrol all.
- When a course completes, `EnrolmentService` calls `PathService::onCourseCompleted`:
  - Sequential → enrol the next course.
  - Recompute `progress_percent` (courses completed / total).
  - At 100% → stamp `completed_at`; issue a path certificate if a template is set.
- Duplicate active path enrolment is rejected.

## UI

- **Kind**: background  <!-- progression is a service hook; the learner sees it in the enrolments portal -->
- **Trigger**: `PathService::onCourseCompleted` (called by enrolments on course completion). Learner-facing progress renders in the [[../../enrolments/features/learner-portal|learner portal]]; admin sees progress columns on `LearningPathResource`.

## Data

- Owns / writes: `lms_path_enrolments` (`progress_percent`, `completed_at`).
- Reads: `lms_path_courses` (sequence), enrolment completion (enrolments).
- Cross-domain writes: NONE — next-course enrolment via `EnrolmentService`, certificate via `CertificateService`.

## Relations

- Consumes: nothing (invoked by enrolments' service).
- Feeds: next course enrolment; path certificate at 100%.
- Shared entity: course enrolments (enrolments), certificate (certifications).

## Unknowns

- Whether removing a course mid-path reflows in-progress enrolments — see [[../unknowns]].

## Related

- [[../_module|Learning Paths module]] · [[path-builder]] · [[../../enrolments/_module|Enrolments]]
