---
type: module
domain: Events Management
domain-key: events
panel: events
module-key: events.registrations
status: planned
priority: p3
depends-on: [events.events, core.billing, core.rbac, foundation.email, foundation.queues]
soft-depends: [crm.contacts, events.tickets]
fires-events: [EventRegistrationReceived]
consumes-events: []
patterns: [states, events, custom-pages]
tables: [ev_registrations]
permission-prefix: events.registrations
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Registrations

Attendee registration: public sign-up, capacity enforcement, waitlist, confirmation, and check-in.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/events/events\|events.events]] | registrations per published event |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/email-setup\|foundation.email]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, confirmation mails |
| Soft | [[domains/crm/contacts\|crm.contacts]] | consumes `EventRegistrationReceived` → contact |
| Soft | [[domains/events/tickets\|events.tickets]] | paid registration requires ticket purchase before confirm |

---

## Core Features

- Public registration form per event (Vue + Inertia on the landing page)
- Registration record: attendee details, ticket type, status, registered date
- Status: `registered → confirmed → attended | no_show | cancelled` + `waitlisted`
- Capacity enforcement (atomic against event capacity) + automatic waitlist when full
- Waitlist promotion on cancellation (FIFO, notified)
- Confirmation email with `.ics` calendar invite (`spatie/icalendar-generator`); free events auto-confirm, paid confirm on payment
- Check-in at event: QR code scan (`simplesoftwareio/simple-qrcode` on confirmation) or manual
- Fires `EventRegistrationReceived` → CRM contact
- Custom registration questions per event (jsonb definition on event *(assumed: stored in events.custom_questions)*)
- Post-event: confirmed not checked-in → no_show *(assumed)*

---

## Data Model

### ev_registrations

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), event_id FK | ulid | |
| attendee_name / attendee_email | string | unique `(event_id, attendee_email)` |
| contact_id | ulid nullable | CRM link |
| ticket_id | ulid nullable | paid path |
| status | string default `registered` | state machine + waitlisted |
| custom_answers | jsonb | per event questions |
| qr_code | uuid unique | check-in token |
| registered_at / checked_in_at | timestamp | |

---

## DTOs

### PublicRegisterData — event slug (published, registration open), attendee_name/email (required), ticket_id? (active sales window), custom_answers (validated per event questions) — rate-limited + honeypot

## Services & Actions

- `RegistrationService::register(...)` — atomic capacity check → registered/waitlisted; free auto-confirm + mail+.ics; paid → pending ticket purchase
- `RegistrationService::confirm(...)` — on payment (tickets module callback)
- `CheckInAction::run(qr|registration_id)` — confirmed only
- `cancel(...)` — waitlist promotion (FIFO)
- `MarkNoShowsCommand` — post-event

## Events

### Fires: EventRegistrationReceived
| Payload field | Type |
|---|---|
| company_id | string |
| event_id | string |
| registration_id | string |
| attendee_email | string |
| attendee_name | string |

Consumer: CRM find-or-create contact ([[architecture/event-bus]]).

---

## Filament

**Nav group:** Events

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `RegistrationResource` | #1 CRUD resource | per-event filter, check-in action, attendee export |
| `CheckInPage` | #7 custom page | QR scan interface (camera + token input) |
| `RegistrationStatsWidget` | #6 widget | registered/confirmed/attended |

Public form: part of event landing (Vue) — ui-strategy row #16.

---

## Permissions

`events.registrations.view-any` · `events.registrations.check-in` · `events.registrations.manage`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Capacity atomic: concurrent registrations at limit → waitlist, never overshoot
- [ ] Duplicate email per event rejected
- [ ] Free auto-confirms + .ics mail; paid confirms on purchase only
- [ ] Cancellation promotes first waitlisted + notifies
- [ ] QR check-in confirmed-only; invalid/foreign QR rejected
- [ ] Event fires with contract payload
- [ ] No-show marking post-event
- [ ] Public form rate-limited

---

## Build Manifest

```
database/migrations/xxxx_create_ev_registrations_table.php
app/Models/Events/Registration.php
app/States/Events/Registration/{RegistrationState,Registered,Waitlisted,Confirmed,Attended,NoShow,Cancelled}.php
app/Data/Events/PublicRegisterData.php
app/Services/Events/RegistrationService.php
app/Events/Events/EventRegistrationReceived.php
app/Actions/Events/CheckInAction.php
app/Mail/Events/RegistrationConfirmationMail.php (+ .ics attachment)
app/Console/Commands/Events/MarkNoShowsCommand.php
app/Http/Controllers/PublicRegistrationController.php
app/Filament/Events/Resources/RegistrationResource.php
app/Filament/Events/Pages/CheckInPage.php
app/Filament/Events/Widgets/RegistrationStatsWidget.php
database/factories/Events/RegistrationFactory.php
tests/Feature/Events/{RegistrationCapacityTest,WaitlistTest,CheckInTest}.php
```

---

## Related

- [[domains/events/events]]
- [[domains/events/tickets]]
- [[domains/crm/contacts]]
- [[architecture/event-bus]]
