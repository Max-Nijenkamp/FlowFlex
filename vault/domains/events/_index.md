---
type: domain-index
domain: Events Management
panel: events
color: "#4ADE80"
---

# Events Management

Events, registrations, tickets, speakers, sponsors, venues, and analytics. **Panel:** `/events` (Rose) — Phase 3.

**Displaces**: Eventbrite, Hopin, BigMarker (SMB)

---

## Navigation Groups

- **Events** — Events, Event Calendar, Sessions, Check-In
- **Registrations** — Registrations, Tickets
- **People** — Speakers, Sponsors
- **Analytics** — Event Dashboard
- **Settings** — Venues

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/events/events\|Events]] | `events.events` | planned | **P3 core** |
| [[domains/events/registrations\|Registrations]] | `events.registrations` | planned | **P3 core** |
| [[domains/events/tickets\|Tickets]] | `events.tickets` | planned | P3 |
| [[domains/events/speakers\|Speakers]] | `events.speakers` | planned | P3 |
| [[domains/events/sponsors\|Sponsors]] | `events.sponsors` | planned | P3 |
| [[domains/events/venues\|Venues]] | `events.venues` | planned | P3 |
| [[domains/events/event-analytics\|Event Analytics]] | `events.analytics` | planned | P3 |

---

## Key Patterns

- `saade/filament-fullcalendar` — event calendar
- `spatie/laravel-model-states` — event status, registration status
- `spatie/laravel-pdf` + `simplesoftwareio/simple-qrcode` — tickets with QR codes
- `spatie/icalendar-generator` — `.ics` invites on registration
- `brick/money` + `stripe/stripe-php` — paid tickets
- `spatie/laravel-sluggable` — event slugs
- Public registration + landing pages via Vue + Inertia (see [[frontend/_index]])
- Cross-domain: `EventRegistrationReceived` → CRM contact; ticket/sponsor revenue → Finance
