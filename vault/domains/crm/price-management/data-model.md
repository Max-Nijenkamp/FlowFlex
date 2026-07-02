---
domain: crm
module: price-management
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Price Management — Data Model

Owns `crm_products`, `crm_price_books`, `crm_price_book_entries`, and `crm_volume_discounts`.

## crm_products

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed, tenant scope |
| name | string | |
| sku | string | Unique per company |
| description | text nullable | |
| unit | string | piece / hour / month etc. |
| standard_price_cents | bigint | Minor unit |
| cost_cents | bigint | Minor unit; margin guard basis |
| is_active | bool | Default true |
| deleted_at | timestamp nullable | Soft delete |

**Indexes:** `company_id`; unique `(company_id, sku)`.

## crm_price_books

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed, tenant scope |
| name | string | Unique per company |
| currency | string(3) | ISO currency |
| is_default | bool | Exactly one default per company |
| deleted_at | timestamp nullable | Soft delete |

**Indexes:** `company_id`; unique `(company_id, name)`; partial unique on `(company_id, is_default)` where true *(assumed enforcement)*.

## crm_price_book_entries

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed, tenant scope |
| price_book_id | FK | |
| product_id | FK | |
| price_cents | bigint | Minor unit |
| valid_from | date nullable | Promo window start |
| valid_until | date nullable | Promo window end |

**Indexes:** `company_id`; unique `(price_book_id, product_id, valid_from)`.

## crm_volume_discounts

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid | Indexed, tenant scope |
| product_id | FK | |
| min_quantity | decimal(10,2) | Tier threshold |
| discount_percent | decimal(5,2) | |

**Indexes:** `company_id`; unique `(product_id, min_quantity)`.

## ER Diagram

```mermaid
erDiagram
    crm_products {
        ulid id PK
        ulid company_id
        string sku
        bigint standard_price_cents
        bigint cost_cents
        bool is_active
    }
    crm_price_books {
        ulid id PK
        ulid company_id
        string name
        string currency
        bool is_default
    }
    crm_price_book_entries {
        ulid id PK
        ulid company_id
        ulid price_book_id FK
        ulid product_id FK
        bigint price_cents
        date valid_from
        date valid_until
    }
    crm_volume_discounts {
        ulid id PK
        ulid company_id
        ulid product_id FK
        decimal min_quantity
        decimal discount_percent
    }
    crm_products ||--o{ crm_price_book_entries : "priced in"
    crm_price_books ||--o{ crm_price_book_entries : "contains"
    crm_products ||--o{ crm_volume_discounts : "tiered by"
```
