---
domain: crm
module: activities
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Activities — Architecture

See also [[_module|activities._module]], [[../../../architecture/filament-patterns]], [[../../../architecture/ui-strategy]], [[../../../architecture/event-bus]].

---

## Services & Actions

Actions (simple ops — lorisleiva/laravel-actions):

- `LogActivityAction::run(LogActivityData $data): ActivityData`
- `CompleteTaskAction::run(string $activityId, ?LogActivityData $followUp = null): void` — optional follow-up task creation
- `TimelineQuery::for(Model $contactOrDealOrAccount): CursorPaginator` — shared timeline scope used by Contact, Deal, and Account view pages

---

## Filament Artifacts

**Nav group:** Activities

| Artifact | Kind (ui-strategy row) | Notes |
|---|---|---|
| `ActivityResource` | #1 CRUD resource | filters: type/owner/status; complete action |
| Timeline widget | #2 (embedded in view pages) | cursor-paginated feed on Contact/Deal/Account view |
| `OverdueTasksWidget` | #6 widget | owner's overdue count |

Pattern reference: [[../../../architecture/filament-patterns]], [[../../../architecture/ui-strategy]].

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `TaskReminderCommand` | notifications | every 15 min | `due_at` window + `reminded_at` null guard — fires once |

See [[../../../infrastructure/queue-horizon]] for queue config and [[features/task-reminders|task-reminders feature]] for reminder logic detail.

---

## Events

No domain events fired or consumed by this module. Reminder delivery uses Core Notifications (hard dependency).

---

## Search & Realtime

No realtime channel for activities — the timeline is rendered on load and refreshes on action. Cursor pagination per [[../../../architecture/api-design]] rule avoids offset drift on live feeds.
