---
domain: crm
module: activities
feature: task-reminders
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Task Reminders

Notify owners of upcoming and overdue activities (calls, tasks, meetings).

- Scheduled job scans due/overdue activities and dispatches queued notifications
  ([[../../../../infrastructure/queue-horizon]], [[../../../core/notifications/_module]]).
- Reminder lead time configurable *(assumed: per-user preference — see [[../unknowns]])*.
- Per-company scoped (tenant isolation — [[../../../../security/tenancy-isolation]]).

## UI
- **Kind**: background — a scheduled reminder job; the task list itself lives on the activities resource/timeline (custom-page).
- **Page**: no dedicated page. Trigger: `TaskReminderCommand` (scheduled) scans due/overdue activities; results surface as Core Notifications.
- **Layout**: n/a (headless job). Reminders render as in-app/notification-centre entries; overdue tasks also drive `OverdueTasksWidget` on the CRM dashboard.
- **Key interactions**: none direct — user acts on the surfaced notification (open activity, mark task done). Lead-time preference set on user settings *(assumed)*.
- **States**: empty (no due tasks → no dispatch) · loading (job running) · error (notification dispatch failure retried on queue) · selected (n/a)
- **Gating**: `crm.activities.view` to see tasks; reminders honour the activity owner's access

## Data
- Owns / writes: `crm_activities` (stamps reminder guard e.g. `reminded_at`; task/reminder state lives on the activity row per the Build Manifest)
- Reads: activity owner + due data on `crm_activities`; contact/deal context via [[../../contacts/_module|crm.contacts]] / [[../../deals/_module|crm.deals]] read APIs
- Cross-domain writes: via events only ([[../../../../security/data-ownership]]) — notifications dispatched through core.notifications, not written directly

## Relations
- Consumes: scheduled tick (`schedule:run`) → scan due/overdue activities
- Feeds: `ActivityReminderDue` *(assumed)* → consumed by [[../../../core/notifications/_module|core.notifications]] → user notification
- Shared entity: contacts / deals (reference data owned elsewhere; read-only here)

## Test Checklist

### Unit
- [ ] Due-window selection: only activities with `due_at` inside the reminder window and `reminded_at` null are picked
- [ ] Lead-time preference resolves to the configured value (default when unset) *(assumed)*

### Feature (Pest)
- [ ] `TaskReminderCommand` dispatches exactly one queued notification per due activity and stamps `reminded_at` (re-run fires nothing — once-guard)
- [ ] Command is tenant-scoped: an owner in company A is never notified about company B activities
- [ ] Notification-dispatch failure is retried on the queue without clearing `reminded_at` prematurely

## Related

- [[../_module|Activities]] · [[../../../core/notifications/_module]]
