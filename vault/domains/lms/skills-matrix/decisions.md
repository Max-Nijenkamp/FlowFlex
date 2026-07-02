---
domain: lms
module: skills-matrix
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Skills Matrix — Decisions

## ADR: Self and manager assessments stored separately; manager authoritative

- **Context:** Self-assessment and manager-assessment often disagree.
- **Decision:** One `lms_employee_skills` row per `(employee, skill, assessor_type)`. Both values are kept; the **manager** value drives gap analysis *(assumed)*.
- **Consequences:** Transparent self-vs-manager delta; gaps never inflated by optimistic self-scores.

## ADR: Course completion raises, never lowers, proficiency

- **Context:** Completing a course teaching a skill should credit the learner.
- **Decision:** `SkillService::raiseFromCourse` sets `max(current, taught_level)` per linked skill. A course can only raise a level.
- **Consequences:** Skills don't regress from taking a lower-level course; called same-domain from `EnrolmentService`.

## ADR: Manager scope derives from HR reporting line (read-only)

- **Context:** "Assess my reports" needs a source of truth for who reports to whom.
- **Decision:** The reporting line is read from HR ([[../../hr/employee-profiles/_module|profiles]]); skills never writes HR data.
- **Consequences:** Respects data-ownership; skills degrades if HR is inactive (hard dep, so it isn't).

## ADR: Proficiency as a 0–3 integer enum

- **Decision:** none/beginner/intermediate/expert as `0–3`, shared across employee, role-required, and course-taught levels.
- **Consequences:** Gap math is a simple `required − actual`; heat-map colours map to four buckets.
