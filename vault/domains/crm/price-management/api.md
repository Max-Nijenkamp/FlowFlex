---
domain: crm
module: price-management
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Price Management — API

## DTOs

### CreateProductData (input)

| Field | Type | Rules |
|---|---|---|
| name | string | required |
| sku | string | required, unique per company |
| unit | string | required |
| standard_price_cents | int | required, min:0 |
| cost_cents | int | required, min:0 |

### ResolvePriceData (input)

| Field | Type | Rules |
|---|---|---|
| product_id | ulid | required, exists |
| account_id | ?ulid | optional |
| quantity | decimal | required, min:0 |
| date | ?date | optional; defaults to today |

### PriceResolutionData (output)

| Field | Type | Notes |
|---|---|---|
| price_cents | int | Resolved unit price (minor unit) |
| source_book | ?string | Which book supplied the base price |
| volume_discount_applied | ?float | Percent applied, if any |
| below_margin_warning | bool | True when price < cost + threshold |

## Public / Portal Endpoints

None. Pricing is panel-internal and consumed by Quotes/Deals via `PricingService`.
