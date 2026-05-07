---
tags: [flowflex, domain/communications, booking, appointments, scheduling, phase/5]
domain: Communications
panel: communications
color: "#0284C7"
status: planned
last_updated: 2026-05-07
---

# Booking & Appointment Scheduling

Calendly-style booking pages. Clients and colleagues book time without the back-and-forth email chains. Auto-creates meetings, sends confirmations, and syncs with Google Calendar.

**Who uses it:** All employees (create booking pages), CRM contacts and external visitors (book time)
**Filament Panel:** `communications`
**Depends on:** [[CRM — Contact & Company Management]], [[Meeting & Video Integration]], Google Calendar API
**Phase:** 5
**Build complexity:** High — 3 resources, 2 pages, 3 tables

---

## Features

- **Booking page builder** — each tenant creates one or more booking pages with name, duration, description, buffer before/after, and availability rules
- **Shareable booking link** — each booking page gets a public URL (e.g. `yourco.flowflex.app/book/max-intro`) that CRM contacts or external visitors can access without logging in
- **Availability configuration** — define weekly availability windows per booking page (stored as JSON); supports multiple slots per day
- **Availability overrides** — block specific dates as unavailable, or add custom slots for one-off exceptions
- **Buffer times** — configurable preparation and wrap-up time before and after each appointment; prevents back-to-back scheduling
- **Google Calendar sync** — check Google Calendar for conflicts; create calendar event on booking confirmed (deferred: two-way sync)
- **Appointment confirmation** — `AppointmentBooked` event fires on booking; sends confirmation email to both the booker (CRM contact) and the host (tenant)
- **Meeting URL generation** — if meeting platform configured, auto-generate Google Meet / Zoom link on booking; included in confirmation email
- **Reminder emails** — reminder sent 24 hours before the appointment; second reminder 1 hour before
- **Cancellation flow** — booker can cancel via unique link in confirmation email; `AppointmentCancelled` event fires and notifies host
- **Rescheduling** — booker can reschedule from the cancellation link; original appointment cancelled and new one created
- **Round-robin team booking** — team booking pages distribute appointments across all available team members; assign via `tenant_id` round-robin logic
- **Appointment history** — host sees full history of past and upcoming appointments per booking page; filterable by status

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `booking_pages`
| Column | Type | Notes |
|---|---|---|
| `tenant_id` | ulid FK | owner → tenants |
| `name` | string | e.g. "30-min Intro Call" |
| `slug` | string unique per company | |
| `description` | text nullable | shown on booking page |
| `duration_minutes` | integer | |
| `buffer_before` | integer default 0 | minutes |
| `buffer_after` | integer default 0 | minutes |
| `is_active` | boolean default true | |
| `availability` | json | weekly schedule: {mon: [{from: "09:00", to: "17:00"}], ...} |
| `platform` | enum nullable | `google_meet`, `zoom`, `teams`, `in_person` |
| `location` | string nullable | address for in-person |
| `min_notice_hours` | integer default 24 | minimum advance booking |
| `max_future_days` | integer default 60 | how far ahead bookers can schedule |

### `booking_appointments`
| Column | Type | Notes |
|---|---|---|
| `booking_page_id` | ulid FK | → booking_pages |
| `tenant_id` | ulid FK | assigned host → tenants |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `booker_name` | string | captured at booking |
| `booker_email` | string | |
| `start_at` | timestamp | |
| `end_at` | timestamp | |
| `status` | enum | `pending`, `confirmed`, `cancelled`, `completed`, `rescheduled` |
| `meeting_url` | string nullable | |
| `notes` | text nullable | booker's notes |
| `cancellation_reason` | string nullable | |
| `cancelled_at` | timestamp nullable | |
| `cancel_token` | string unique | for cancel/reschedule link |
| `meeting_id` | ulid FK nullable | → meetings (if created) |

### `booking_availability_overrides`
| Column | Type | Notes |
|---|---|---|
| `booking_page_id` | ulid FK | → booking_pages |
| `date` | date | |
| `is_unavailable` | boolean default true | blocks the day |
| `custom_slots` | json nullable | [{from: "10:00", to: "11:00"}] override slots |
| `reason` | string nullable | e.g. "Annual leave" |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `AppointmentBooked` | `booking_appointment_id`, `crm_contact_id` | Confirmation email to booker + host; creates `meeting` record |
| `AppointmentCancelled` | `booking_appointment_id` | Cancellation notification to host |

---

## Events Consumed

None — Booking pages are triggered by external visitors or tenants following shared links.

---

## Permissions

```
communications.booking-pages.view
communications.booking-pages.create
communications.booking-pages.edit
communications.booking-pages.delete
communications.booking-appointments.view
communications.booking-appointments.cancel
communications.booking-appointments.complete
communications.booking-availability-overrides.view
communications.booking-availability-overrides.create
communications.booking-availability-overrides.delete
```

---

## Related

- [[Communications Overview]]
- [[Meeting & Video Integration]]
- [[Events & Webinars]]
- [[CRM — Contact & Company Management]]
