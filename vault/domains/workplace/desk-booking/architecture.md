---
domain: workplace
module: desk-booking
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Desk Booking — Architecture

## Booking Lifecycle

Plain string status (no state-machine class *(assumed)*):

```
booked → cancelled
booked → released   (no-show auto-release by 11:00 *(assumed)*)
booked → (checked_in_at stamped)
```

## Booking Rules

- **Dual uniqueness** — one booking per desk per date `(desk_id, booking_date)` **and** one desk per employee per date `(employee_id, booking_date)`. Both enforced by DB unique indexes + a transaction.
- **Max days in advance** — default 14 *(assumed, configurable)*.
- **Max consecutive days** — default 5 *(assumed, configurable)*.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Services\Workplace\DeskBookingService` | interface→service | `book(BookDeskData)` — both uniqueness rules in a transaction; advance + consecutive-day checks; optional recurrence (weekdays until). |
| `App\Actions\Workplace\CheckInDeskAction` | lorisleiva action | Stamp `checked_in_at`. |
| `App\Console\Commands\Workplace\ReleaseDeskNoShowsCommand` | command | Daily 11:00 — release `booked` desks with no check-in. |

### DeskBookingService::book flow

1. Validate `BookDeskData` (desk bookable + free that date; date future + ≤ max advance; ≤ max consecutive).
2. Transaction: assert both uniqueness rules; a violation yields a rule message ("You already have a desk booked for this date.").
3. Insert the booking (or a recurrence set of weekday rows).
4. Commit.

## Events

None fired or consumed. See [[unknowns]] for the open `DeskBooked` question. Platform contract: [[../../../architecture/event-bus]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ReleaseDeskNoShowsCommand` | default | daily 11:00 | status guard `booked` + no `checked_in_at`; date guard = today |

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `DeskResource` | Desks | Standard CRUD resource | map position fields (x/y) |
| `DeskBookingPage` | Desks | Spatial / floor-map custom page | floor map, date picker, click-to-book, team view; polling 60s |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.desks.view-any')
        && BillingService::hasModule('workplace.desks');
}
```

## Search & Realtime

Floor map polls every 60s *(assumed)*; no websocket channel.
