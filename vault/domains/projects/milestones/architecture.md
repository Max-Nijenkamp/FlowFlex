---
domain: projects
module: milestones
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Milestones — Architecture

## Status

Plain enum (trivial transitions — no spatie states *(assumed)*):

```
open → achieved   (manual)
open → missed     (scheduled: target passed, still open)
```

## Services & Actions

Actions (lorisleiva/laravel-actions):

- `CreateMilestoneAction` / `AchieveMilestoneAction`.
- `LinkTasksAction::run(milestoneId, taskIds)` — same-project check.
- `MilestoneProgress::for(milestoneId): float` — done/total linked tasks; called by projects.tasks' `CompleteTaskAction` (same domain).

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `MilestoneStatusCommand` | notifications | daily 07:30 | open→missed past target; 7-day reminders guarded by `reminded_at` |

## Events

None cross-domain. Progress is a same-domain call; reminders use the notifications service API.

## Filament Artifacts

**Nav group:** Projects

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `MilestoneResource` | #1 CRUD resource | tweaks: custom-header-actions (achieve) | progress column, status chip (plain enum — not spatie states), cross-project list filterable by status/date |
| `MilestoneTimelineWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | horizontal timeline of markers on the project view page |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('projects.milestones.view-any') && BillingService::hasModule('projects.milestones')`
per [[../../../architecture/filament-patterns]] #1. The `achieve` header action additionally requires
`projects.milestones.achieve`. The `MilestoneTimelineWidget` is a widget, not a page — it inherits its host
page's gate but restates the module check so it cannot render when `projects.milestones` is inactive.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Milestone CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Achieve / link-tasks (`AchieveMilestoneAction`, `LinkTasksAction`) | Optimistic | plain-enum status write + join rows on the milestone record — `updated_at` stale-check; no spatie state machine, so no pessimistic lock ([[../../../architecture/patterns/optimistic-locking]]) |
| Scheduled maintenance (`MilestoneStatusCommand`: open→missed, 7-day reminder) | n/a | Single-writer background command per company; idempotent via status re-check + `reminded_at` once-guard — no interactive-editor contention |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

None.
