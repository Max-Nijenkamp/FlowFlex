---
domain: hr
module: onboarding
feature: milestone-checkins
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Milestone Check-ins

Part of [[../_module]].

## Purpose

Send 30/60/90 day milestone check-in reminders after a hire starts.

## Behavior

- `SendMilestoneCheckInsCommand` runs daily 08:00 on the `notifications` queue.
- Fires at 30/60/90d after `started_at`, once per milestone (per-milestone not-yet-sent flag *(assumed: milestone log in plan jsonb)*).
- See [[../../../infrastructure/queue-horizon]] and [[../../../infrastructure/mail]].

## Tables / Permissions / Events

- Tables: `hr_onboarding_plans` (+ milestone-sent storage, undecided).
- Permissions: none (scheduled, system-sent).
- Events: none.

## UI

- **Kind**: custom-page (30/60/90-day check-in tracking surface fed by a scheduled command)
- **Page**: "Milestone Check-ins" (`/hr/onboarding/milestones`)
- **Layout**: list of upcoming/overdue 30/60/90-day check-ins per employee; the reminders themselves are sent by `SendMilestoneCheckInsCommand` (scheduled).
- **Key interactions**: review upcoming/overdue check-ins; drill into a check-in.
- **States**: empty = "No upcoming check-ins" · loading = skeleton · error = "Could not load" · selected = check-in detail.
- **Gating**: visible with `hr.onboarding.view`.

> [!warning] UNVERIFIED
> The reminder send is a scheduled console command (background); the page only displays state.

## Data

- Owns / writes: reads `hr_onboarding_plans` (`started_at` drives 30/60/90).
- Reads: `hr_employees` (owned by hr.profiles).
- Cross-domain writes: none (sends reminders via core.notifications).

> [!warning] UNVERIFIED
> May need an own table to track sent milestones *(assumed — the data-model does not list one)*.

## Relations

- Consumes: none.
- Feeds: milestone reminders → core.notifications (email).
- Shared entity: reads `hr_employees` (owned by hr.profiles).

> Storage of the per-milestone sent flag is undecided — no jsonb column exists in the data model. See [[../unknowns]].
