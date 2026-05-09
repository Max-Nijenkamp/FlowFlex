---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 3
status: planned
migration_range: 250000–299999
last_updated: 2026-05-09
---

# Meeting Scheduler

Prospects book meetings directly with sales reps without email ping-pong. Replaces Calendly, Chili Piper, HubSpot Meetings, SavvyCal.

**Panel:** `crm`  
**Phase:** 3 — core sales motion; SDRs and AEs use this daily

---

## Features

### Booking Page
- Personal booking page per user: `company.flowflex.com/book/jane`
- Custom slug, name, profile photo, title, short bio
- Meeting types: 15-min intro, 30-min demo, 60-min strategy
- Branded with company logo and colours
- Embed on website (iframe or JS snippet)

### Availability & Calendar Sync
- Connect Google Calendar / Outlook Calendar (OAuth)
- Real-time availability pulled from calendar — no double-bookings
- Buffer times between meetings (15 min before/after)
- Working hours per day (Mon 9–17, no Tue appointments after 16:00)
- Minimum scheduling notice (no bookings within 2 hours)
- Maximum advance booking window (no bookings more than 60 days out)

### Meeting Types
- One-on-one (booker + one rep)
- Round-robin (team booking page — distribute meetings across available reps)
- Collective (all team members must be available — for panel meetings)
- Fixed-time (specific date/time slot, like a webinar)

### Booking Flow
- Prospect selects date → time slot → fills form (name, email, company, custom questions)
- Confirmation email to both parties with calendar invite (.ics)
- Reminder emails: 24h before + 1h before
- Rescheduling/cancellation link in confirmation email

### CRM Integration (key differentiator vs Calendly)
- Booking auto-creates/updates contact in CRM
- Booking linked to existing deal (if prospect already in pipeline)
- Custom intake questions mapped to CRM fields (company size → contact property)
- Pre-meeting context card: show rep all CRM data on the contact before meeting starts
- Post-meeting: prompt rep to log outcome and next action

### No-Show Management
- Track no-shows (meeting time passed, no activity logged)
- Auto-send "Sorry we missed you — reschedule?" email after no-show
- No-show rate metric per rep

### Payment-Required Bookings
- Optional: require payment before booking confirmed (Stripe integration)
- Used for: paid consultations, onboarding calls with fee
- Refund flow for cancellations within X hours

---

## Data Model

```erDiagram
    booking_pages {
        ulid id PK
        ulid company_id FK
        ulid owner_id FK
        string slug
        string name
        string type
        json availability
        integer duration_minutes
        string location_type
        string location_value
        boolean requires_payment
        decimal payment_amount
    }

    bookings {
        ulid id PK
        ulid booking_page_id FK
        ulid contact_id FK
        ulid deal_id FK
        string invitee_name
        string invitee_email
        timestamp scheduled_at
        string status
        string cancellation_reason
        string calendar_event_id
        json form_responses
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `MeetingBooked` | Booking confirmed | CRM (create activity, update contact), Notifications (rep + invitee) |
| `MeetingCancelled` | Cancellation received | CRM (update activity), Notifications |
| `MeetingNoShow` | Scheduled time passed, no outcome logged | CRM (flag), Notifications (rep) |

---

## Permissions

```
crm.scheduler.manage-own-pages
crm.scheduler.view-team-bookings
crm.scheduler.manage-team-pages
```

---

## Competitors Displaced

Calendly · Chili Piper · HubSpot Meetings · SavvyCal · Doodle · YouCanBookMe

---

## Related

- [[MOC_CRM]]
- [[entity-contact]]
- [[MOC_Communications]] — Booking & Appointment in Communications is for customer-facing service booking; this is sales-specific CRM scheduling
