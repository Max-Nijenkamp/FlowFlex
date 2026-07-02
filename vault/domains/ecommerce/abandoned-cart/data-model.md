---
domain: ecommerce
module: abandoned-cart
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Abandoned Cart — Data Model

Owns `ec_carts` + `ec_cart_recovery_emails`.

## `ec_carts`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `customer_contact_id` | ulid nullable | crm.contacts |
| `customer_email` | string | |
| `items` | jsonb | snapshot |
| `total_cents` | bigint | |
| `currency` | string(3) | |
| `status` | string default `active` | active/abandoned/recovered/converted |
| `last_activity_at` | timestamp | drives detection |
| `recovery_token` | uuid | unique — signed restore link |
| `order_id` | ulid nullable | conversion link |

## `ec_cart_recovery_emails`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `cart_id` | ulid | FK → `ec_carts` |
| `company_id` | ulid | Indexed |
| `step` | int | 1–3, unique per cart |
| `sent_at` | timestamp | |
| `opened_at` / `clicked_at` | timestamp nullable | |

**Unique:** `(cart_id, step)`, `(recovery_token)`.

## ERD

```mermaid
erDiagram
    ec_carts ||--o{ ec_cart_recovery_emails : "recovery steps"
    ec_orders }o--o| ec_carts : "converts (read)"

    ec_carts {
        ulid id PK
        ulid company_id
        ulid customer_contact_id
        string customer_email
        jsonb items
        bigint total_cents
        string currency
        string status
        timestamp last_activity_at
        uuid recovery_token
        ulid order_id
    }
    ec_cart_recovery_emails {
        ulid id PK
        ulid cart_id FK
        ulid company_id
        int step
        timestamp sent_at
        timestamp opened_at
        timestamp clicked_at
    }
    ec_orders { ulid id PK }
```
