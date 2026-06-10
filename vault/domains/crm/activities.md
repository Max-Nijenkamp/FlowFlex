---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.activities
status: planned
priority: v1-core
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications]
soft-depends: [crm.deals]
fires-events: []
consumes-events: []
patterns: []
tables: [crm_activities]
permission-prefix: crm.activities
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Activities

Calls, emails, meetings, and tasks logged against contacts and deals. The activity log is the source of truth for all customer interactions.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | activities attach to contacts/accounts |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, due reminders |
| Soft | [[domains/crm/deals\|crm.deals]] | deal timeline; contact-only without it |

---

## Core Features

- Activity types: Call, Email, Meeting, Task, Note
- Log against: contact, deal, account — appears on all three timelines
- Activity date/time, duration, outcome, description
- Task completion: mark task done, set follow-up reminder
- Activity due date + reminder notification via Core Notifications
- Activity feed: chronological timeline on contact/deal view pages (cursor-paginated per [[architecture/api-design]] rule)
- Filter by type, owner, date range
- Overdue task detection and dashboard badge

---

## Data Model

### crm_activities

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| type | string | not null | call / email / meeting / task / note |
| subject | string | not null | |
| description | text | nullable | |
| owner_id | ulid | not null FK users | |
| contact_id / deal_id / account_id | ulid | nullable FKs — at least one required | |
| activity_date | timestamp | not null | when it happened/happens |
| duration_minutes | int | nullable | |
| outcome | string | nullable | |
| is_complete | boolean | default true (false for tasks) | |
| due_at | timestamp | nullable | tasks only |
| reminded_at | timestamp | nullable | reminder-once guard |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, contact_id, activity_date)`, `(company_id, deal_id, activity_date)`, `(company_id, owner_id, is_complete, due_at)` (overdue queries)

---

## DTOs

### LogActivityData
| Field | Type | Validation |
|---|---|---|
| type | string | required, in:call,email,meeting,task,note |
| subject | string | required, max:255 |
| description | ?string | max:5000 |
| contact_id / deal_id / account_id | ?string | each ulid in company |
| activity_date | CarbonImmutable | required |
| duration_minutes | ?int | min:1 |
| due_at | ?CarbonImmutable | required_if type=task ("Tasks need a due date.") |

Cross-field: at least one of contact_id/deal_id/account_id ("Link the activity to a contact, deal, or account.").

## Services & Actions

Actions (simple ops):
- `LogActivityAction::run(LogActivityData $data): ActivityData`
- `CompleteTaskAction::run(string $activityId, ?LogActivityData $followUp = null): void` — optional follow-up task creation
- `TimelineQuery::for(Model $contactOrDealOrAccount): CursorPaginator` — shared timeline scope

---

## Filament

**Nav group:** Activities

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ActivityResource` | #1 CRUD resource | filters: type/owner/status; complete action |
| Timeline widget | #2 (embedded in view pages) | cursor-paginated feed on Contact/Deal/Account view |
| `OverdueTasksWidget` | #6 widget | owner's overdue count |

---

## Permissions

`crm.activities.view-any` · `crm.activities.view` · `crm.activities.create` · `crm.activities.update` · `crm.activities.delete`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `TaskReminderCommand` | notifications | every 15 min | `due_at` window + `reminded_at` null guard — fires once |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Activity without any link rejected; task without due date rejected
- [ ] Activity appears on contact AND deal AND account timelines when all linked
- [ ] Reminder fires once (`reminded_at` guard)
- [ ] Overdue widget counts only own incomplete past-due tasks
- [ ] Timeline cursor pagination (no offset on the feed)

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

## Related

- [[domains/crm/contacts]]
- [[domains/crm/deals]]
- [[architecture/api-design]] — cursor pagination rule
