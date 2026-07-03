---
domain: ecommerce
module: promotions
feature: manage-coupons
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Manage Coupons

Create and manage discount codes and automatic promotions, with usage tracking and a redemption report.

## Behaviour

- Coupon: code (unique per company), percent/fixed value, min order, usage + per-customer limits, validity window, active flag.
- Auto-promotion: a JSONB rule (threshold / product / category) + discount, registry-validated on save; unknown rule types rejected.
- Usage columns show `used_count` vs `usage_limit`; a redemptions relation lists each order.

## UI

- **Kind**: simple-resource
- **Page**: `CouponResource` (`/ecommerce/coupons`) + `EcPromotionResource` (`/ecommerce/promotions`), nav group **Marketing**.
- **Layout**: coupons table (code, type, value, used/limit, window, active) with a redemptions relation manager; promotion form uses a rule-builder repeater.
- **Key interactions**: create/edit coupon or promotion; toggle active; view redemptions; validity + limit validation inline.
- **States**: empty (no coupons → "create your first code" CTA) · loading (table skeleton) · error (duplicate code / until < from toast) · selected (row → edit + redemptions).
- **Gating**: view `ecommerce.promotions.view-any`; edit `ecommerce.promotions.manage`.

## Data

- Owns / writes: `ec_coupons`, `ec_promotions` only (`ec_coupon_redemptions` written at redeem time).
- Reads: product/category ids (products), segment ids (crm.customer-segments, soft).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: coupons/promotions consumed by [[apply-discount]] at checkout.
- Shared entity: products/categories (products), segments (crm.customer-segments).

## Test Checklist

### Unit
- [ ] Auto-promotion JSONB rule registry-validated; unknown rule type rejected
- [ ] Coupon code unique per company; percent/fixed value validation

### Feature (Pest)
- [ ] Usage columns reflect `used_count` vs `usage_limit`; redemptions relation lists orders
- [ ] Tenant isolation + permission: coupons manageable only in own company with the manage verb

### Livewire
- [ ] Coupon form validates window/limits; canAccess() hides resource without permission or `ecommerce.promotions` inactive

## Unknowns

- Bulk campaign-code generation (see [[../unknowns]]).

## Related

- [[../_module|Promotions & Coupons]] · [[apply-discount]]
