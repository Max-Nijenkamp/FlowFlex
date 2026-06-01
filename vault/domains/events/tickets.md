---
type: module
domain: Events Management
panel: events
module-key: events.tickets
status: planned
color: "#4ADE80"
---

# Tickets

Paid and free ticket types for events with pricing, quantity limits, and Stripe payment for paid tickets.

## Core Features

- Ticket type: name, price, quantity available, sales start/end, per-event
- Free and paid tickets
- Paid tickets via Stripe (raw SDK)
- Early-bird / tiered pricing (date-based price changes)
- Quantity limits and sold-out handling
- Discount codes for tickets
- Ticket PDF with QR code (`spatie/laravel-pdf` + `simplesoftwareio/simple-qrcode`)
- Refund handling for cancellations
- Revenue tracking per event

## Data Model

| Table | Key Columns |
|---|---|
| `ev_tickets` | company_id, event_id, name, price_cents, currency, quantity_available, quantity_sold, sales_start, sales_end |
| `ev_ticket_purchases` | company_id, ticket_id, registration_id, amount_cents, stripe_payment_intent_id, status |

## Filament

**Nav group:** Events

- Ticket types as relation manager on `EventResource`
- `TicketSalesWidget` — revenue + sold counts

## Cross-Domain / Security

- Stripe payments (see [[architecture/security]] webhook verification)
- Revenue can post to Finance

## Related

- [[domains/events/registrations]]
- [[domains/events/events]]
- `spatie/laravel-pdf`, `brick/money`
