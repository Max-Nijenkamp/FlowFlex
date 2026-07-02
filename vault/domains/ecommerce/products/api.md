---
domain: ecommerce
module: products
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Products — API / DTOs

## `CreateProductData` (spatie/laravel-data)

| Field | Type | Rules |
|---|---|---|
| `name` | string | required |
| `sku` | string | required, unique per company |
| `price_cents` | int | required, min:0 |
| `compare_at_cents` | int | nullable, `> price_cents` |
| `category_id` | ulid | nullable, exists in company |
| `is_digital` | bool | default false |
| `tax_class` | string | nullable (finance.tax label) |
| `stock_quantity` | int | nullable — mutually exclusive with `ops_item_id` |
| `ops_item_id` | ulid | nullable — mutually exclusive with `stock_quantity` |
| `description` | text | nullable, purified |
| `images[]` | file[] | image MIME only (jpg/png/webp), max size enforced, stored under `companies/{company_id}/` |

## `ProductStock` support API (read/command bridge)

- `ProductStock::available(Product $p, ?Variant $v = null): int`
- `ProductStock::reserve/release/deduct(...)` — delegates to `operations.inventory` `StockService` when `ops_item_id` set; otherwise mutates internal `stock_quantity`.

## Public / Portal Endpoints

None here. Public product browse/search endpoints are owned by [[../../storefront/_module|storefront]] (Vue + Inertia), reading only `status = active` products for the company.
