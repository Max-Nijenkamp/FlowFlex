---
domain: events
module: tickets
feature: refunds
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Refunds

Refund a paid ticket: Stripe refund, registration cancellation, and sold-count decrement.

## Behaviour

- `TicketService::refund(RefundTicketData)` on a `paid` purchase → Stripe refund (idempotency key) → purchase `refunded` → `RegistrationService::cancel` → decrement `quantity_sold` (freeing the seat / promoting waitlist).
- Idempotent: a replayed refund does not double-decrement.

## UI

- **Kind**: simple-resource (row action)
- **Page**: "Refund" row action on the read-only Purchases list.
- **Layout**: purchases table (attendee, ticket, amount, status); refund confirmation modal with reason.
- **Key interactions**: refund → confirm modal (reason) → Stripe refund → status flips to `refunded`, registration cancelled.
- **States**: empty (no purchases) · loading (refund processing) · error (Stripe failure toast) · selected (purchase row).
- **Gating**: `events.tickets.refund`.

## Data

- Owns / writes: `ev_ticket_purchases` (status), `ev_tickets` (`quantity_sold`).
- Reads: purchase + linked registration.
- Cross-domain writes: NONE — registration cancellation via `RegistrationService::cancel` (same-domain service) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: Stripe refund webhook (external).
- Feeds: `RegistrationService::cancel` (registrations) → waitlist promotion.
- Shared entity: `ev_registrations` (written only by Registrations).

## Test Checklist

### Unit
- [ ] Refund math in minor units; idempotency key derivation

### Feature (Pest)
- [ ] Refund: Stripe refund + registration cancel + `quantity_sold` decrement in one locked transaction; retry with same key refunds once
- [ ] Permission: refund verb enforced; `panel-action` limiter cited on the money mutation
- [ ] Tenant isolation: refunds on own-company purchases only

### Livewire
- [ ] Refund action confirms + reports outcome; hidden without the refund permission

## Unknowns

- Partial vs. full-only refunds — see [[../unknowns]].

## Related

- [[../_module|Tickets]] · [[ticket-purchase]] · [[../../registrations/_module|Registrations]]
