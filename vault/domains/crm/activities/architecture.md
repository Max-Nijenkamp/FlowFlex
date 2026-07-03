---
domain: crm
module: activities
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ActivityResource` | #1 CRUD resource | tweaks: state-badge-column (task done/overdue), custom-header-actions (complete task) | filters: type / owner / status |
| `ActivityTimeline` (Livewire) | #2 record detail timeline | tweaks: relation-manager-timeline (host Contact/Deal/Account view pages render it as a timeline tab; bubble styling cues [[../../../architecture/patterns/page-blueprints#Inbox / Chat / Conversation]]) | cursor-paginated feed on Contact/Deal/Account view |
| `OverdueTasksWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | owner's overdue count; widget polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.activities.view-any') && BillingService::hasModule('crm.activities')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not auto-gate them. This module has no public/portal surface — all artifacts live behind the `/crm` panel guard.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Activity CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Task completion (`CompleteTaskAction`, optional follow-up spawn) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate — guards double-complete / duplicate follow-up ([[../../../architecture/patterns/states]]) |
| `TaskReminderCommand` (stamps `reminded_at`) | n-a | append-only once-guard on a scheduled background job — no interactive concurrent writer |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

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
