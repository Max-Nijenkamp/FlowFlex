---
domain: events
module: tickets
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tickets

Paid and free ticket types per event, with pricing, quantity limits, discount codes, Stripe payment, and refunds.

## Module-key

| Field | Value |
|---|---|
| key | `events.tickets` |
| priority | p3 |
| panel | events |
| permission-prefix | `events.tickets` |
| tables | `ev_tickets`, `ev_ticket_purchases`, `ev_ticket_discounts` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../events/_module\|events.events]] | Ticket types per event |
| Hard | [[../registrations/_module\|events.registrations]] | Purchase confirms a registration |
| Hard | [[../../core/billing/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | PDF generation jobs |
| Soft | [[../../finance/invoicing/_module\|finance.invoicing]] | Revenue posting (manual link v1 *(assumed)*) |

## Core Features

- **Ticket types** — name, price, quantity available, sales start/end, per event; free + paid.
- **Stripe payment** — Payment Intents (raw SDK, idempotency keys; same Connect-vs-keys ADR as ecommerce *(assumed)*).
- **Early-bird** — multiple ticket types with sales windows *(assumed: windows, not auto price switch)*.
- **Quantity limits** — atomic `quantity_sold`, sold-out handling.
- **Discount codes** — per-event percent/fixed with `max_uses` *(assumed)*.
- **Ticket PDF with QR** (registration QR) — `spatie/laravel-pdf` + `simple-qrcode`.
- **Refunds** — Stripe refund → registration cancelled → sold-count decremented.

## See features/

- [[features/ticket-types|Ticket Types]] — pricing, quantities, sales windows.
- [[features/ticket-purchase|Ticket Purchase]] — the public Stripe purchase flow + PDF ticket.
- [[features/discount-codes|Discount Codes]] — per-event codes.
- [[features/refunds|Refunds]] — Stripe refund + registration cancellation.

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Concurrent purchases at the quantity limit never oversell (atomic).
- [ ] Outside sales window rejected.
- [ ] Payment success confirms registration + mails PDF ticket with QR.
- [ ] Discount math (brick/money); `max_uses` enforced.
- [ ] Refund: Stripe refund + registration cancelled + count decremented.
- [ ] Webhook signature verified; idempotent; Stripe mocked.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | `RegistrationService::confirm` / `cancel` | events.registrations | Payment success confirms; refund cancels (same-domain service calls) |
| Reads/Commands | draft invoice | finance.invoicing | `CreateSponsorInvoiceAction`-style manual bridge for revenue *(assumed, soft)* |
| Inbound | Stripe webhook | (external) | Signature-verified payment-confirmation events |

**Data ownership:** `events.tickets` writes only `ev_tickets`, `ev_ticket_purchases`, `ev_ticket_discounts`. Registration state changes go through the registrations service (`confirm`/`cancel`), never a direct write to `ev_registrations`. Finance revenue posting is via a manual invoice action (finance owns `fin_*`) ([[../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../registrations/_module|Registrations]] · [[../events/_module|Events]] · [[../../finance/invoicing/_module|Finance Invoicing]] · [[../../../architecture/security]]
- [[../_index|Events MOC]]
