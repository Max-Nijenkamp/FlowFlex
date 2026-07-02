---
domain: hr
module: onboarding
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Onboarding — Unknowns & Assumptions

Carried `*(assumed)*` markers and unverified items from the source spec.

- `*(assumed)*` `is_default` — one default template per company (uniqueness/enforcement not specified).
- `*(assumed)*` `hr_onboarding_tasks.due_days_after_start` — relative due dates; exact due-date semantics (business days? calendar?) unverified.
- `*(assumed)*` `ActiveOnboardingsWidget` shows count + overdue tasks — "overdue" definition depends on unverified due-date semantics.
- `*(assumed)*` Milestone tracking — `SendMilestoneCheckInsCommand` uses a per-milestone not-yet-sent flag, stored as a "milestone log in plan jsonb". No such jsonb column appears in the `hr_onboarding_plans` data model; storage location is undecided.
- UNVERIFIED: Equipment requests are "task type only" in v1; real IT ticket integration deferred to P3 with no target module named.
- UNVERIFIED: Document collection (contract, ID, tax forms) lists request types but no table/columns for the documents themselves or their signed-status tracking.
- UNVERIFIED: Whether `OnboardingService::startPlan` materializes plan tasks at creation or lazily — implied by flow, not stated.
