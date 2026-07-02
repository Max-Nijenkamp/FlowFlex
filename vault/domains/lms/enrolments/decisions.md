---
domain: lms
module: enrolments
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Enrolments — Decisions

## ADR: Completion side effects are same-domain direct calls, not a `CourseCompleted` event

- **Context:** v1 specs defined `CourseCompleted` firing to certifications/skills/paths (and potentially HR).
- **Decision:** The event was dropped. On `completed`, `EnrolmentService` calls `CertificateService::issue`, `SkillService::raiseFromCourse`, `PathService::onCourseCompleted` directly (all same LMS domain). Each callee writes only its own tables.
- **Consequences:** Simpler within LMS; **HR integration** (feeding completions into HR training records / performance) is now an open cross-domain question — see [[unknowns]].

## ADR: External learners use a scoped portal guard, not ad-hoc tokens

- **Context:** External learners log into `/learn` without a full user account.
- **Decision:** A Sanctum scoped **learner guard** authenticates the portal; `lms_learners.portal_token` issuance/rotation flows through it. (Security-audit HIGH finding.)
- **Consequences:** Token handling is centralised + rotatable; own-data scope is enforceable and testable.

## ADR: State machine via `spatie/laravel-model-states`

- **Decision:** `enrolled → in_progress → completed | dropped` as state classes (not a string field).
- **Consequences:** Transitions and their side effects are explicit and guarded; `completed` is the single side-effect hook.

## ADR: Re-enrolment allowed for recurring training

- **Decision:** Uniqueness is on the **active** `(course, learner)` pair only; a completed/dropped enrolment can be re-created (new row, history kept).
- **Consequences:** Supports annual/recurring compliance without deleting history; the "renewal path" for certifications.

## ADR: Auto-enrol on hire is idempotent

- **Decision:** `AutoEnrolOnHireListener` enrols mandatory internal-audience courses exactly once per new hire; no-op if none exist or already enrolled.
- **Consequences:** Safe against event re-delivery; degrades cleanly when HR is absent (soft dep).
