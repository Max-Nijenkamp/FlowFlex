---
domain: ecommerce
module: payments
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Payments — Data Model

Owns `ec_payments`. No card data stored locally — only Stripe references.

## `ec_payments`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `order_id` | ulid | FK → `ec_orders` |
| `stripe_payment_intent_id` | string | unique |
| `amount_cents` | bigint | minor units |
| `currency` | string(3) | |
| `status` | string | pending / succeeded / failed |
| `method` | string nullable | card / ideal / sepa |
| `paid_at` | timestamp nullable | |
| `refunded_amount_cents` | bigint default 0 | ≤ `amount_cents`, cumulative |
| `created_at` / `updated_at` | timestamps | |

**Indexes:** `(company_id, order_id)`, unique `(stripe_payment_intent_id)`.

## ERD

```mermaid
erDiagram
    ec_orders ||--o{ ec_payments : "paid by"

    ec_payments {
        ulid id PK
        ulid company_id
        ulid order_id FK
        string stripe_payment_intent_id
        bigint amount_cents
        string currency
        string status
        string method
        timestamp paid_at
        bigint refunded_amount_cents
    }
    ec_orders {
        ulid id PK
        ulid company_id
    }
```
