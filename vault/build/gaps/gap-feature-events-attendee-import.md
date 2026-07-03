---
type: gap
severity: medium
category: feature
status: accepted
domain: events
color: "#F97316"
discovered: 2026-07-03
discovered-in: events.registrations
---

# Gap: Events has attendee export but no bulk attendee/guest-list import

## Context

[[../../domains/events/registrations/features/registration-admin|registration-admin]] specs attendee
**export** (CSV/Excel, throttled) for a selected event, but there is no **import** path.
`core.data-import`'s [[../../domains/core/data-import/features/importer-registry|ImporterRegistry]] already
provides the mechanism (importers registered by `crm.contacts`, `hr.employees`, products, …) — Events does
not register one.

## Problem

Eventbrite organizers repeatedly ask to add many attendees at once rather than one-at-a-time; third-party
tools exist purely to bulk-add to a guest list. An organizer migrating an existing event, or seeding a
comped/VIP guest list, has no way to upload a roster and issue tickets in bulk.

## Impact

Weakens the "switch from Eventbrite" and comped/guest-list flows for
[[../../domains/events/registrations/_module|events.registrations]] and
[[../../domains/events/tickets/_module|events.tickets]]. Package-fit — no new dependency needed.

## Proposed Solution

Register an `events.registrations` importer with `core.data-import` (`maatwebsite/laravel-excel`) that
creates `ev_registrations` rows from an uploaded roster, then issues QR tickets via
`simplesoftwareio/simple-qrcode` + `spatie/laravel-pdf` and emails them (reusing the existing ticket-issue
path). Respect oversell/capacity checks and per-row validation through the importer's Create DTO.

## Sources

- [Add multiple attendees at once — organizers ask, Eventbrite has no native bulk-add (Quora)](https://www.quora.com/Is-there-a-way-in-Eventbrite-to-add-multiple-attendees-to-an-event-without-doing-it-one-at-a-time)
- [Import attendees from a text/CSV file to a guest list (IgniteTalks, GitHub)](https://github.com/IgniteTalks/AddAttendeesToEventBrite)
- [Upload a list of attendees & email QR-code tickets (Event Smart)](https://eventsmart.com/features/attendee-importer/)
