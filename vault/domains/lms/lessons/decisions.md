---
domain: lms
module: lessons
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Lessons — Decisions

## ADR: Quizzes graded server-side, answers never leave the server

- **Context:** Client-side grading leaks answer keys.
- **Decision:** `QuizService::grade` scores on the server; `lms_quizzes.questions[].correct` is stripped from every learner-facing payload. Only `QuizResult` returns.
- **Consequences:** The correctness of the quiz can't be gamed from the browser; adds a server round-trip per submission (acceptable).

## ADR: Content is a per-type validated jsonb blob

- **Context:** Four lesson types with different shapes.
- **Decision:** One `content` jsonb column, schema-validated per `type` in `CreateLessonData` (rather than four tables). Embed URLs whitelisted to youtube/vimeo.
- **Consequences:** Fewer tables; validation logic lives in the DTO; SCORM would extend the type set later *(assumed)*.

## ADR: Completion recompute is a same-domain direct call

- **Context:** Completing a lesson must update enrolment progress.
- **Decision:** `LessonProgressService::complete` calls `EnrolmentService::recomputeProgress` directly (same domain) — no event. Enrolments writes `lms_enrolments`; lessons writes only its own tables.
- **Consequences:** Simple, synchronous; respects data-ownership (each service writes its own tables).

## ADR: Best quiz score retained across attempts

- **Decision:** `lms_lesson_progress.quiz_score` keeps the best of all attempts; `attempts` counts tries.
- **Consequences:** Re-attempts can only improve the recorded score; max-attempts default is unlimited *(assumed)* — see [[unknowns]].
