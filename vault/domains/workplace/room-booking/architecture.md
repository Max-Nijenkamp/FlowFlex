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

**Nav group:** Meeting Rooms

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `RoomResource` | #1 CRUD resource | — | amenities, `is_bookable` toggle; list filters: capacity, amenities |
| `RoomBookingPage` | #4 Calendar custom page | [[../../../architecture/patterns/page-blueprints#Calendar]] | `saade/filament-fullcalendar`; room filter, slot booking form; polling 30s |

**Access contract (mandatory):** every artifact above gates on
`canAccess() = Auth::user()->can('workplace.rooms.view-any') && BillingService::hasModule('workplace.rooms')`
per [[../../../architecture/filament-patterns]] #1 — `RoomBookingPage` (custom page) states it explicitly, Filament does not auto-gate custom pages. Booking + cancel actions on the calendar carry their own permissions (`workplace.rooms.book`, `workplace.rooms.cancel-any`) and the book action names a `panel-action` rate limiter (sends a confirmation notification — see [[security#Rate Limiting]]). `RoomBookingPage` satisfies [[../../../architecture/patterns/custom-page-checklist]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Room CRUD (`RoomResource` form) | Optimistic | `updated_at` stale-check on save → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Booking overlap (`RoomBookingService::book`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the room's bookings in the window; overlap → `RoomUnavailableException` |
| Recurrence materialisation | Pessimistic | same transaction as the series insert; conflicting occurrences skipped + reported |
| Check-in / cancel / release | Optimistic | status guard on the single booking row |

Booking overlap is the slot/capacity-decrement case → **pessimistic** per [[../../decisions/decision-2026-07-02-optimistic-locking-standard|concurrency standard]] (the transaction-based overlap rejection documented in `RoomBookingService::book` above). Ordinary room-catalogue edits use the optimistic default.

## Search & Realtime

Calendar page polls every 30s *(assumed)* — no websocket channel specified. See [[../../../architecture/patterns/perceived-performance]].
