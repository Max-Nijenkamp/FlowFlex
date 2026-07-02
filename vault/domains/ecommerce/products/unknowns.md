---
domain: ecommerce
module: products
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Products — Unknowns

## Assumed Items

- `status` is a plain enum string, no state-machine class *(assumed)*.
- Categories only for v1; collections deferred *(assumed)*.
- Max 3 variant options per product (see [[../../variants/_module|Variants]]) *(assumed)*.

## Open Questions

- Should archived products be purged after a retention window, or kept indefinitely for order history?
- Multi-currency pricing per product, or single store currency in v1? (storefront settings imply single currency.)
- Do digital products need a downloadable-asset model (license keys, file delivery), or is the flag alone enough for v1?

> [!warning] UNVERIFIED
> Tax-class values are read as free-text labels from `finance.tax-management`; the exact contract (label vs id) is not yet pinned.
