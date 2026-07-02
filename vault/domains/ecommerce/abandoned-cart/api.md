---
domain: ecommerce
module: abandoned-cart
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Abandoned Cart — API / DTOs

## DTOs

No public-input DTO beyond the storefront cart snapshot captured at checkout start; the recovery link restores the session cart from `ec_carts.items`.

## `CartRecoveryService`

- `detect()` — flip stale `active` carts to `abandoned`.
- `advance()` — send due recovery steps (once each), stop on conversion/recovery.

## Public / Portal Endpoints (guest guard, signed)

| Route | Guard | Notes |
|---|---|---|
| `GET /shop/{company-slug}/recover/{token}` | public/guest + signed URL | `RestoreCartController`; validates `recovery_token` (single-use capability); `throttle:public`; restores session cart |

The 3rd recovery mail may embed a single-use coupon created via the promotions service (when active).
