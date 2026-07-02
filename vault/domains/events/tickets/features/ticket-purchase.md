---
domain: events
module: tickets
feature: ticket-purchase
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Ticket Purchase

The public paid-ticket flow: Stripe payment, atomic sold-count, registration confirmation, and PDF ticket with QR.

## Behaviour

- Attendee selects a ticket during registration → `TicketService::purchase` increments `quantity_sold` atomically (guarding sold-out) → creates a Stripe PaymentIntent (idempotency key).
- Stripe webhook `payment_intent.succeeded` (signature-verified) → purchase `paid` → `RegistrationService::confirm` → queue `GenerateTicketPdfJob` → `TicketMail` (PDF with registration QR + `.ics`).
- Purchases outside the sales window or when sold out are rejected with an attendee-facing message.

## UI

- **Kind**: public-vue
- **Page**: purchase panel embedded in the event landing (`/e/{company}/{slug}`) — Vue + Inertia + Stripe Elements, ui-strategy row #16.
- **Layout**: ticket select → attendee fields → discount code → Stripe Elements card → pay; success screen with ticket download.
- **Key interactions**: apply discount (live recalculated total, brick/money) → pay → PaymentIntent → confirmation on webhook; sold-out disables the CTA.
- **States**: empty (tickets on sale) · loading (payment processing spinner) · error (declined card / sold out / window closed → inline) · selected (chosen ticket) · success (paid + ticket ready).
- **Gating**: guest guard; rate-limited.

## Data

- Owns / writes: `ev_tickets` (`quantity_sold`), `ev_ticket_purchases`, `ev_ticket_discounts` (`used_count`).
- Reads: registration to confirm (Registrations service); ticket + discount (own).
- Cross-domain writes: NONE — registration confirmation is via `RegistrationService::confirm` (same-domain service), never a direct `ev_registrations` write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: Stripe webhook (external).
- Feeds: `RegistrationService::confirm` (registrations); revenue feeds [[../../event-analytics/_module|Event Analytics]].
- Shared entity: `ev_registrations` (written only by Registrations).

## Unknowns

- Tax-inclusive vs. added on the displayed price — see [[../unknowns]].

## Related

- [[../_module|Tickets]] · [[ticket-types]] · [[refunds]] · [[../../registrations/features/public-registration|Public Registration]]
