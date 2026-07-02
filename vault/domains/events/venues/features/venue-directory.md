---
domain: events
module: venues
feature: venue-directory
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Venue Directory

The reusable, company-level venue records used by in-person events.

## Behaviour

- CRUD venues: name, address, capacity, contact (phone → E.164), facilities, cost.
- Usage list shows which events used the venue.
- Delete blocked while an upcoming event references it *(assumed)*.

## UI

- **Kind**: simple-resource
- **Page**: `VenueResource` list + form at `/app/events/venues` (nav group "Settings").
- **Layout**: table (name, city, capacity, # events); form with address fields, facilities repeater, cost, phone; rooms relation manager.
- **Key interactions**: create/edit venue; view usage; delete guarded.
- **States**: empty (no venues → CTA) · loading (skeleton) · error (delete blocked toast; phone validation) · selected (edit form).
- **Gating**: `events.venues.view-any`; edit needs `events.venues.manage`.

## Data

- Owns / writes: `ev_venues` only.
- Reads: event usage (Events service).
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: venues referenced by [[../../events/_module|Events]] (`venue_id`); address renders on the landing.
- Shared entity: none.

## Unknowns

- Per-event vs. flat venue cost — see [[../unknowns]].

## Related

- [[../_module|Venues]] · [[rooms]] · [[../../events/_module|Events]]
