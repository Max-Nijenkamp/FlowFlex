---
domain: ecommerce
module: storefront
type: data-model
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Storefront — Data Model

Owns `ec_storefront_pages` and the `StorefrontSettings` (spatie/laravel-settings) bag. Cart state is session-based (see [[../../abandoned-cart/_module|Abandoned Cart]] for the persisted `ec_carts` capture).

## `ec_storefront_pages`

| Column | Type | Notes |
|---|---|---|
| `id` | ulid | PK |
| `company_id` | ulid | Indexed, `BelongsToCompany` |
| `title` | string | |
| `slug` | string | unique per company |
| `body` | text | purified |
| `is_published` | boolean | only published shown publicly |
| `deleted_at` | timestamp nullable | `SoftDeletes` |

## `StorefrontSettings` (settings bag, not a table)

- `theme_config` (name, logo, colours, currency, languages)
- `navigation` (menu tree of categories + pages)
- `checkout_config` (required fields, guest toggle, terms)
- `shipping_options` (flat rate, free-over threshold)

## ERD

```mermaid
erDiagram
    companies ||--o{ ec_storefront_pages : "has"
    companies ||--|| StorefrontSettings : "configured by"

    ec_storefront_pages {
        ulid id PK
        ulid company_id
        string title
        string slug
        text body
        boolean is_published
        timestamp deleted_at
    }
    StorefrontSettings {
        json theme_config
        json navigation
        json checkout_config
        json shipping_options
    }
```
