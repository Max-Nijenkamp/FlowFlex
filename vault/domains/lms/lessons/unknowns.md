---
domain: lms
module: lessons
type: unknowns
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Lessons — Unknowns

## Assumed Items

- SCORM/xAPI support is deferred *(assumed)* — only native types (video/text/file/quiz) in v1.
- Max quiz attempts default is unlimited *(assumed)*.
- `content` schema shapes are reconstructed from the flat spec's prose *(assumed)*.

## Open Questions

- Should quizzes support question types beyond multiple-choice / true-false (e.g. fill-in, ordering, weighted scoring)?
- Is there a per-lesson "minimum time on page" or scroll-depth gate for `viewed`, or is opening enough?
- Does video need resume-position tracking (watch %) as a completion signal, distinct from a boolean `viewed`?
- Should `max_attempts` be configurable per quiz, and what happens when exhausted (lock vs cooldown)?
- SCORM/xAPI import path — when, and does it change the `content` schema?
