---
type: module
domain: Events Management
panel: events
module-key: events.registrations
status: planned
color: "#4ADE80"
---

# Registrations

> Attendee registration management — ticket purchase, confirmation email, waitlist, and cancellation handling.

**Panel:** `events`
**Module key:** `events.registrations`

---

## What It Does

Registrations manages all attendee sign-ups for an event. When a prospective attendee visits the registration page, they select their ticket type, complete the registration form (including any custom questions configured on the event), and pay if the ticket is paid. They receive a confirmation email with a QR code for check-in. If the event is at capacity, they are added to a waitlist and notified automatically if a place becomes available. Cancellations release the place and optionally trigger a refund.

---

## Features

### Core
- Online registration form: name, email, company, custom questions, ticket type selection
- Paid ticket checkout: Stripe payment integration for paid tickets
- Confirmation email: branded confirmation with event details and a QR code for check-in
- Capacity enforcement: block registration when ticket type is sold out; offer waitlist
- Waitlist management: automatic notification when a waitlisted attendee is promoted to registered
- Cancellation: self-service cancellation link in confirmation email; configurable refund policy

### Advanced
- Group registration: one registrant books for multiple people; co-attendee details collected per seat
- Discount codes: promotional codes for percentage or flat-rate ticket discounts
- Transfer: registered attendee transfers their ticket to another person
- Registration approval: for invitation-only events, registrations require admin approval before confirmation
- CRM linking: new registrant automatically creates or updates a CRM contact

### AI-Powered
- Dropout prediction: flag registrants unlikely to attend based on historical no-show patterns
- Personalised reminder: AI personalises the pre-event reminder email based on registrant profile
- Capacity optimisation: recommend overbooking threshold based on historical attendance rate for this event type

---

## Data Model

```erDiagram
    registrations {
        ulid id PK
        ulid event_id FK
        ulid ticket_type_id FK
        ulid company_id FK
        string attendee_name
        string attendee_email
        string attendee_company
        json custom_answers
        string status
        string qr_code
        decimal amount_paid
        string payment_reference
        boolean is_waitlisted
        timestamp registered_at
        timestamp cancelled_at
        timestamps created_at_updated_at
    }

    registrations }o--|| events : "for"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `registrations` | Attendee registrations | `id`, `event_id`, `ticket_type_id`, `attendee_email`, `status`, `qr_code`, `is_waitlisted`, `amount_paid` |

---

## Permissions

```
events.registrations.view-any
events.registrations.manage
events.registrations.approve
events.registrations.export
events.registrations.issue-refunds
```

---

## Filament

- **Resource:** `App\Filament\Events\Resources\RegistrationResource`
- **Pages:** `ListRegistrations`, `ViewRegistration`
- **Custom pages:** `WaitlistPage`, `RegistrationApprovalPage`, `AttendeeExportPage`
- **Widgets:** `RegistrationCountWidget`, `WaitlistCountWidget`, `RevenueWidget`
- **Nav group:** Participants

---

## Displaces

| Feature | FlowFlex | Eventbrite | Cvent | Hopin |
|---|---|---|---|---|
| Paid ticket checkout | Yes | Yes | Yes | Yes |
| Waitlist management | Yes | Yes | Yes | Yes |
| CRM contact auto-creation | Yes | No | No | No |
| AI dropout prediction | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[events]] — registrations link to event and ticket type records
- [[check-in]] — QR codes from registrations used at check-in
- [[post-event-analytics]] — registration vs attendance rates in analytics
- [[crm/INDEX]] — registrant data creates/updates CRM contacts
