---
domain: events
module: registrations
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Registrations

Attendee registration: public sign-up, capacity enforcement, waitlist, confirmation (+`.ics`), and check-in. Fires `EventRegistrationReceived` → CRM.

## Module-key

| Field | Value |
|---|---|
| key | `events.registrations` |
| priority | p3 |
| panel | events |
| permission-prefix | `events.registrations` |
| tables | `ev_registrations` |
| encrypted | `attendee_name`, `attendee_email`, `custom_answers` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../events/_module\|events.events]] | Registrations per published event |
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../foundation/email-setup/_module\|foundation.email]] | Confirmation mails |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | Async mail + jobs |
| Soft | [[../../crm/contacts/_module\|crm.contacts]] | Consumes `EventRegistrationReceived` → find-or-create contact |
| Soft | [[../tickets/_module\|events.tickets]] | Paid registration confirms on ticket purchase |

## Core Features

- **Public registration form** per event (embedded on the Vue landing page).
- **Status lifecycle** — `registered → confirmed → attended | no_show | cancelled`, plus `waitlisted`.
- **Atomic capacity enforcement** + automatic waitlist when full; FIFO promotion on cancellation.
- **Confirmation email** with `.ics` invite; free events auto-confirm, paid confirm on payment.
- **QR check-in** at the event (camera scan or manual), confirmed-only.
- **Fires `EventRegistrationReceived`** → CRM contact.
- **Custom per-event questions** (encrypted answers).

## See features/

- [[features/public-registration|Public Registration]] — the public sign-up form + capacity/waitlist logic.
- [[features/check-in|Check-In]] — QR scan / manual check-in custom page.
- [[features/registration-admin|Registration Admin]] — the internal attendee list, statuses, and export.

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's registrations data
- [ ] Module gating: artifacts hidden when `events.registrations` inactive
- [ ] Capacity atomic: concurrent registrations at limit → waitlist, never overshoot.
- [ ] Duplicate email per event rejected (via `attendee_email_hash`).
- [ ] Free auto-confirms + `.ics` mail; paid confirms on purchase only.
- [ ] Cancellation promotes first waitlisted + notifies.
- [ ] QR check-in confirmed-only; invalid/foreign QR rejected.
- [ ] `EventRegistrationReceived` fires with the contract payload.
- [ ] No-show marking post-event.
- [ ] Public form rate-limited.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Fires | `EventRegistrationReceived` | crm.contacts | CRM listener find-or-creates a contact from attendee email/name |
| Reads | published event + capacity | events.events | Register targets a published event within its capacity |
| Consumes (callback) | `RegistrationService::confirm` | events.tickets | Paid registration confirmed on ticket purchase (same-domain service call from Tickets) |

**Data ownership:** `events.registrations` writes only `ev_registrations`. The CRM contact is created by CRM's **own** listener reacting to `EventRegistrationReceived` — registrations never writes `crm_contacts`. Ticket purchase confirms a registration via this module's own `confirm()` service, not by Tickets writing `ev_registrations` ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../events/_module|Events]] · [[../tickets/_module|Tickets]] · [[../../crm/contacts/_module|CRM Contacts]] · [[../../../architecture/event-bus]]
- [[../_index|Events MOC]]
