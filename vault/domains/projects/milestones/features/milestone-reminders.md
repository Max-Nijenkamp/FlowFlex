---
domain: projects
module: milestones
feature: milestone-reminders
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Overdue & Reminders

Scheduled milestone status maintenance and 7-day reminders.

## Behaviour

- Daily command (`MilestoneStatusCommand`, 07:30): any `open` milestone past its `target_date` → `missed`.
- 7-day reminder: milestone due in 7 days and still open → notify owners once (`reminded_at` guard).

## UI

- **Kind**: background (scheduled command — no page).
- **Page**: none. Results surface as notifications (core.notifications) + updated status chips on the milestone list.
- **Key interactions**: n/a (job). Notification click deep-links to the milestone.
- **States**: n/a (background). Failures logged/retried on the notifications queue.
- **Gating**: runs system-side under `WithCompanyContext` per company; recipients gated by notification preferences.

## Data

- Owns / writes: `proj_milestones` (`status`, `reminded_at`).
- Reads: own milestones due/overdue.
- Cross-domain writes: none — reminders sent via `NotificationService` API ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: `NotificationService::notify` → core.notifications delivers the reminder.
- Shared entity: `users` (owners).

## Unknowns

- Multiple reminder windows (14d/7d/1d) configurable — see [[../unknowns]].

## Related

- [[../_module|Milestones]] · [[milestone-tracking|Milestone Tracking]] · [[../../../core/notifications/_module|Notifications]]
