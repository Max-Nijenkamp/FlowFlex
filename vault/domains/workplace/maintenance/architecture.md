---
domain: workplace
module: maintenance
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Facility Maintenance — Architecture

## State Machine (`spatie/laravel-model-states`)

`MaintenanceRequest.status` is a real state machine:

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `reported` | `assigned` | `workplace.maintenance.assign` | assignee notified |
| `assigned` | `in_progress` | assignee | — |
| `in_progress` | `resolved` | assignee | reporter notified; after-photo prompt |
| `resolved` | `closed` | reporter confirm, or auto 7d *(assumed)* | — |
| any open | `reported` (reopen) | reporter | — |

Classes: `App\States\Workplace\MaintenanceRequest\{MaintenanceState, Reported, Assigned, InProgress, Resolved, Closed}`. See [[../../../architecture/patterns/states]].

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Actions\Workplace\AssignMaintenanceAction` | lorisleiva action | Transition `reported → assigned`; set assignee/contractor; notify. |
| `App\Actions\Workplace\ResolveMaintenanceAction` | lorisleiva action | Transition `in_progress → resolved`; notify reporter; prompt after-photo. |
| `App\Console\Commands\Workplace\RunMaintenanceSchedulesCommand` | command | Due schedules → create request + advance `next_due_at` transactionally. |

## Events

None fired or consumed *(assumed)*. A `MaintenanceReported` / `MaintenanceResolved` cross-domain event is an open question (finance contractor cost, asset linkage) — see [[unknowns]]. Platform contract: [[../../../architecture/event-bus]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RunMaintenanceSchedulesCommand` | default | daily 06:00 | `next_due_at` advanced transactionally; one request per due date |
| Auto-close resolved | default | daily | 7d guard on `resolved_at` |

## Filament Artifacts

**Nav group:** Maintenance

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `MaintenanceRequestResource` | #1 CRUD resource | tweaks: state-badge-column (status), custom-header-actions (assign / resolve / reopen) | queue tabs (open/assigned/overdue); before/after photos |
| `MaintenanceScheduleResource` | #1 CRUD resource | tweaks: custom-header-actions (pause/resume *(assumed)*) | preventive schedules; next-due column |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.maintenance.view-any')
        && BillingService::hasModule('workplace.maintenance');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Request / schedule CRUD | Optimistic | `updated_at` stale-check → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Status transitions (assign / resolve / close / reopen) | Pessimistic | `DB::transaction()` + `lockForUpdate()` per [[../../../architecture/patterns/states]] — no double-assign or double-notify |
| Schedule due-run (`next_due_at`) | Pessimistic | Cursor advanced transactionally — one request per due date |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## File Uploads

Before/after photos via `core.files` / Media Library: restrict to image MIME (jpg/png/webp), max size cap, stored under `companies/{company_id}/maintenance/` for tenant isolation (security audit 2026-06-11, medium).
