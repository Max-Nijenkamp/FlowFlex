---
domain: workplace
module: room-booking
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Room Booking — Decisions

> Reconstructed from the flat source spec. Ratify during the v2 spec rebuild.

## ADR: Overlap prevention in a transaction

- **Context:** Two users may book the same room for overlapping times concurrently.
- **Decision:** `RoomBookingService::book` runs the overlap check + insert inside a single DB transaction (row-lock on the room's bookings); a conflict throws `RoomUnavailableException`.
- **Consequences:** No double-booking under concurrency; the calendar page must surface the exception cleanly.

## ADR: No-show auto-release at start+15m *(assumed cutoff)*

- **Decision:** `ReleaseNoShowsCommand` frees a `confirmed` booking 15 minutes after `start_at` if no `checked_in_at`.
- **Consequences:** Recovers ghost-booked capacity; cutoff is a configurable default *(assumed)*.

## ADR: Recurrence materialised as rows (not a rule)

- **Decision:** Recurring bookings are expanded into individual `wp_room_bookings` rows linked by `recurrence_group`, capped 6 months *(assumed)*; conflicting occurrences are skipped and reported.
- **Consequences:** Each occurrence is independently cancellable/check-in-able; simpler queries than an RRULE engine, at the cost of row volume.

## ADR: `.ics` invites via `spatie/icalendar-generator` *(assumed)*

- **Decision:** Booking a room sends attendees a calendar invite.
- **Consequences:** Attendees resolved read-only from `hr.profiles`; no attendee table persisted *(assumed)* — see [[unknowns]].
