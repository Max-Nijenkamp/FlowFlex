---
domain: lms
module: courses
feature: course-builder
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Course Builder

A drag-and-drop workspace for structuring a course — arranging modules and the lessons within them.

## Behaviour

- Modules and their lessons are reordered by drag; `order` columns persist immediately.
- Add / rename / remove modules inline.
- Lessons are added within a module (the lesson forms belong to [[../../lessons/_module|lessons]]).
- The builder is the natural place to see whether a course has enough content to publish.

## UI

- **Kind**: custom-page
- **Page**: "Course Builder" (`CourseBuilderPage`, `/lms/courses/{course}/build`)
- **Layout**: left rail = module list (draggable); each module expands to its ordered lessons (draggable within/between modules); right = add-module / add-lesson affordances.
- **Key interactions**: drag module → reorder → optimistic move + persist `order`; drag lesson between modules → reassign `module_id` + `order`; inline add/rename; "Publish" surfaced when ≥ 1 lesson.
- **States**: empty (no modules → "Add your first module") · loading (skeleton rails) · error (reorder failed → revert + toast) · selected (dragged card highlighted).
- **Gating**: `lms.courses.update` (reorder/structure); publish requires `lms.courses.publish`.

## Data

- Owns / writes: `lms_course_modules` (`order`, `title`), `lms_courses.status` on publish. Lesson `module_id` / `order` reassignment is written through the [[../../lessons/_module|lessons]] service (its own table).
- Reads: `lms_lessons` (via lessons service) to render the tree.
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: nothing (structure is read by the enrolments portal to render course navigation).
- Shared entity: lessons are owned by [[../../lessons/_module|lessons]] — the builder arranges them but the lesson rows/content belong to that module.

## Unknowns

- Whether module-level completion criteria exist, or only lesson-level, is undocumented — see [[../unknowns]].

## Related

- [[../_module|Course Builder module]] · [[course-management]] · [[../../lessons/_module|Lessons]] · [[../architecture]]
