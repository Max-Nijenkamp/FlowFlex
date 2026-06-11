---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.variants
status: planned
priority: p3
depends-on: [ecommerce.products, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [money]
tables: [ec_product_options, ec_variants]
permission-prefix: ecommerce.variants
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Product Variants

Product options (size, colour) generating purchasable variants, each with its own SKU, price, and stock.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ecommerce/products\|ecommerce.products]] | variants belong to products |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Option types: size, colour, material, custom (per product, max 3 options *(assumed)*)
- Variant generation: combinations of options (matrix generator)
- Per-variant: SKU, price override, stock quantity, image
- Variant selection on storefront product page
- Bulk edit variant prices/stock
- Out-of-stock variant handling (unselectable, optional notify-me later *(assumed: hide/disable v1)*)
- Product with variants: product-level stock/price become fallbacks

---

## Data Model

### ec_product_options — id, product_id FK, company_id, name, values (jsonb array); unique `(product_id, name)`
### ec_variants

| Column | Type | Notes |
|---|---|---|
| id, product_id FK, company_id (indexed) | ulid | |
| sku | string | unique per company |
| option_values | jsonb | {Size: "L", Colour: "Red"}; unique combination per product |
| price_cents | bigint nullable | null = product price |
| stock_quantity | int default 0 | (or ops link via product) |
| image_media_id | ulid nullable | |
| deleted_at | timestamp nullable | |

---

## DTOs

### DefineOptionsData — product_id, options[{name, values[] min:1}] max 3
### GenerateVariantsData — product_id — creates missing combinations, skips existing
### UpdateVariantData — variant_id, price_cents?, stock_quantity?, sku?

## Services & Actions

- `VariantService::generate(...)` — cartesian product of option values, SKU suffixing *(assumed: base-SKU-VALUE)*
- `VariantService::bulkUpdate(array $rows)`
- Order lines reference variant_id when product has variants (orders module validates)

---

## Filament

**Nav group:** Catalogue

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| Variant relation manager | on EcProductResource | matrix generator + bulk-edit table |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('ecommerce.variants.view-any') && BillingService::hasModule('ecommerce.variants')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`ecommerce.variants.manage` (under products permissions umbrella)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Generation creates all combinations once; re-run skips existing
- [ ] Duplicate combination/SKU rejected
- [ ] Price fallback to product when null
- [ ] Out-of-stock variant unselectable on storefront

---

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

---

## Related

- [[domains/ecommerce/products]]
- [[domains/ecommerce/orders]]
