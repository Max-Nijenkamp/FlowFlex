---
domain: crm
module: price-management
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Price Management — Decisions

## ADR: Price resolution order

**Context:** A single product can have multiple applicable prices depending on the account, segment, and active promotions.

**Decision:** `PricingService::resolve()` walks a fixed precedence: account's assigned book → segment book → default book → product standard price. The first match supplies the base price; volume tiers and the margin check are then layered on top.

**Consequences:** Deterministic, testable resolution. Assigning a book to an account always wins over segment/default.

## ADR: Exactly-one-default price book invariant

**Context:** The resolution fallback needs a guaranteed default.

**Decision:** Enforce exactly one `is_default = true` price book per company (partial unique index + service guard on assign) *(assumed enforcement mechanism)*.

**Consequences:** Setting a new default clears the old one; resolution always terminates at a real book before falling to standard price.

## ADR: Promotions via entry validity window *(assumed)*

**Context:** Time-bound promotional prices must not require a separate promotions table.

**Decision:** Model promos as `crm_price_book_entries` rows carrying `valid_from` / `valid_until`. An entry applies only when the resolution date falls inside its window *(assumed)*.

**Consequences:** Multiple dated entries per (book, product) via the `(price_book_id, product_id, valid_from)` unique key; overlapping windows are an open question. Flagged in [[unknowns]].

## ADR: Absorbed from former Pricing Management domain

**Context:** The standalone Pricing Management domain was folded into CRM during the rebuild scope reset.

**Decision:** Product catalogue, price books, volume discounts, and CPQ now live as the `crm.pricing` module. Tables are prefixed `crm_`.

**Consequences:** Quotes/Deals consume pricing intra-domain; no cross-domain event bus needed for price resolution.
