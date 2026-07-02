---
domain: crm
module: appointment-scheduling
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# CRM Appointment Scheduling

Public booking pages for reps, round-robin team scheduling, and calendar sync. Prospects self-book meetings.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module Key

```
module-key:        crm.scheduling
priority:          v1
panel:             crm
permission-prefix: crm.scheduling
tables:            crm_meeting_types, crm_bookings, crm_availability
encrypted-fields:  crm_availability.calendar_connection
```

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../contacts/_module\|Contacts]] | A booking find-or-creates a contact. |
| Hard | [[../activities/_module\|Activities]] | A booking logs a meeting activity. |
| Hard | [[../../../infrastructure/module-catalog\|core.billing]] | Module gating (`hasModule`). |
| Hard | [[../../../security/authn-authz\|core.rbac]] | Permissions and role scoping. |
| Hard | [[../../../infrastructure/mail\|foundation.email]] | Confirmation and reminder emails. |

## Core Features

- Meeting types (name, duration, location video/phone/in-person, buffer time).
- Public booking page per rep (Vue + Inertia) — prospect picks a slot.
- Availability (working hours + calendar busy times Google/Outlook sync) — v1: working hours only; OAuth calendar sync = v1.x fast-follow *(assumed — OAuth scope creep)*.
- Round-robin (distribute bookings across a team).
- Calendar sync two-way Google/Outlook (deferred with the above).
- Booking confirmation email + `.ics` invite (spatie/icalendar-generator).
- Video link generation (manual link field v1; Zoom/Meet API later *(assumed)*).
- Reminders before meeting (24h *(assumed)*).
- Paid bookings via Stripe (optional, consultations).
- Booking creates an activity and links to the contact.

## See features/

- [[features/public-booking|Public booking]]
- [[features/round-robin|Round-robin assignment]]
- [[features/calendar-sync|Calendar sync]]

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

## Test Checklist

- [ ] Tenant isolation + module gating enforced.
- [ ] Slot list respects working hours / buffers / existing bookings.
- [ ] Concurrent booking of same slot → second gets `SlotTakenException`.
- [ ] Round-robin distributes to least-loaded rep.
- [ ] Booking creates contact (find-or-create) + activity + `.ics` mail.
- [ ] Paid type requires successful PaymentIntent before confirm.
- [ ] Reminder fires once.
- [ ] Public endpoints rate-limited.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | Contact find-or-create API | [[../contacts/_module\|crm.contacts]] | Booking find-or-creates a contact. |
| Fires | `AppointmentBooked` | [[../activities/_module\|crm.activities]] | Auto-logs a meeting activity. |
| Fires | `AppointmentBooked` | [[../sequences/_module\|crm.sequences]] | Auto-halts an active sequence. |
| Fires | `AppointmentCancelled` | crm.activities · crm.sequences | Cancellation propagated. |
| Consumes | Calendar OAuth grant *(assumed)* | core/integrations provider | Encrypted token for calendar-sync. |
| Reads | serves Events domain *(assumed)* | events (P3) | Shared booking surface. |

**Data ownership:** `appointment-scheduling` writes only `crm_meeting_types`, `crm_bookings`, `crm_availability`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../contacts/_module|Contacts]]
- [[../activities/_module|Activities]]
- [[../../../architecture/patterns/encryption]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../glossary]]
