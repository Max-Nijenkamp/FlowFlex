---
domain: events
module: registrations
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Registrations — API / DTOs

## `PublicRegisterData` (spatie/laravel-data, public)

| Field | Type | Rules |
|---|---|---|
| `event_slug` | string | required; event published + registration open |
| `attendee_name` | string | required |
| `attendee_email` | string | required; email |
| `ticket_id` | ulid | nullable; required for paid events; within active sales window |
| `custom_answers` | array | validated per the event's custom questions |

Guarded by a rate limiter + honeypot on the public endpoint.

## Read / Command API (internal)

- `RegistrationService::confirm(registrationId)` — called by [[../tickets/_module|Tickets]] on payment success (same-domain call).
- `RegistrationService::cancel(registrationId)` — cancels + promotes first waitlisted.
- `CheckInAction::run($qrOrId)` — confirmed-only check-in.

## Fired Event

- `EventRegistrationReceived` — see [[architecture#Events]] for the payload contract. Consumed by CRM Contacts.

## Public / Portal Endpoints

| Route | Method | Auth | Purpose |
|---|---|---|---|
| `/e/{company}/{slug}` (form POST) | POST | guest (rate-limited) | Submit a registration. See [[features/public-registration]]. |

Attendee export (admin) is throttled (per [[../../../build/security-audit-2026-06-11]]).
