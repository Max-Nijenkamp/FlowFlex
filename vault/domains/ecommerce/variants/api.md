---
domain: ecommerce
module: variants
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Variants — API / DTOs

## `DefineOptionsData`

| Field | Type | Rules |
|---|---|---|
| `product_id` | ulid | required, in company |
| `options[]` | array | max 3; each `{name, values[] min:1}` |

## `GenerateVariantsData`

| Field | Type | Rules |
|---|---|---|
| `product_id` | ulid | required |

`VariantService::generate` creates missing option-value combinations, skips existing ones (idempotent).

## `UpdateVariantData`

| Field | Type | Rules |
|---|---|---|
| `variant_id` | ulid | required |
| `price_cents` | int | nullable, min:0 (null = product price) |
| `stock_quantity` | int | nullable, min:0 |
| `sku` | string | nullable, unique per company |

## Public / Portal Endpoints

None. Variant selection happens on the storefront product page (owned by [[../../storefront/_module|storefront]]).
