---
domain: projects
module: okrs
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# OKRs — Architecture

## Progress Model

- KR progress = `(current − baseline) / (target − baseline)` clamped 0–100.
- Objective progress = average of its KR progress; cascades up parent objectives.
- Health = progress vs quarter time-elapsed: at-risk >15pt behind, off-track >30pt *(assumed)*.

## Hierarchy

`parent_objective_id` self-FK, cycle-checked, max depth 4 *(assumed)*: company → department → team → individual.

## Services & Actions

- `OkrService::checkIn(CheckInData)` — records check-in, recomputes KR progress, cascades objective + parent progress.
- `OkrService::health(objectiveId): string`.
- `CreateObjectiveAction` / `AddKeyResultAction` (cycle + depth guards).

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `OkrCheckinReminderCommand` | notifications | weekly Mon 09:00 | KRs without a check-in in 7d — window-safe re-run |

## Events

None cross-domain. Reminders use the notifications service API.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `ObjectiveResource` | OKRs | #1 CRUD (tree-ish) | nested display, KR relation manager, check-in action |
| `OkrDashboardPage` | OKRs | #6 dashboard page | quarter selector, health distribution, recent check-ins |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('projects.okrs.view-any')
        && BillingService::hasModule('projects.okrs');
}
```

## Search & Realtime

None.
