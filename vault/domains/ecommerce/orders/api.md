---
domain: ecommerce
module: orders
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Orders — API / DTOs

## `CreateOrderData` (storefront checkout)

| Field | Type | Rules |
|---|---|---|
| `customer` | object | `{email required, name}` |
| `lines[]` | array | min:1; each `{product_id, variant_id?, quantity > 0}` — variant required when product has variants; stock validated |
| `coupon_code` | string | nullable |
| `shipping_address` | object | required unless all lines digital |

## `FulfilData`

| Field | Type | Rules |
|---|---|---|
| `order_id` | ulid | must be `paid` |
| `line_ids[]` | array | nullable (partial fulfilment) |
| `tracking_number` | string | nullable |

## `OrderData` (output)

Read model returned by `place`/queries: order number, status, fulfilment status, totals breakdown, lines (snapshot), customer, timeline.

## `OrderService` (via `OrderServiceInterface`)

- `place(CreateOrderData): OrderData`
- `markPaid(...)` — fires `CheckoutCompleted`
- `fulfil(FulfilData)` · `cancel(...)` · `refund(amount_cents, restock)`

## Public / Portal Endpoints

Checkout POSTs `CreateOrderData` through the storefront's public Vue + Inertia flow ([[../../storefront/_module|storefront]]); the server re-validates cart stock/prices before `place`. No direct public Orders API.
