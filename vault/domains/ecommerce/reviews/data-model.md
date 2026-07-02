---
domain: ecommerce
module: reviews
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Reviews — Data Model

Owns `ec_reviews`.

## `ec_reviews`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `product_id` | ulid | FK → `ec_products` |
| `customer_contact_id` | ulid nullable | crm.contacts link |
| `customer_email` | string | |
| `customer_name` | string | |
| `order_id` | ulid nullable | verified link; unique `(order_id, product_id)` |
| `rating` | int | 1–5 |
| `title` | string | purified |
| `body` | text | purified |
| `status` | string default `pending` | pending / approved / rejected |
| `is_verified` | boolean | order-linked |
| `helpful_count` | int default 0 | |
| `merchant_reply` | text nullable | purified |
| `review_token` | uuid | unique — request-mail link |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Unique:** `(order_id, product_id)`, `(review_token)`.

## ERD

```mermaid
erDiagram
    ec_products ||--o{ ec_reviews : "reviewed"
    ec_orders }o--o| ec_reviews : "verified by"
    crm_contacts }o--o| ec_reviews : "author (soft)"

    ec_reviews {
        ulid id PK
        ulid company_id
        ulid product_id FK
        ulid customer_contact_id
        string customer_email
        string customer_name
        ulid order_id FK
        int rating
        string title
        text body
        string status
        boolean is_verified
        int helpful_count
        text merchant_reply
        uuid review_token
        timestamp deleted_at
    }
    ec_products { ulid id PK }
    ec_orders { ulid id PK }
```
