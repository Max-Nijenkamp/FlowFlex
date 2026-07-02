---
domain: lms
module: learning-paths
feature: path-builder
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Path Builder

Create a learning path, order its courses, choose sequential vs parallel, and bulk-assign learners.

## Behaviour

- A path (`CreatePathData`) holds an ordered list of published courses, a `sequential` toggle, and an optional certificate template.
- Courses are ordered via a repeater; only published courses are selectable.
- Bulk assignment enrols a set of learners into the path (`PathService::enrol`).

## UI

- **Kind**: simple-resource
- **Page**: "Learning Paths" (`LearningPathResource`, `/lms/paths`)
- **Layout**: table (title, course count, sequential badge, enrolled count) + form (details, ordered course repeater, sequential toggle, certificate template) + bulk-assign action + progress columns.
- **Key interactions**: add/reorder courses in the repeater; toggle sequential; bulk-assign learners; view per-path progress.
- **States**: empty (no paths → "Build your first path") · loading (skeleton) · error (unpublished course / duplicate active enrolment → validation) · selected (row → edit).
- **Gating**: view `lms.paths.view-any`; manage `lms.paths.manage`; assign `lms.paths.enrol`.

## Data

- Owns / writes: `lms_paths`, `lms_path_courses`, `lms_path_enrolments`.
- Reads: published courses (courses); certificate template (certifications).
- Cross-domain writes: NONE — course enrolments created via `EnrolmentService`.

## Relations

- Consumes: nothing.
- Feeds: path enrolments drive course enrolments (via enrolments service).
- Shared entity: courses, certificate template.

## Unknowns

- Branching/elective paths and auto role-assignment are open — see [[../unknowns]].

## Related

- [[../_module|Learning Paths module]] · [[path-progression]] · [[../architecture]]
