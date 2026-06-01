---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.scheduling
status: planned
color: "#4ADE80"
---

# Appointment Scheduling

Public booking pages for reps, round-robin team scheduling, and calendar sync. Prospects self-book meetings.

## Core Features

- Meeting types: name, duration, location (video/phone/in-person), buffer time
- Public booking page per rep (Vue + Inertia) — prospect picks a slot
- Availability: working hours + calendar busy times (Google/Outlook sync)
- Round-robin: distribute bookings across a team
- Calendar sync: two-way with Google Calendar / Outlook
- Booking confirmation: email + `.ics` calendar invite (spatie/icalendar-generator)
- Video link generation (Zoom/Teams/Meet)
- Reminders before the meeting
- Paid bookings via Stripe (optional, for consultations)
- Booking creates an activity + links to contact

## Data Model

| Table | Key Columns |
|---|---|
| `crm_meeting_types` | company_id, owner_id, name, duration_minutes, location_type, buffer_minutes, price_cents |
| `crm_bookings` | company_id, meeting_type_id, contact_id, assigned_rep_id, scheduled_at, status, video_link |
| `crm_availability` | company_id, user_id, working_hours (json), calendar_connection (json, encrypted) |

## Filament

**Nav group:** Activities

- `MeetingTypeResource` — define bookable meeting types, get booking link
- `BookingResource` — view/manage bookings
- Public booking page via Vue + Inertia (see [[frontend/_index]])

## Cross-Domain / Security

- Calendar OAuth tokens encrypted (see [[architecture/patterns/encryption]])
- `.ics` invites via `spatie/icalendar-generator`
- Paid bookings via `stripe/stripe-php`

## Related

- [[domains/crm/contacts]]
- [[domains/crm/activities]]
- `spatie/icalendar-generator`
