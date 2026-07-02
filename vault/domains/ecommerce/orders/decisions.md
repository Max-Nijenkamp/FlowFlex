---
domain: ecommerce
module: orders
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Orders — Decisions

## ADR: `CheckoutCompleted` is the Finance bridge (no direct writes)

- **Context:** A completed sale must reach Finance to record revenue.
- **Decision:** On `markPaid`, Orders fires `CheckoutCompleted` (`company_id`, `order_id`, `customer_email`, `total_cents`, `currency`). Finance's own queued listener writes finance's tables. Orders never touches finance tables.
- **Consequences:** Bounded contexts preserved; the E-commerce → Finance edge is a single, contract-checked event ([[../../../../security/data-ownership]]).

## ADR: Prices snapshot at order time

- **Decision:** Line `unit_price_cents`/`line_total_cents` and order totals are frozen when the order is placed; later product price changes don't mutate historical orders.
- **Consequences:** Correct receipts + audit; totals reproducible.

## ADR: Stock reserved on place, deducted on paid, released on cancel

- **Decision:** `place` reserves via `ProductStock`; `markPaid` deducts; `cancel` releases; refund optionally restocks.
- **Consequences:** No oversell between placement and payment; ops remains stock owner.

## ADR: `CartAbandoned` event dropped — recovery is same-domain

- **Decision:** The v1 spec's cross-domain `CartAbandoned` event is not fired; abandoned-cart recovery lives in-domain ([[../../abandoned-cart/_module|Abandoned Cart]]).
- **Consequences:** Fewer cross-domain contracts; recovery detection is a scheduled in-domain job.

## ADR: Auto-complete 14 days after fulfilment (assumed)

- **Decision:** `AutoCompleteOrdersCommand` transitions `fulfilled → completed` 14d post-fulfilment *(assumed)*.
- **Consequences:** Orders self-close without manual action; window configurable later.
