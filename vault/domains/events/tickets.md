---
type: module
domain: Events Management
domain-key: events
panel: events
module-key: events.tickets
status: planned
priority: p3
depends-on: [events.events, events.registrations, core.billing, core.rbac, foundation.queues]
soft-depends: [finance.invoicing]
fires-events: []
consumes-events: []
patterns: [money, pdf, queues]
tables: [ev_tickets, ev_ticket_purchases]
permission-prefix: events.tickets
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Tickets

Paid and free ticket types for events with pricing, quantity limits, and Stripe payment for paid tickets.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/events/events\|events.events]] + [[domains/events/registrations\|events.registrations]] | ticket types per event; purchase confirms registration |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, PDF jobs |
| Soft | [[domains/finance/invoicing\|finance.invoicing]] | revenue posting (manual link v1 *(assumed)*) |

---

## Core Features

- Ticket type: name, price, quantity available, sales start/end, per-event
- Free and paid tickets
- Paid tickets via Stripe Payment Intents (raw SDK, idempotency keys; same Connect-vs-keys ADR as ecommerce *(assumed: shared decision)*)
- Early-bird pricing: multiple ticket types with sales windows *(assumed: windows, not auto price switch)*
- Quantity limits + sold-out handling (atomic `quantity_sold`)
- Discount codes for tickets *(assumed: simple per-event codes, percent/fixed)*
- Ticket PDF with QR code (registration QR) — `spatie/laravel-pdf` + `simple-qrcode`
- Refund handling on cancellation (Stripe refund → registration cancelled)
- Revenue tracking per event

---

## Data Model

### ev_tickets

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), event_id FK | ulid | |
| name | string | |
| price_cents | bigint | 0 = free |
| currency | string(3) | |
| quantity_available | int nullable | null = unlimited |
| quantity_sold | int default 0 | atomic |
| sales_start / sales_end | timestamp nullable | |
| deleted_at | timestamp nullable | |

### ev_ticket_purchases — id, company_id (indexed), ticket_id FK, registration_id FK unique, amount_cents, currency, stripe_payment_intent_id nullable unique, status (pending/paid/refunded), discount_code nullable
### ev_ticket_discounts *(formalised)* — id, company_id, event_id, code (unique per event), type (percent/fixed), value, max_uses, used_count

---

## DTOs

### PurchaseTicketData (public, via registration flow) — registration_id (registered, unconfirmed), ticket_id (in sales window, not sold out), discount_code? — validated with messages ("This ticket is sold out.")
### RefundTicketData — purchase_id (paid), reason

## Services & Actions

- `TicketService::purchase(...)` — atomic sold-count increment, discount application (brick/money), PaymentIntent; webhook success → purchase paid + `RegistrationService::confirm` + PDF ticket mail
- `TicketService::refund(...)` — Stripe refund (idempotency key) + registration cancel + sold-count decrement
- Webhook handled by shared Stripe webhook routing *(assumed: per-domain event types)*

---

## Filament

**Nav group:** Events

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| Ticket types relation manager | on EventResource | windows, quantities |
| `TicketSalesWidget` | #6 widget | revenue + sold per event |
| Purchases list | #1 (read-only) | refund action |

Purchase flow: public registration pages (Vue) — ui-strategy row #16, Stripe Elements.

---

## Permissions

`events.tickets.manage` · `events.tickets.refund`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Concurrent purchases at quantity limit never oversell (atomic)
- [ ] Outside sales window rejected
- [ ] Payment success confirms registration + mails PDF ticket with QR
- [ ] Discount math (brick/money); max_uses enforced
- [ ] Refund: Stripe refund + registration cancelled + count decremented
- [ ] Webhook idempotent; Stripe mocked

---

## Build Manifest

```
database/migrations/xxxx_create_ev_tickets_table.php
database/migrations/xxxx_create_ev_ticket_purchases_table.php
database/migrations/xxxx_create_ev_ticket_discounts_table.php
app/Models/Events/{Ticket,TicketPurchase,TicketDiscount}.php
app/Data/Events/{PurchaseTicketData,RefundTicketData}.php
app/Services/Events/TicketService.php
app/Jobs/Events/GenerateTicketPdfJob.php
app/Mail/Events/TicketMail.php
app/Filament/Events/Widgets/TicketSalesWidget.php
database/factories/Events/{TicketFactory,TicketPurchaseFactory}.php
tests/Feature/Events/{TicketPurchaseTest,TicketOversellTest,TicketRefundTest}.php
```

---

## Related

- [[domains/events/registrations]]
- [[domains/events/events]]
- [[architecture/security]] — webhook verification
