---
type: module
domain: Events Management
panel: events
module-key: events.registrations
status: planned
color: "#4ADE80"
---

# Registrations

Attendee registration: public sign-up, capacity enforcement, waitlist, confirmation, and check-in.

## Core Features

- Public registration form per event (Vue + Inertia)
- Registration record: attendee details, ticket type, status, registered date
- Status: registered → confirmed → attended | no_show | cancelled
- Capacity enforcement + automatic waitlist when full
- Confirmation email with calendar invite (`.ics` via `spatie/icalendar-generator`)
- Check-in at event (QR code scan via `simplesoftwareio/simple-qrcode`, or manual)
- Registration creates/links a CRM contact
- Custom registration questions per event
- Cancellation + waitlist promotion

## Data Model

| Table | Key Columns |
|---|---|
| `ev_registrations` | company_id, event_id, contact_id, ticket_id, status, custom_answers (json), registered_at, checked_in_at, qr_code |

## Filament

**Nav group:** Events

- `RegistrationResource` — list (per event), check-in action, export attendee list
- `CheckInPage` (custom page) — QR scan check-in interface
- `RegistrationStatsWidget` — registered / confirmed / attended counts

## Cross-Domain / Events

- Fires `EventRegistrationReceived` → CRM (create contact), email confirmation

## Related

- [[domains/events/events]]
- [[domains/events/tickets]]
- [[domains/crm/contacts]]
