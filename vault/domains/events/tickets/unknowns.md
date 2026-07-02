---
domain: events
module: tickets
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Tickets — Unknowns

## Assumed Items

- Stripe Connect-vs-direct-keys follows the shared ecommerce decision *(assumed)* — not independently ratified for Events.
- Early-bird pricing is modelled as multiple ticket types with sales windows, not an automatic price switch *(assumed)*.
- Discount codes are simple per-event percent/fixed with `max_uses` *(assumed)* — no stacking, no per-attendee limits specified.
- Finance revenue posting is a manual invoice link in v1 *(assumed)*.
- The Stripe webhook is routed through shared per-domain event-type handling *(assumed)*.

## Open Questions

- Should refunds be partial (per-line) or full-only?
- Tax handling on ticket price (VAT-inclusive vs. added) — defer to [[../../finance/tax-management/_module|Finance Tax]]?
- Transfer / resale of a purchased ticket to another attendee — supported?
- Per-ticket-type discount eligibility (some codes valid only on some tickets)?
- Should ticket revenue auto-post to the GL once Finance is active, replacing the manual bridge?
