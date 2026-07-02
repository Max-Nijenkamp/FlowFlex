---
domain: ecommerce
module: products
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Products — Decisions

## ADR: Single `ProductStock` API over ops vs internal stock

- **Context:** Stock can live in `operations.inventory` (when linked via `ops_item_id`) or as a plain internal `stock_quantity` field.
- **Decision:** One support class `ProductStock` fronts both. When `ops_item_id` is set it delegates to `StockService`; otherwise it uses the internal field. Callers (orders, storefront) never branch on the source.
- **Consequences:** Products never writes `ops_*` tables ([[../../../../security/data-ownership]]); operations stays the sole owner of its stock ledger.

## ADR: Categories tree only for v1 (collections deferred)

- **Context:** Catalogue organisation could use categories (tree) and/or collections (rule-based sets).
- **Decision:** v1 ships categories only (`ec_categories`, cycle-checked). Collections *(assumed)* deferred.
- **Consequences:** Simpler catalogue; storefront navigation is category-driven.

## ADR: Prices stored in minor units

- **Decision:** `price_cents` / `compare_at_cents` are integer minor units, arithmetic via `brick/money`. Compare-at must exceed price when set.
- **Consequences:** No float rounding drift; consistent with orders/payments money handling.
