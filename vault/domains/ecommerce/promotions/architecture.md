---
domain: ecommerce
module: promotions
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Promotions — Architecture

## Services & Actions

`DiscountEngine` is the single entry point for orders/checkout.

| Method | Responsibility |
|---|---|
| `apply(CartData $cart, ?string $couponCode): DiscountResult` | validates coupon (active, window, limits, min order, segment), applies auto-promotions, returns discount lines; throws typed exceptions with user messages ("This code has expired.") |
| `redeem(...)` | atomic `used_count` increment + `ec_coupon_redemptions` row at order paid |

Percent discounts are stored/computed in basis points; fixed in cents. All math via `brick/money`.

## Rule Registry

`ec_promotions.rule` + `discount` are JSONB validated against a rule registry *(assumed: threshold + product/category rules v1; BXGY later)*. Unknown rule types are rejected at save.

## Events

None fired/consumed. `DiscountEngine` is called synchronously by orders/checkout. See [[../../../../architecture/event-bus]].

## Filament Artifacts

**Nav group:** Marketing

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `CouponResource` | #1 CRUD resource | tweaks: relation-manager-timeline (redemptions relation) | usage columns (`used_count` / `usage_limit`); active toggle |
| `EcPromotionResource` | #1 CRUD resource | tweaks: inline-relation-repeater (rule-builder) | JSONB rule + discount, registry-validated on save |

**Public storefront (Vue + Inertia):**

- The coupon code field lives on the storefront checkout (Vue + Inertia, [[../../../architecture/ui-strategy]] row #16), which calls `DiscountEngine::apply` server-side — no Filament artifact and no client-side eligibility.

**Access contract (mandatory):** both resources gate on
`canAccess() = Auth::user()->can('ecommerce.promotions.view-any') && BillingService::hasModule('ecommerce.promotions')`
per [[../../../architecture/filament-patterns]] #1. Standard CRUD, no custom pages. The storefront coupon surface is guest-guarded in [[../../storefront/_module|storefront]].

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Coupon / promotion CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| `redeem` — `used_count` increment + redemption row | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the coupon row, re-check total + per-customer limits, increment, insert `ec_coupon_redemptions` — concurrent redemptions cannot exceed `usage_limit` (capacity decrement) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None. Expiry is evaluated live in `DiscountEngine` from `valid_until`.

## Search & Realtime

None.
