---
domain: lms
module: learning-paths
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Learning Paths — Decisions

## ADR: Path enrolment creates course enrolments through `EnrolmentService`

- **Context:** A path is a sequence of courses; enrolling in a path must enrol in its courses.
- **Decision:** `PathService::enrol` calls `EnrolmentService::enrol` (sequential → first course, parallel → all) — it never writes `lms_enrolments` directly.
- **Consequences:** Respects data-ownership (enrolments owns its table); prerequisite/duplicate guards are reused.

## ADR: Sequential unlock driven by the completion hook, not events

- **Context:** In sequential paths, the next course unlocks on completing the previous.
- **Decision:** `EnrolmentService` calls `PathService::onCourseCompleted` on completion (same-domain); the hook enrols the next course and recomputes path progress.
- **Consequences:** No cross-domain event; deterministic advancement. Parallel paths ignore ordering.

## ADR: Only published courses allowed in a path

- **Decision:** `CreatePathData` rejects unpublished course ids.
- **Consequences:** A path can never enrol a learner into draft content.

## ADR: Path certificate at 100%

- **Decision:** When a path reaches 100% and has `certificate_template_id`, `PathService` calls `CertificateService::issue` for the path.
- **Consequences:** Programme-level certification distinct from per-course certificates.
