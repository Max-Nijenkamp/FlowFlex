---
domain: events
module: tickets
feature: discount-codes
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Discount Codes

Per-event discount codes (percent or fixed) applied at purchase, with usage limits.

## Behaviour

- Code is unique per event; `type` percent/fixed, `value`, optional `max_uses`, tracked `used_count` *(assumed)*.
- Applied at purchase → discount computed via brick/money → `amount_cents` reflects the reduced total.
- Rejected when expired, over `max_uses`, or unknown; validation endpoint is throttled to prevent enumeration.

## UI

- **Kind**: simple-resource
- **Page**: discount-codes relation/resource under the event's ticket settings.
- **Layout**: table (code, type, value, used/max); create/edit form.
- **Key interactions**: create code → set type + value + max_uses; used_count shown read-only.
- **States**: empty (no codes) · loading (skeleton) · error (duplicate code per event) · selected (edit).
- **Gating**: `events.tickets.manage`.

## Data

- Owns / writes: `ev_ticket_discounts` only (`used_count` incremented on successful purchase).
- Reads: parent event (Events service).
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: applied during [[ticket-purchase|Ticket Purchase]].
- Shared entity: `ev_events` (read).

## Unknowns

- Stacking, per-attendee limits, per-ticket-type eligibility — all unspecified *(assumed: none)*; see [[../unknowns]].

## Related

- [[../_module|Tickets]] · [[ticket-purchase]] · [[ticket-types]]
