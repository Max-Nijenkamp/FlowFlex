---
domain: events
module: venues
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Venues — Architecture

## Services & Actions

None beyond CRUD. Events reference `venue_id`; the session room picker reads a venue's rooms. Delete is blocked while an upcoming event references the venue *(assumed)*.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `VenueResource` | Settings | #1 CRUD resource | Rooms relation manager; usage list. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('events.venues.view-any')
        && BillingService::hasModule('events.venues');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Venue / room CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |
| Delete guard | n-a | Blocked while an upcoming event references the venue *(assumed)* -- checked in the delete action |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Events

None fired or consumed. See [[../../../architecture/event-bus]].

## Data Notes

- `address` is jsonb; `contact_phone` normalised to E.164 via `propaganistas/laravel-phone`.
- Rooms are unique per venue by name.
