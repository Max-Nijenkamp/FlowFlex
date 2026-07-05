---
domain: crm
module: activities
type: module
build-status: in-progress
status: wip
color: "#4ADE80"
updated: 2026-07-05
---

# Activities

Calls, emails, meetings, and tasks logged against contacts and deals. The activity log is planned as the source of truth for all customer interactions.

> All work here is **planned** — the CRM code was stripped back to an app/admin shell. See [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

---

## Module-key

`crm.activities`

**Priority:** v1-core  
**Panel:** crm  
**Permission prefix:** `crm.activities`  
**Tables:** `crm_activities`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../contacts/_module\|crm.contacts]] | activities attach to contacts/accounts |
| Hard | core.billing + core.rbac + core.notifications | gating, permissions, due reminders |
| Soft | [[../deals/_module\|crm.deals]] | deal timeline; contact-only without it |

---

## Core Features

- Activity types: Call, Email, Meeting, Task, Note
- Log against: contact, deal, account — appears on all three timelines
- Activity date/time, duration, outcome, description
- Task completion: mark task done, set follow-up reminder
- Activity due date + reminder notification via Core Notifications
- Activity feed: chronological timeline on contact/deal view pages (cursor-paginated per [[../../../architecture/api-design]] rule)
- Filter by type, owner, date range
- Overdue task detection and dashboard badge

---

## Build Manifest

```
database/migrations/xxxx_create_crm_activities_table.php
app/Models/CRM/Activity.php
app/Data/CRM/{LogActivityData,ActivityData}.php
app/Actions/CRM/{LogActivityAction,CompleteTaskAction}.php
app/Support/CRM/TimelineQuery.php
app/Console/Commands/CRM/TaskReminderCommand.php
app/Filament/CRM/Resources/ActivityResource.php
app/Filament/CRM/Widgets/OverdueTasksWidget.php
app/Livewire/CRM/ActivityTimeline.php
database/factories/CRM/ActivityFactory.php
tests/Feature/CRM/{ActivityTest,TaskReminderTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot see/log activities on company B contacts/deals/accounts
- [ ] Module gating: artifacts hidden when `crm.activities` inactive
- [ ] Activity without any link rejected; task without due date rejected
- [ ] Activity appears on contact AND deal AND account timelines when all linked
- [ ] Reminder fires once (`reminded_at` guard)
- [ ] Overdue widget counts only own incomplete past-due tasks
- [ ] Timeline cursor pagination (no offset on the feed)

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | contact/deal read API | [[../contacts/_module\|crm.contacts]], [[../deals/_module\|crm.deals]] | activities attach polymorphically; rows live in activities' own tables |
| Consumes | `EmailTracked` | [[../email-integration/_module\|crm.email]] | auto-log an email/open activity on the timeline |
| Consumes | `AppointmentBooked` *(assumed)* | scheduling | auto-log a meeting activity |
| Fires | `ActivityLogged` | revenue-intelligence, [[../sales-sequences/_module\|crm.sequences]] | interaction signal |
| Fires | `ActivityCompleted` | revenue-intelligence | task/meeting completion signal |

**Data ownership:** `crm.activities` writes only `crm_activities` (task/reminder state lives on the activity row per the Build Manifest); all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

> [!warning] UNVERIFIED
> `crm_activities` is the only table named in the Build Manifest — there is no separate `tables:` line. Task/reminder columns are assumed to live on `crm_activities`. Event names `ActivityLogged` / `ActivityCompleted` / `AppointmentBooked` must be confirmed against [[../../../architecture/event-bus]].

---

## Related

- [[../contacts/_module|crm.contacts]]
- [[../deals/_module|crm.deals]]
- [[architecture|activities.architecture]]
- [[data-model|activities.data-model]]
- [[api|activities.api]]
- [[security|activities.security]]
- [[features/task-reminders|task-reminders feature]]
- [[../../../architecture/event-bus]]
- [[../../../architecture/ui-strategy]]
- [[../../../architecture/filament-patterns]]
- [[../../../glossary]]
