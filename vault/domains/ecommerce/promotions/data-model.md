---
domain: ecommerce
module: promotions
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Promotions — Data Model

Owns `ec_coupons` + `ec_promotions` + `ec_coupon_redemptions`.

## `ec_coupons`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `code` | string | unique per company, case-insensitive |
| `discount_type` | string | percent / fixed |
| `discount_value` | int | basis points (percent) or cents (fixed) |
| `min_order_cents` | bigint nullable | |
| `usage_limit` | int nullable | total uses |
| `per_customer_limit` | int nullable | |
| `used_count` | int default 0 | atomic increment |
| `valid_from` / `valid_until` | date nullable | |
| `is_active` | boolean | |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Unique:** `(company_id, lower(code))`.

## `ec_promotions`

| Column | Type | Notes |
|---|---|---|
| `id`, `company_id` | ulid | Indexed |
| `name` | string | |
| `rule` | jsonb | registry-validated |
| `discount` | jsonb | |
| `valid_from` / `valid_until` | date nullable | |
| `is_active` | boolean | |

## `ec_coupon_redemptions`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `coupon_id` | ulid | FK → `ec_coupons` |
| `company_id` | ulid | Indexed |
| `order_id` | ulid | FK → `ec_orders` |
| `customer_email` | string | for per-customer limit |
| `redeemed_at` | timestamp | |

## ERD

```mermaid
erDiagram
    ec_coupons ||--o{ ec_coupon_redemptions : "redeemed via"
    ec_orders ||--o| ec_coupon_redemptions : "applies"

    ec_coupons {
        ulid id PK
        ulid company_id
        string code
        string discount_type
        int discount_value
        bigint min_order_cents
        int usage_limit
        int per_customer_limit
        int used_count
        date valid_from
        date valid_until
        boolean is_active
        timestamp deleted_at
    }
    ec_promotions {
        ulid id PK
        ulid company_id
        string name
        jsonb rule
        jsonb discount
        boolean is_active
    }
    ec_coupon_redemptions {
        ulid id PK
        ulid coupon_id FK
        ulid company_id
        ulid order_id FK
        string customer_email
        timestamp redeemed_at
    }
    ec_orders {
        ulid id PK
    }
```
