---
domain: events
module: events
feature: agenda-sessions
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Agenda & Sessions

Multi-session events: a schedule of sessions within the event window, each with a room and an ordered agenda.

## Behaviour

- Each session has a title, start/end (must fall inside the event's `start_at`/`end_at`), a room, and an order.
- Rooms are drawn from the event's venue rooms (read from Venues) *(assumed: `ev_venue_rooms.name`)*.
- Agenda renders sessions in `order`, grouped by day for multi-day events.
- Speakers are attached to sessions by the [[../../speakers/_module|Speakers]] module (`ev_session_speakers`), not written here.

## UI

- **Kind**: custom-page
- **Page**: "Agenda" (`/app/events/events/{event}/agenda`) — an agenda/timeline builder for the event.
- **Layout**: day tabs → time-ordered session cards per room column (multi-track); drag to reorder / reslot; right rail = add-session form. On the public landing this renders read-only as a schedule.
- **Key interactions**: drag session card to a new time/room → validate within event window → save; click card → edit slide-over; add session inline.
- **States**: empty (no sessions → "build your agenda" CTA) · loading (skeleton timeline) · error (out-of-window drop rejected with toast) · selected (card highlighted, slide-over open).
- **Gating**: `events.events.view-any` to view; `events.events.update` to edit the agenda.

## Data

- Owns / writes: `ev_sessions` only.
- Reads: venue rooms via the Venues service; session-speaker badges via the Speakers service.
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing (reads Venues rooms + Speakers assignments read-only).
- Feeds: sessions are read by [[../../speakers/_module|Speakers]] (assignment target) and the public landing.
- Shared entity: `ev_venue_rooms` (owned by Venues).

## Unknowns

- Room as FK to `ev_venue_rooms` vs. free-text string *(assumed)* — see [[../unknowns]].
- Session-level check-in / attendance is deferred (analytics uses an attendance proxy) — see [[../../event-analytics/_module]].

## Related

- [[../_module|Events]] · [[event-crud]] · [[../../speakers/_module|Speakers]] · [[../../venues/_module|Venues]]
