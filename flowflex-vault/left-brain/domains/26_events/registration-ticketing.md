---
type: module
domain: Events Management
panel: events
phase: 5
status: planned
cssclasses: domain-events
migration_range: 990500–990999
last_updated: 2026-05-09
---

# Registration & Ticketing

Manage ticket types, pricing, promo codes, and the full registration flow. Payments integrated. Waitlists, group bookings, and refund handling.

---

## Ticket Types

Per event, unlimited ticket types:
| Type | Example | Price |
|---|---|---|
| Early bird | First 100 | €149 |
| Standard | General admission | €249 |
| VIP | Includes dinner | €549 |
| Free | Webinar access | €0 |
| Group | 5-pack | €999 |
| Speaker/Staff | Complimentary | €0 |

Ticket types have:
- Availability window (early bird closes date X)
- Quantity limit
- Visibility (public/private/invite code)
- Assigned sessions (workshop tickets lock to specific session)

---

## Registration Flow

1. Attendee selects ticket type + quantity
2. Fills registration form (custom fields per event: role, company, dietary, T-shirt size)
3. Payment (Stripe / Mollie / bank transfer for groups)
4. Confirmation email + calendar invite + e-ticket (QR code)
5. On capacity: offer waitlist join

---

## Promo Codes

- Percentage or fixed discount
- Single-use or multi-use with usage cap
- Partner codes (track which partner drove registrations)
- Validity dates

---

## Group Bookings

Company buys 10 tickets:
- Nominee manager distributes tickets to team members
- Each team member completes their own profile
- Invoice issued to company (not individual card charges)

---

## Waitlist

When sold out:
- Attendees join waitlist
- On cancellation → next on waitlist auto-offered ticket (24h to accept)
- Configurable: notify all or ranked order

---

## Refunds & Transfers

- Full refund if cancelled > X days before event (configurable)
- Partial refund within window
- Ticket transfer: attendee can transfer to colleague (with event organiser permission setting)
- Transfer triggers new QR code, old code invalidated

---

## Data Model

### `evt_ticket_types`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| event_id | ulid | FK |
| name | varchar(200) | |
| price | decimal(10,2) | |
| currency | char(3) | |
| quantity | int | nullable = unlimited |
| sold | int | |
| available_from | timestamp | nullable |
| available_until | timestamp | nullable |

### `evt_registrations`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| event_id | ulid | FK |
| ticket_type_id | ulid | FK |
| attendee_id | ulid | FK |
| status | enum | registered/waitlisted/cancelled/attended |
| qr_code | varchar(100) | unique |
| paid_at | timestamp | nullable |
| amount_paid | decimal(10,2) | |

---

## Migration

```
990500_create_evt_ticket_types_table
990501_create_evt_registrations_table
990502_create_evt_promo_codes_table
990503_create_evt_waitlist_entries_table
```

---

## Related

- [[MOC_Events]]
- [[event-creation-branding]]
- [[attendee-management]]
- [[event-checkin-app]]
