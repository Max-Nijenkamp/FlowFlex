---
domain: events
module: venues
feature: rooms
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Rooms

Rooms within a venue, used by multi-track events for session room assignment.

## Behaviour

- Each venue has rooms (name unique per venue, capacity).
- Rooms feed the session room picker in [[../../events/features/agenda-sessions|Agenda & Sessions]].
- Duplicate room name per venue rejected.

## UI

- **Kind**: simple-resource (relation manager)
- **Page**: rooms relation manager on `VenueResource`.
- **Layout**: table (name, capacity); inline add/edit.
- **Key interactions**: add room → name + capacity; edit/delete.
- **States**: empty (no rooms → CTA) · loading (skeleton) · error (duplicate name) · selected (edit).
- **Gating**: `events.venues.manage`.

## Data

- Owns / writes: `ev_venue_rooms` only.
- Reads: parent venue (own).
- Cross-domain writes: NONE — rooms are read by Events' session picker, never written by Events ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: session room picker in [[../../events/_module|Events]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Room validation: name unique per venue *(assumed)*, capacity int

### Feature (Pest)
- [ ] Session room picker lists only the event venue's rooms; tenant isolation enforced

### Livewire
- [ ] Rooms relation manager CRUD; gated by the venues permission

## Unknowns

- Room-as-FK vs. string on sessions — see [[../unknowns]].

## Related

- [[../_module|Venues]] · [[venue-directory]] · [[../../events/features/agenda-sessions|Agenda & Sessions]]
