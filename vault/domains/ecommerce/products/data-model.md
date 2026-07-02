---
domain: ecommerce
module: products
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Products — Data Model

Owns `ec_products` + `ec_categories`. No other module writes these ([[../../../../security/data-ownership]]).

## `ec_products`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `name` | string | |
| `slug` | string | unique per company (`spatie/laravel-sluggable`) |
| `description` | text | purified (htmlpurifier) |
| `sku` | string | unique per company |
| `price_cents` | bigint | minor units (`brick/money`) |
| `compare_at_cents` | bigint nullable | must exceed `price_cents` when set |
| `category_id` | ulid nullable | FK → `ec_categories` |
| `status` | string default `draft` | draft / active / archived |
| `is_digital` | boolean default false | skips fulfilment/shipping |
| `tax_class` | string nullable | label read from finance.tax |
| `stock_quantity` | int nullable | internal stock when no ops link |
| `ops_item_id` | ulid nullable | operations.inventory link |
| `meta_title` | string nullable | SEO |
| `meta_description` | string nullable | SEO |
| `created_at` / `updated_at` | timestamps | |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

**Indexes:** `(company_id, status)`, unique `(company_id, slug)`, unique `(company_id, sku)`.

## `ec_categories`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed |
| `name` | string | |
| `slug` | string | unique per company |
| `parent_category_id` | ulid nullable | FK → `ec_categories`; cycle-checked |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## ERD

```mermaid
erDiagram
    ec_categories ||--o{ ec_products : classifies
    ec_categories ||--o{ ec_categories : "parent of"
    ec_products ||--o{ ec_variants : "has variants"
    ops_items }o--o| ec_products : "stock link (read-only)"

    ec_products {
        ulid id PK
        ulid company_id
        string name
        string slug
        string sku
        bigint price_cents
        bigint compare_at_cents
        ulid category_id FK
        string status
        boolean is_digital
        string tax_class
        int stock_quantity
        ulid ops_item_id
        timestamp deleted_at
    }
    ec_categories {
        ulid id PK
        ulid company_id
        string name
        string slug
        ulid parent_category_id FK
    }
    ec_variants {
        ulid id PK
        ulid product_id FK
    }
    ops_items {
        ulid id PK
    }
```
