---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.variants
status: planned
color: "#4ADE80"
---

# Product Variants

Product options (size, colour) generating purchasable variants, each with its own SKU, price, and stock.

## Core Features

- Option types: size, colour, material, custom (per product)
- Variant generation: combinations of options (e.g. Red/Large, Blue/Small)
- Per-variant: SKU, price override, stock quantity, image
- Variant selection on storefront product page
- Bulk edit variant prices/stock
- Out-of-stock variant handling

## Data Model

| Table | Key Columns |
|---|---|
| `ec_product_options` | product_id, company_id, name, values (json) |
| `ec_variants` | product_id, company_id, sku, option_values (json), price_cents, stock_quantity, image_media_id |

## Filament

**Nav group:** Catalogue

- Variant management as relation manager on `ProductResource`
- Variant matrix generator

## Related

- [[domains/ecommerce/products]]
- [[domains/ecommerce/orders]]
