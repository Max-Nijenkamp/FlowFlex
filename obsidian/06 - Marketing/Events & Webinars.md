---
tags: [flowflex, domain/marketing, events, webinars, phase/5]
domain: Marketing
panel: marketing
color: "#DB2777"
status: planned
last_updated: 2026-05-07
---

# Events & Webinars

Build registration pages, manage attendees, and automate event communications. Run in-person events, virtual webinars, or hybrid — with QR code check-in and post-event follow-up built in.

**Who uses it:** Marketing team, event managers, CRM contacts (registrants)
**Filament Panel:** `marketing`
**Depends on:** [[CRM — Contact & Company Management]], [[Email Marketing]]
**Phase:** 5
**Build complexity:** High — 4 resources, 2 pages, 4 tables

---

## Features

- **Event creation** — create events with title, description, type (in-person/virtual/hybrid), location, start/end times, capacity limit, and optional price
- **Registration page** — auto-generated public registration page per event; customisable with event branding, agenda, and speaker bios
- **Paid event registration** — if `is_paid` is true, Stripe checkout is triggered on registration; failed payment blocks registration
- **Waitlist management** — when `max_attendees` is reached, further registrations go to waitlist with position number; auto-promote from waitlist when a spot opens
- **Attendee list** — real-time list of registrants with status (registered/attended/no_show/cancelled); filterable and exportable
- **QR code check-in** — each registrant receives a unique `ticket_code`; scan on arrival via mobile QR scanner in the Filament app to mark `checked_in_at`
- **Session agenda** — add multiple sessions to an event with title, speaker, room, and times; shown on registration page
- **Automated reminder emails** — `EventStartingSoon` event fires at configurable intervals before event (e.g. 24h and 1h); triggers email in [[Email Marketing]]
- **Confirmation email on registration** — `EventRegistrationReceived` fires immediately; triggers confirmation email with QR ticket via [[Email Marketing]]
- **Post-event follow-up** — after event ends, trigger a follow-up email sequence to all attendees (recordings, survey, next event)
- **Attendance metrics** — registered vs attended count, no-show rate, waitlist conversion rate; dashboard widget per event
- **Zoom/Teams integration** — if virtual event, connect a Zoom or Teams meeting; meeting URL auto-sent to registrants
- **Cancel registration** — registrant can cancel via unique link in confirmation email; spot released from capacity; waitlist auto-promoted

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `events`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `description` | text nullable | |
| `type` | enum | `in_person`, `virtual`, `hybrid` |
| `status` | enum | `draft`, `published`, `completed`, `cancelled` |
| `start_at` | timestamp | |
| `end_at` | timestamp | |
| `location` | string nullable | address for in-person |
| `meeting_url` | string nullable | Zoom/Teams link for virtual |
| `platform` | enum nullable | `zoom`, `teams`, `google_meet` |
| `max_attendees` | integer nullable | null = unlimited |
| `current_registrants` | integer default 0 | denormalised count |
| `registration_deadline` | timestamp nullable | |
| `is_paid` | boolean default false | |
| `price` | decimal(10,2) nullable | |
| `currency` | string(3) default 'GBP' | |
| `thumbnail_file_id` | ulid FK nullable | → files |
| `slug` | string unique per company | for registration page URL |
| `tenant_id` | ulid FK nullable | event owner → tenants |

### `event_registrations`
| Column | Type | Notes |
|---|---|---|
| `event_id` | ulid FK | → events |
| `crm_contact_id` | ulid FK | → crm_contacts |
| `status` | enum | `registered`, `attended`, `no_show`, `cancelled` |
| `registered_at` | timestamp | |
| `checked_in_at` | timestamp nullable | |
| `cancelled_at` | timestamp nullable | |
| `ticket_code` | string unique | for QR check-in |
| `payment_reference` | string nullable | Stripe payment intent |
| `amount_paid` | decimal(10,2) nullable | |

### `event_sessions`
| Column | Type | Notes |
|---|---|---|
| `event_id` | ulid FK | → events |
| `title` | string | |
| `speaker` | string nullable | |
| `description` | text nullable | |
| `start_at` | timestamp | |
| `end_at` | timestamp | |
| `room` | string nullable | |
| `sort_order` | integer default 0 | |

### `event_waitlist`
| Column | Type | Notes |
|---|---|---|
| `event_id` | ulid FK | → events |
| `crm_contact_id` | ulid FK | → crm_contacts |
| `position` | integer | queue position |
| `added_at` | timestamp | |
| `promoted_at` | timestamp nullable | when moved off waitlist |
| `notification_sent_at` | timestamp nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `EventRegistrationReceived` | `event_registration_id`, `crm_contact_id` | [[Email Marketing]] (confirmation email with QR ticket) |
| `EventStartingSoon` | `event_id`, `hours_until_start` | [[Email Marketing]] (reminder email to all registered attendees) |

---

## Events Consumed

None — Events & Webinars is triggered by user actions.

---

## Permissions

```
marketing.events.view
marketing.events.create
marketing.events.edit
marketing.events.delete
marketing.events.publish
marketing.events.cancel
marketing.event-registrations.view
marketing.event-registrations.check-in
marketing.event-registrations.export
marketing.event-sessions.view
marketing.event-sessions.create
marketing.event-sessions.edit
marketing.event-waitlist.view
```

---

## Related

- [[Marketing Overview]]
- [[Email Marketing]]
- [[CRM — Contact & Company Management]]
- [[Booking & Appointment Scheduling]]
