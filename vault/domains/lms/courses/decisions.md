---
domain: lms
module: courses
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Courses — Decisions

## ADR: Publish requires ≥ 1 lesson (assumed)

- **Context:** An empty course should not be enrolable.
- **Decision:** `CourseService::publish` refuses when the course has no lessons across its modules *(assumed)*.
- **Consequences:** Learners never hit a content-less course; draft is the authoring state.

## ADR: Prerequisites are cycle-checked at write

- **Context:** Courses can require other courses first.
- **Decision:** `prerequisites` (jsonb of course ids) is validated for cycles both on write (`CreateCourseData`) and at publish. `CourseService::prerequisitesMet` is the read used by enrolments.
- **Consequences:** No infinite prerequisite loops; enrolment can block on unmet prerequisites.

## ADR: Completion side effects are same-domain direct calls, not events (assumed)

- **Context:** v1 specs defined a `CourseCompleted` cross-domain event.
- **Decision:** The event was dropped; certificate issue, skill raise, and path advance are direct calls from `EnrolmentService` within the LMS domain *(assumed)*. Courses fires nothing.
- **Consequences:** Simpler wiring inside LMS; cross-domain (HR) reactions to completion remain an open question — see [[unknowns]].

## ADR: Slug uniqueness is per-company

- **Decision:** Course slugs are unique per `company_id`, not global (`spatie/laravel-sluggable`).
- **Consequences:** Two tenants can both own a "leadership-101" slug without collision.
