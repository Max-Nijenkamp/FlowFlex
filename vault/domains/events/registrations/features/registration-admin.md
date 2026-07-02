---
domain: events
module: registrations
feature: registration-admin
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Registration Admin

The internal attendee list: per-event filtering, status management, waitlist visibility, and export.

## Behaviour

- Lists registrations per event with status (registered/waitlisted/confirmed/attended/no_show/cancelled).
- Admin can cancel a registration → triggers FIFO waitlist promotion of the first waitlisted attendee (notified).
- Attendee export (CSV/Excel) for a selected event — throttled.
- Stats widget: registered / confirmed / attended counts.
- `MarkNoShowsCommand` flips confirmed-not-checked-in → `no_show` after the event.

## UI

- **Kind**: simple-resource
- **Page**: `RegistrationResource` list at `/app/events/registrations` + `RegistrationStatsWidget`.
- **Layout**: table (attendee name, email, ticket, status badge, registered/checked-in times); per-event filter; row actions (check-in, cancel); header action (export).
- **Key interactions**: filter by event → status filter → cancel (confirm, promotes waitlist) → export (throttled).
- **States**: empty (no registrations → prompt) · loading (skeleton table) · error (export throttled toast) · selected (row → view/edit).
- **Gating**: `events.registrations.view-any`; cancel/export need `events.registrations.manage`; check-in needs `events.registrations.check-in`.

## Data

- Owns / writes: `ev_registrations` only.
- Reads: event list (Events service) for the filter.
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: cancel → same-module waitlist promotion; counts feed [[../../event-analytics/_module|Event Analytics]].
- Shared entity: `ev_events` (read for the per-event filter).

## Unknowns

- Export field set (which encrypted PII columns are decrypted into the export) + GDPR handling — see [[../unknowns]].

## Related

- [[../_module|Registrations]] · [[check-in]] · [[../../event-analytics/_module|Event Analytics]]
