---
domain: ecommerce
module: orders
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Orders — Data Model

Owns `ec_orders` + `ec_order_lines` + `ec_order_events`. Never writes finance tables ([[../../../../security/data-ownership]]).

## `ec_orders`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `order_number` | string | unique per company |
| `customer_contact_id` | ulid nullable | CRM link (soft) |
| `customer_email` | string | snapshot |
| `customer_name` | string | snapshot |
| `status` | string default `pending` | state machine |
| `fulfilment_status` | string default `unfulfilled` | unfulfilled/partial/fulfilled |
| `subtotal_cents` / `discount_cents` / `tax_cents` / `shipping_cents` / `total_cents` | bigint | minor units |
| `currency` | string(3) | |
| `coupon_code` | string nullable | |
| `shipping_address` | jsonb nullable | |
| `tracking_number` | string nullable | |
| `deleted_at` | timestamp nullable | kept 7y per [[../../../../architecture/data-lifecycle]] |

**Indexes:** `(company_id, status)`, `(company_id, customer_email)`, unique `(company_id, order_number)`.

## `ec_order_lines`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `order_id` | ulid | FK → `ec_orders` |
| `company_id` | ulid | Indexed |
| `product_id` | ulid | FK → `ec_products` |
| `variant_id` | ulid nullable | FK → `ec_variants` |
| `description` | string | snapshot |
| `quantity` | int | `> 0` |
| `unit_price_cents` | bigint | snapshot |
| `line_total_cents` | bigint | snapshot |

## `ec_order_events` (append-only timeline)

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `order_id` | ulid | FK → `ec_orders` |
| `company_id` | ulid | Indexed |
| `type` | string | placed/paid/fulfilled/cancelled/refunded/note |
| `notes` | text nullable | |
| `occurred_at` | timestamp | |

## ERD

```mermaid
erDiagram
    ec_orders ||--o{ ec_order_lines : contains
    ec_orders ||--o{ ec_order_events : "logs"
    ec_products ||--o{ ec_order_lines : "sold as"
    ec_variants }o--o| ec_order_lines : "variant of"
    crm_contacts }o--o| ec_orders : "customer (soft)"

    ec_orders {
        ulid id PK
        ulid company_id
        string order_number
        ulid customer_contact_id
        string customer_email
        string status
        string fulfilment_status
        bigint subtotal_cents
        bigint discount_cents
        bigint tax_cents
        bigint shipping_cents
        bigint total_cents
        string currency
        string coupon_code
        jsonb shipping_address
        string tracking_number
        timestamp deleted_at
    }
    ec_order_lines {
        ulid id PK
        ulid order_id FK
        ulid company_id
        ulid product_id FK
        ulid variant_id FK
        string description
        int quantity
        bigint unit_price_cents
        bigint line_total_cents
    }
    ec_order_events {
        ulid id PK
        ulid order_id FK
        ulid company_id
        string type
        text notes
        timestamp occurred_at
    }
```
