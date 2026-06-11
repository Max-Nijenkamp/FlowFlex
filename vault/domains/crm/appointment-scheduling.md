---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.scheduling
status: planned
priority: v1
depends-on: [crm.contacts, crm.activities, core.billing, core.rbac, foundation.email]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [encryption, email]
tables: [crm_meeting_types, crm_bookings, crm_availability]
permission-prefix: crm.scheduling
encrypted-fields: ["crm_availability.calendar_connection"]
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Appointment Scheduling

Public booking pages for reps, round-robin team scheduling, and calendar sync. Prospects self-book meetings.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | booking find-or-creates contact |
| Hard | [[domains/crm/activities\|crm.activities]] | booking logs a meeting activity |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/email-setup\|foundation.email]] | gating, permissions, confirmations |

---

## Core Features

- Meeting types: name, duration, location (video/phone/in-person), buffer time
- Public booking page per rep (Vue + Inertia) — prospect picks a slot
- Availability: working hours + calendar busy times (Google/Outlook sync — **v1: working hours only; OAuth calendar sync = v1.x fast-follow** *(assumed — OAuth scope creep)*)
- Round-robin: distribute bookings across a team
- Calendar sync: two-way Google/Outlook (deferred with above)
- Booking confirmation: email + `.ics` calendar invite (spatie/icalendar-generator)
- Video link generation (manual link field v1; Zoom/Meet API later *(assumed)*)
- Reminders before the meeting (24h *(assumed)*)
- Paid bookings via Stripe (optional, for consultations)
- Booking creates an activity + links to contact

---

## Data Model

### crm_meeting_types

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| owner_id | ulid FK users | null = team round-robin *(assumed)* |
| name | string | |
| slug | string | booking URL, unique per company |
| duration_minutes | int | |
| location_type | string | video / phone / in-person |
| video_link | string nullable | static link v1 |
| buffer_minutes | int default 0 | |
| price_cents | bigint default 0 | 0 = free |
| team_user_ids | jsonb nullable | round-robin pool |
| deleted_at | timestamp nullable | |

### crm_bookings

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), meeting_type_id FK | ulid | |
| contact_id | ulid FK | find-or-created |
| assigned_rep_id | ulid FK users | round-robin result |
| scheduled_at | timestamp | no double-booking per rep (unique-ish check in service) |
| status | string default `confirmed` | confirmed / cancelled / completed / no-show |
| stripe_payment_intent_id | string nullable | paid bookings |
| reminded_at | timestamp nullable | |

### crm_availability

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), user_id FK unique | ulid | |
| working_hours | jsonb | per weekday [{start,end}] |
| 🔐 calendar_connection | text nullable | encrypted OAuth blob (v1.x) |

---

## DTOs

### BookSlotData (public)
| Field | Type | Validation |
|---|---|---|
| meeting_type_slug | string | required, exists |
| scheduled_at | CarbonImmutable | required, future, on a free slot (validated in service) |
| name / email | string | required (email: email) |
| notes | ?string | max:1000 |

Rate-limited + honeypot.

## Services & Actions

- `SchedulingService::slots(string $meetingTypeSlug, CarbonImmutable $day): array` — working hours − existing bookings − buffers
- `SchedulingService::book(BookSlotData $data): BookingData` — slot re-validated in transaction (`SlotTakenException`); round-robin = least-bookings-this-week *(assumed)*; find-or-create contact; logs activity; queues confirmation + .ics; Stripe PaymentIntent when priced
- `CancelBookingAction::run(string $bookingId): void` — notifies both sides

---

## Filament

**Nav group:** Activities

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `MeetingTypeResource` | #1 CRUD resource | booking link copy button |
| `BookingResource` | #1 CRUD resource | status actions, no-show marking |
| `AvailabilityPage` | #7 custom page (form) | own working hours |

Public booking page: Vue + Inertia `/book/{company-slug}/{meeting-slug}` — ui-strategy row #16.


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.scheduling.view-any') && BillingService::hasModule('crm.scheduling')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Public/portal guard** (HIGH): Specify the guard for the public booking surface (guest/no-auth route group, isolated from app session guard) and confirm no app/Sanctum session leakage; document the route group's middleware stack.
- **Rate limiter** (medium): Cite a specific named rate limiter for the public booking POST (e.g. RateLimiter::for('public-booking')) covering slot lookup and PaymentIntent creation.

---

## Permissions

`crm.scheduling.view-any` · `crm.scheduling.manage-types` · `crm.scheduling.manage-own-availability`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `BookingReminderCommand` | notifications | every 15 min | 24h window + `reminded_at` null guard |
| Confirmation mail + .ics | notifications | on booking | — |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Slot list respects working hours, buffers, existing bookings
- [ ] Concurrent booking of same slot: second gets `SlotTakenException`
- [ ] Round-robin distributes to least-loaded rep
- [ ] Booking creates contact (find-or-create) + activity + .ics mail
- [ ] Paid type requires successful PaymentIntent before confirm
- [ ] Reminder fires once
- [ ] Public endpoints rate-limited

---

## Build Manifest

```
database/migrations/xxxx_create_crm_meeting_types_table.php
database/migrations/xxxx_create_crm_bookings_table.php
database/migrations/xxxx_create_crm_availability_table.php
app/Models/CRM/{MeetingType,Booking,Availability}.php
app/Data/CRM/{BookSlotData,BookingData}.php
app/Services/CRM/SchedulingService.php
app/Exceptions/CRM/SlotTakenException.php
app/Actions/CRM/CancelBookingAction.php
app/Mail/CRM/{BookingConfirmationMail,BookingReminderMail}.php
app/Console/Commands/CRM/BookingReminderCommand.php
app/Http/Controllers/PublicBookingController.php + resources/js/Pages/Booking/{Show,Confirm}.vue
app/Filament/CRM/Resources/{MeetingTypeResource,BookingResource}.php
app/Filament/CRM/Pages/AvailabilityPage.php
database/factories/CRM/{MeetingTypeFactory,BookingFactory}.php
tests/Feature/CRM/{BookingFlowTest,SlotConcurrencyTest}.php
```

---

## Related

- [[domains/crm/contacts]]
- [[domains/crm/activities]]
- [[architecture/patterns/encryption]]
- [[frontend/_index]]
