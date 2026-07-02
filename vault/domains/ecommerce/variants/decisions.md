---
domain: ecommerce
module: variants
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Variants — Decisions

## ADR: Matrix generation is idempotent

- **Context:** Options change over time; re-generating must not duplicate variants.
- **Decision:** `VariantService::generate` computes the cartesian product of option values and inserts only combinations that don't already exist (`option_values` unique per product).
- **Consequences:** Safe to re-run after adding an option value; no duplicate SKUs from double-clicks.

## ADR: Variant price is an override, product price is the fallback

- **Decision:** `ec_variants.price_cents` is nullable; null means "use the product price". Same for stock via `ProductStock`.
- **Consequences:** Simple pricing for uniform variants; per-variant override only when needed.

## ADR: Max 3 options per product (assumed)

- **Decision:** A product may define at most 3 option types (e.g. Size × Colour × Material) *(assumed)*.
- **Consequences:** Bounds the matrix size; revisit if a merchant needs more.
