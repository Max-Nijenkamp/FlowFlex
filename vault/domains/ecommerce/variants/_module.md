---
domain: ecommerce
module: variants
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Product Variants

Product options (size, colour, material) generating purchasable variants, each with its own SKU, price override, and stock.

## Module-key

| Field | Value |
|---|---|
| key | `ecommerce.variants` |
| priority | p3 |
| panel | ecommerce |
| permission-prefix | `ecommerce.variants` |
| tables | `ec_product_options`, `ec_variants` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../products/_module\|Products]] | variants belong to products |
| Hard | [[../../core/billing/_module\|Billing]] · [[../../core/rbac/_module\|RBAC]] | gating + permissions |

## Core Features

- **Option types** — size, colour, material, custom (max 3 options per product *(assumed)*).
- **Variant generation** — cartesian product of option values (matrix generator); re-run skips existing combinations.
- **Per-variant fields** — SKU, price override (null = product price), stock quantity, image.
- **Bulk edit** — variant prices/stock in a table.
- **Out-of-stock handling** — variant unselectable on storefront *(assumed: hide/disable v1)*.
- **Fallback** — a product with variants uses product-level price/stock as fallback.

## See features/

- [[features/generate-variants|Generate Variants]] — matrix generation + bulk edit of the variant set.

## Build Manifest

```
database/migrations/xxxx_create_ec_product_options_table.php
database/migrations/xxxx_create_ec_variants_table.php
app/Models/Ecommerce/{ProductOption,Variant}.php
app/Data/Ecommerce/{DefineOptionsData,GenerateVariantsData,UpdateVariantData}.php
app/Services/Ecommerce/VariantService.php
database/factories/Ecommerce/VariantFactory.php
tests/Feature/Ecommerce/VariantTest.php
```

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Generation creates all combinations once; re-run skips existing.
- [ ] Duplicate combination / SKU rejected.
- [ ] Price falls back to product when variant price null.
- [ ] Out-of-stock variant unselectable on storefront.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads/Commands | `ProductStock` (products) → `StockService` | operations.inventory | Variant stock via product link; never writes `ops_*` |
| Feeds | variant lines | ecommerce.orders / storefront | Order lines reference `variant_id` when product has variants |

**Data ownership:** `ecommerce.variants` writes only `ec_product_options` + `ec_variants`. Stock is reached through `ProductStock` / `StockService`, never by writing operations tables ([[../../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../products/_module|Products]] · [[../orders/_module|Orders]] · [[../storefront/_module|Storefront]]
- [[../../../glossary]]
