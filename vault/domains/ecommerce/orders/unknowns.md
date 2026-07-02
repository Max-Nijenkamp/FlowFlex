---
domain: ecommerce
module: orders
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Orders — Unknowns

## Assumed Items

- `fulfilled → completed` auto-transition at +14d *(assumed)*.
- Refund restock is a per-refund flag *(assumed)*.
- Customer PII (email, name, shipping address) stored plaintext *(assumed — no encryption documented)*.

## Open Questions

- Does `CheckoutCompleted` need line-level detail for Finance (per-product revenue), or is the order total enough for v1?
- Should a partial refund emit its own event to Finance (credit note), or is that pulled on demand?
- Order-number scheme: sequential per company, or prefixed random? (unique per company either way.)
- Multi-currency orders, or single store currency? (storefront settings imply single currency per store.)
- GDPR: 7-year retention on soft-deleted orders vs customer erasure requests — reconcile with [[../../../../architecture/data-lifecycle]].
