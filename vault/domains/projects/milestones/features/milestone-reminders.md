---
domain: projects
module: milestones
feature: milestone-reminders
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
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

## Test Checklist

### Unit
- [ ] Reminder window predicate: only milestones exactly 7 days from `target_date` and still `open` are selected.
- [ ] Overdue predicate: `open` milestone past `target_date` flips to `missed`.

### Feature (Pest)
- [ ] `MilestoneStatusCommand` flips overdue `open` milestones to `missed` and leaves `achieved` ones untouched.
- [ ] 7-day reminder sends once via `NotificationService`; a second run does not re-send (`reminded_at` once-guard).
- [ ] Command runs per company under `WithCompanyContext` — company A's job never touches company B's milestones (tenant isolation).

## Unknowns

- Multiple reminder windows (14d/7d/1d) configurable — see [[../unknowns]].

## Related

- [[../_module|Milestones]] · [[milestone-tracking|Milestone Tracking]] · [[../../../core/notifications/_module|Notifications]]
