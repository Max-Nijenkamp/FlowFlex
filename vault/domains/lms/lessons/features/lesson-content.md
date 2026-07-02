---
domain: lms
module: lessons
feature: lesson-content
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Lesson Content

Author lessons of each type (video, text, file) inside a course module.

## Behaviour

- A lesson has a `type` that determines its `content` shape and default completion criterion (see [[../architecture]]).
- Video: upload (core.files, signed URL) or embed (youtube/vimeo whitelist).
- Text: Tiptap rich text, purified (`ezyang/htmlpurifier`).
- File: uploaded downloadable resource.
- Ordering within a module persists via the [[../../courses/features/course-builder|course builder]].

## UI

- **Kind**: simple-resource  <!-- lesson relation manager on course modules -->
- **Page**: Lesson relation manager on `CourseResource` / modules (`/lms/courses/{course}` → module → lessons).
- **Layout**: table of lessons per module (title, type badge, duration, criterion) + type-switched form (video upload/embed field · Tiptap editor · file upload). Quiz lessons hand off to the [[quizzes|quiz builder]].
- **Key interactions**: add lesson → pick type → type-specific form; reorder within module; set completion criterion; upload validated client + server.
- **States**: empty (module has no lessons → "Add a lesson") · loading (upload progress) · error (non-whitelisted embed or oversized file → field error) · selected (row → edit).
- **Gating**: `lms.lessons.manage`.

## Data

- Owns / writes: `lms_lessons` (+ media refs held by core.files).
- Reads: core.files (upload/serve), `lms_course_modules` (parent, via courses).
- Cross-domain writes: NONE.

## Relations

- Consumes: nothing.
- Feeds: lesson list is read by the [[../../enrolments/_module|enrolments]] learner portal to render the player.
- Shared entity: uploaded media owned by core.files (referenced by id).

## Unknowns

- SCORM/xAPI and video watch-% tracking are open — see [[../unknowns]].

## Related

- [[../_module|Lessons module]] · [[quizzes]] · [[../../courses/features/course-builder|Course Builder]]
