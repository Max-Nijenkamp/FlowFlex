---
type: module
domain: E-commerce & Sales Channels
panel: ecommerce
cssclasses: domain-ecommerce
phase: 5
status: planned
migration_range: 600000–649999
last_updated: 2026-05-09
---

# Promotions & Discount Engine

Flash sales, BOGO offers, tiered discounts, bundle pricing, and loyalty-based promotions. The rule engine that powers all promotional logic across the storefront.

---

## Features

### Promotion Types
- **Flash sale**: time-limited price reduction, countdown timer on storefront
- **BOGO**: buy X get Y free (or discounted)
- **Tiered quantity discount**: buy 3 = 10% off, buy 6 = 20% off
- **Bundle pricing**: specific product combination = reduced price
- **Spend threshold**: spend €100, get free shipping / free gift
- **Category promotion**: all items in category X at Y% off
- **Customer segment promotion**: VIP customers see different price
- **Loyalty tier discount**: Platinum members get 15% on everything

### Promotion Rules Engine
- Start/end datetime per promotion
- Max redemptions cap (global + per customer)
- Stackability rules (which promotions can combine)
- Priority order (which promotion wins when multiple apply)
- Exclusions (specific products/categories excluded from sale)
- Geographic restrictions

### Storefront Integration
- Promotional banner auto-shows during active promotions
- Crossed-out original price + discounted price
- Countdown timer for flash sales
- "Spend €X more for free gift" progress bar in cart

### Planning & Scheduling
- Promotion calendar view
- Scheduled activation (set-and-forget)
- A/B test two promotion mechanics
- Performance estimate before launch (based on historical conversion)

### Reporting
- Revenue impact per promotion
- Units sold during promotion vs baseline
- Margin impact
- Discount per order average

---

## Data Model

```erDiagram
    promotions {
        ulid id PK
        ulid company_id FK
        string name
        string type
        json rules
        json conditions
        decimal discount_value
        string discount_type
        integer max_uses
        timestamp starts_at
        timestamp ends_at
        boolean is_active
    }

    promotion_usages {
        ulid id PK
        ulid promotion_id FK
        ulid order_id FK
        ulid contact_id FK
        decimal discount_applied
        timestamp used_at
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `PromotionStarted` | Activation datetime reached | Storefront (update prices), Notifications (marketing alert) |
| `PromotionEnded` | End datetime reached | Storefront (revert prices) |
| `PromotionRedeemed` | Applied to order | Analytics (track usage) |

---

## Permissions

```
ecommerce.promotions.view-any
ecommerce.promotions.create
ecommerce.promotions.manage
```

---

## Related

- [[MOC_Ecommerce]]
- [[gift-cards-vouchers]]
- [[MOC_Marketing]] — promotions feed into email marketing campaigns
