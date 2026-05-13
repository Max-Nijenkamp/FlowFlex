---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.promotions
status: planned
color: "#4ADE80"
---

# Promotions

> Discount codes, automatic promotions, buy-X-get-Y offers, spend thresholds, and flash sales with a rules engine and usage tracking.

**Panel:** `ecommerce`
**Module key:** `ecommerce.promotions`

## What It Does

Promotions is the discount rules engine for the ecommerce storefront. It handles both automatic promotions (applied silently at checkout based on qualifying conditions) and code-based promotions (customer enters a code). The rules engine supports percentage off, fixed amount off, free shipping, buy-X-get-Y, spend-threshold offers, category-wide discounts, and customer-segment pricing. Usage is tracked per order so merchants can measure ROI and enforce redemption limits.

## Features

### Core
- Promotion types: percentage discount, fixed amount off, free shipping, buy-X-get-Y (free or discounted), spend threshold (spend €X, get free gift/shipping), category discount
- Discount codes: generate single-use, multi-use, or bulk unique codes (one per customer in a segment)
- Automatic promotions: no code required — applied automatically when qualifying conditions are met at checkout
- Start and end datetime: scheduled activation; expired promotions deactivate automatically
- Usage limits: maximum redemptions globally and per customer
- Stackability: configure whether multiple promotions can combine on the same order

### Advanced
- Flash sale: time-limited price reduction with countdown timer on the product page and storefront banner
- Tiered quantity discounts: buy 1 = 0% off, buy 3 = 10% off, buy 6 = 20% off — configured as price tiers per product
- Customer segment promotions: apply promotions only to a specific customer tag or segment (VIP, wholesale, repeat buyer)
- Geographic restrictions: limit promotion to customers in specified countries or shipping zones
- Exclusions: specific products or collections excluded from a promotion
- Promotion performance: revenue during promotion period, total discount given, orders using code, margin impact

### AI-Powered
- Performance estimate: before activating a promotion, estimate the expected order volume and margin impact based on historical conversion data
- Optimal discount depth: suggest the minimum discount percentage needed to drive a target uplift in conversion rate

## Data Model

```erDiagram
    ec_promotions {
        ulid id PK
        ulid company_id FK
        string name
        string promotion_type
        string code
        boolean is_automatic
        decimal discount_value
        string discount_type
        json conditions
        json exclusions
        integer max_uses_global
        integer max_uses_per_customer
        boolean stackable
        timestamp starts_at
        timestamp ends_at
        boolean is_active
        timestamps timestamps
    }

    ec_promotion_usages {
        ulid id PK
        ulid promotion_id FK
        ulid order_id FK
        ulid customer_id FK
        decimal discount_applied
        timestamp used_at
    }

    ec_promotions ||--o{ ec_promotion_usages : "tracked in"
```

| Table | Purpose |
|---|---|
| `ec_promotions` | Promotion rules, conditions, and limits |
| `ec_promotion_usages` | Per-order redemption records |

## Permissions

```
ecommerce.promotions.view-any
ecommerce.promotions.create
ecommerce.promotions.update
ecommerce.promotions.manage-codes
ecommerce.promotions.delete
```

## Filament

**Resource class:** `PromotionResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `PromotionCalendarPage` (timeline of all scheduled promotions), `PromotionPerformancePage`
**Widgets:** `ActivePromotionsWidget`, `PromotionUsageSummaryWidget`
**Nav group:** Marketing

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Discounts | Discount codes and automatic promotions |
| WooCommerce Coupons | Code and automatic coupon system |
| Bold Discounts | Advanced tiered and automatic discounts |
| Klaviyo Promotions | Segment-targeted discount codes |

## Related

- [[orders]] — promotions applied at checkout and recorded on orders
- [[gift-cards]] — gift cards function differently from discounts; handled separately
- [[bundles]] — bundle pricing is a distinct promotional format
- [[analytics]] — promotion revenue impact tracked in ecommerce analytics
- [[../marketing/email-marketing]] — promotional codes distributed via email campaigns
