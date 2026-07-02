---
domain: ecommerce
module: storefront
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Storefront — API / DTOs

## `CartData` (storefront session)

| Field | Type | Rules |
|---|---|---|
| `lines[]` | array | each `{product_id, variant_id?, qty > 0}` |
| `coupon_code` | string | nullable |

Server-validated against live stock/prices at every step. Checkout posts `CreateOrderData` to the orders module.

## `StorefrontService`

- `catalog(filters): Collection` — active, company-scoped products (cached).
- checkout orchestration: validate → `DiscountEngine::apply` → `OrderService::place` → `EcPaymentService::createIntent` → confirmation.

## Public / Portal Endpoints (Vue + Inertia, guest guard)

| Route | Page | Notes |
|---|---|---|
| `GET /shop/{company-slug}` | `Shop/Index.vue` | catalog browse/search |
| `GET /shop/{company-slug}/p/{product-slug}` | `Shop/Product.vue` | product + variants + reviews |
| `GET/POST /shop/{company-slug}/cart` | `Shop/Cart.vue` | session cart, re-validated |
| `GET/POST /shop/{company-slug}/checkout` | `Shop/Checkout.vue` | posts `CreateOrderData`; Stripe when active |
| `GET /shop/{company-slug}/confirmation/{order}` | `Shop/Confirmation.vue` | post-order |

All scoped to the company by `{company-slug}`; only `status = active` products and `is_published` pages are served.
