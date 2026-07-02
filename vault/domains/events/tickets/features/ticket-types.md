---
domain: events
module: tickets
feature: ticket-types
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Ticket Types

Define the ticket types offered for an event: pricing, quantity, and sales windows.

## Behaviour

- Per event: name, `price_cents` (0 = free), currency, `quantity_available` (null = unlimited), `sales_start`/`sales_end`.
- Multiple types model early-bird / tiered pricing via distinct sales windows *(assumed)*.
- Sold-out when `quantity_sold >= quantity_available`.

## UI

- **Kind**: simple-resource (relation manager)
- **Page**: Ticket types relation manager on `EventResource` edit.
- **Layout**: table (name, price, sold/available, sales window, status); inline create/edit form.
- **Key interactions**: add type → set price + quantity + window; sold-out badge auto-computed.
- **States**: empty (no types → "add a ticket type") · loading (skeleton) · error (validation) · selected (edit form).
- **Gating**: `events.tickets.manage`.

## Data

- Owns / writes: `ev_tickets` only.
- Reads: parent event (Events service).
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: ticket types read by the public purchase flow + landing.
- Shared entity: `ev_events` (read).

## Unknowns

- Early-bird auto price-switch vs. separate windowed types *(assumed)* — see [[../unknowns]].

## Related

- [[../_module|Tickets]] · [[ticket-purchase]] · [[discount-codes]]
