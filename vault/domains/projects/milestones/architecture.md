---
domain: projects
module: milestones
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `MilestoneResource` | Projects | #1 CRUD | achieve action, progress column, cross-project list |
| `MilestoneTimelineWidget` | Projects | #6 widget | on project view page |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.milestones.view-any')
        && BillingService::hasModule('projects.milestones');
}
```

## Search & Realtime

None.
