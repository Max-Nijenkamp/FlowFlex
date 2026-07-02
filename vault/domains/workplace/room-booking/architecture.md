---
domain: workplace
module: room-booking
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Room Booking — Architecture

## Booking Lifecycle

Bookings carry a simple status field (no `spatie/laravel-model-states` machine specified in source *(assumed)*):

```
confirmed → cancelled
confirmed → released   (no-show auto-release)
confirmed → (checked_in_at stamped)   (attendee arrives)
```

- `confirmed` is the default on create.
- `released` is set by `ReleaseNoShowsCommand` when `start_at + 15m` passes with no `checked_in_at` *(assumed cutoff)*.
- `cancelled` is set manually via `CancelBookingAction`; it frees the slot.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\Workplace\RoomBookingService` | interface→service | `book(BookRoomData)` — transaction + overlap check; recurrence materialises occurrences (conflicting ones skipped + reported). |
| `App\Actions\Workplace\CheckInAction` | lorisleiva action | Stamp `checked_in_at`. |
| `App\Actions\Workplace\CancelBookingAction` | lorisleiva action | Set `status = cancelled`; free the slot. |
| `App\Console\Commands\Workplace\ReleaseNoShowsCommand` | command | Scheduled every 5 min; release `confirmed` bookings past `start_at + 15m` with no check-in. |

### RoomBookingService::book flow

1. Validate `BookRoomData` (room bookable, `end_at` after `start_at`, ≤ 8h *(assumed)*).
2. Open a DB transaction; lock the room's bookings in the window.
3. Overlap check on `(room_id, start_at, end_at)`; conflict → `RoomUnavailableException`.
4. If recurrence, materialise occurrences up to `until` (capped 6 months *(assumed)*); skip + collect conflicting ones.
5. Insert the booking(s), link occurrences by `recurrence_group`.
6. Commit; dispatch confirmation via `core.notifications`; generate `.ics` invite *(assumed)*.

## Events

None fired or consumed. A `RoomBooked` cross-domain event (feed to comms/calendar) is an open design question — see [[unknowns]]. Platform contract: [[../../../architecture/event-bus]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ReleaseNoShowsCommand` | default | every 5 min | status guard `confirmed` + `start_at + 15m` + no `checked_in_at` |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `RoomResource` | Meeting Rooms | Standard CRUD resource | amenities, bookable toggle |
| `RoomBookingPage` | Meeting Rooms | Calendar custom page (fullcalendar) | room filter, booking form; polling 30s |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.rooms.view-any')
        && BillingService::hasModule('workplace.rooms');
}
```

## Search & Realtime

Calendar page polls every 30s *(assumed)* — no websocket channel specified. See [[../../../architecture/patterns/perceived-performance]].
