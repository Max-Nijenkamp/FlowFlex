---
domain: legal
module: compliance-registers
feature: compliance-tasks
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Compliance Tasks

Recurring obligations tied to a control (e.g. annual review, quarterly audit) with assignment and reminders.

## Behaviour

- Task = control + title + due_date + optional frequency (once/monthly/quarterly/annual) + assignee.
- Completing a task with a frequency spawns the next occurrence (`due + frequency`), once ([[../architecture]]).
- `ComplianceTaskReminderCommand` reminds assignees at 7d/overdue windows, once via `reminded`.

## UI

- **Kind**: simple-resource
- **Page**: `ControlResource` tasks tab + a "My compliance tasks" filtered view (`/legal/compliance/controls/{id}` → Tasks).
- **Layout**: table (title, control, due date, frequency, assignee, status); inline add; complete action.
- **Key interactions**: add task with frequency; assign; complete (auto-spawns next if recurring); filter overdue / mine.
- **States**: empty ("No tasks") · loading (skeleton) · error (validation) · selected (row → edit).
- **Gating**: `legal.compliance.manage-tasks`.

## Data

- Owns / writes: `legal_compliance_tasks`.
- Reads: `users` for assignee (platform).
- Cross-domain writes: none — reminders via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: overdue reminders via `core.notifications`.
- Shared entity: `users` (platform).

## Unknowns

- Recurrence anchored to completion date vs original due date — assumed completion+frequency ([[../unknowns]]).

## Related

- [[../_module|Compliance Registers]] · [[./control-management]]
