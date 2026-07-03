---
domain: ecommerce
module: variants
feature: generate-variants
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Generate Variants

Define option types on a product, generate the purchasable variant matrix, and bulk-edit each variant's SKU / price / stock.

## Behaviour

1. Define up to 3 options, each with a name and a list of values (Size: S/M/L).
2. Generate: `VariantService::generate` builds the cartesian product of values, creating only combinations that don't already exist (idempotent).
3. Each variant gets a SKU (suffixed from the base *(assumed)*), optional price override (null = product price), stock quantity, optional image.
4. Bulk-edit prices/stock across the whole set in a table.
5. Duplicate combination or SKU is rejected.

## UI

- **Kind**: simple-resource (relation manager on `EcProductResource`)
- **Page**: Variants tab of the product edit screen (`/ecommerce/products/{id}/edit`), nav group **Catalogue**.
- **Layout**: an Options panel (repeater: name + values) above a variants table (combination, SKU, price override, stock, image). A "Generate variants" button materialises the matrix.
- **Key interactions**: edit options → "Generate variants" → table populates (existing rows preserved); inline-edit cells; bulk-select → set price/stock.
- **States**: empty (no options → "define options to generate variants") · loading (generating) · error (duplicate SKU/combination toast) · selected (rows checked for bulk edit).
- **Gating**: `ecommerce.variants.manage`.

## Data

- Owns / writes: `ec_product_options`, `ec_variants` only.
- Reads: parent `ec_products` (price/stock fallback).
- Cross-domain writes: none — variant stock reserved/deducted via `ProductStock` → `StockService` at order time, never by writing `ops_*` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: variant lines to [[../../orders/_module|Orders]]; variant selector to [[../../storefront/_module|Storefront]].
- Shared entity: parent product (`ecommerce.products`).

## Test Checklist

### Unit
- [ ] Cartesian product of {S,M,L}×{Red,Blue} yields exactly 6 combinations.
- [ ] More than 3 option types is rejected *(assumed max 3)*.

### Feature (Pest)
- [ ] Re-running generate is idempotent — existing combinations are preserved, only missing ones created.
- [ ] Duplicate SKU (per company) or duplicate `(product_id, option_values)` combination is rejected.
- [ ] A variant with null `price_cents` resolves to the parent product price.

### Livewire
- [ ] "Generate variants" action is gated on `ecommerce.variants.manage`; denied without it.
- [ ] Bulk-edit sets price/stock across selected rows and surfaces the duplicate-SKU error inline.

## Unknowns

- Cascade behaviour when removing an option value (see [[../unknowns]]).

## Related

- [[../_module|Product Variants]] · [[../../products/_module|Products]]
