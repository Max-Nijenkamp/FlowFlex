---
domain: ecommerce
module: variants
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Variants — Unknowns

## Assumed Items

- Max 3 option types per product *(assumed)*.
- SKU suffixing scheme `base-SKU-VALUE` *(assumed)*.
- Out-of-stock variants are hidden/disabled on the storefront in v1 *(assumed)*; notify-me deferred.

## Open Questions

- Should deleting an option value cascade-delete affected variants, or block if any have orders?
- Per-variant weight/dimensions for shipping — needed in v1, or product-level only?
- Notify-me / back-in-stock signup for out-of-stock variants — which module owns it?
