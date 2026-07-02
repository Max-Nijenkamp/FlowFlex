---
domain: ecommerce
module: reviews
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Reviews — Decisions

## ADR: Verified-purchase gating via order link (default on)

- **Decision:** By default only buyers may review; the review is tied to an `order_id` and deduped per `(order_id, product_id)`. A company setting can open reviews to anyone *(assumed default on)*.
- **Consequences:** Higher review trust; when open, submissions are token/email-based.

## ADR: Signed-link public submission on the guest guard

- **Decision:** The review-request mail carries a signed link validating `review_token`; public submission + helpful votes run on the public/guest guard, rate-limited, separate from the Filament panel guard.
- **Consequences:** No auth required for shoppers; token is the capability. Security-audit HIGH item addressed.

## ADR: Cached average rating, busted on moderation

- **Decision:** `ProductRating::average` caches the average of approved reviews; approve/reject busts the cache.
- **Consequences:** Fast storefront rating display; always reflects the latest moderation state.

## ADR: Request mail once per order at +7d (assumed)

- **Decision:** `ReviewRequestCommand` mails once per order, 7 days after fulfilment *(assumed)*.
- **Consequences:** No spam; timing configurable later.
