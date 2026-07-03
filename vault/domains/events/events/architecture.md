---
domain: events
module: events
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Events — Architecture

## Status Machine (spatie/laravel-model-states)

```
draft → published → live → completed
  └──────┴──────────────→ cancelled
```

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `draft` | `published` | `events.events.publish` | Landing page goes live; registration opens |
| `published` | `live` | Start time (scheduled) or manual | — |
| `live` | `completed` | End time or manual | — |
| `draft` / `published` | `cancelled` | `events.events.cancel` | Registrants notified (same-domain via registrations) |

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\Events\EventService` | interface→service | `publish()`, `cancel()`; cancel cascades a registrant notification via the registrations service (same-domain call, not a cross-domain write). |
| `App\Console\Commands\Events\EventLifecycleCommand` | scheduled command | Every 15 min: transitions `published → live → completed` at the event's start/end times. Guarded on status + time for idempotency. |

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `EventLifecycleCommand` | default | every 15 min | status + time guards |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `EventResource` | Events | #1 CRUD resource | Publish/cancel actions; sessions relation manager. |
| `EventCalendarPage` | Events | #4 calendar custom page | `saade/filament-fullcalendar` of events. |
| Public landing | — (Vue) | #16 public Vue+Inertia | `/e/{company}/{slug}`, published+ only. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.events.view-any')
        && BillingService::hasModule('events.events');
}
```

Public landing uses a guest guard (Vue + Inertia per [[../../../architecture/ui-strategy]]).

## Events

None fired or consumed at the module level. Cross-domain reactions (registrations, CRM) originate in the **registrations** module. See [[../../../architecture/event-bus]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `publish` / `cancel` | Pessimistic | spatie state transitions under `lockForUpdate` per patterns/states; cancel cascades registrant notification once |
| `EventLifecycleCommand` published->live->completed | n-a | Single scheduled writer; status+time guards idempotent |
| Event/session CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

None specified. Slug via `spatie/laravel-sluggable`.
