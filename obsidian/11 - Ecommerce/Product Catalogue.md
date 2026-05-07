---
tags: [flowflex, domain/ecommerce, products, catalogue, phase/4]
domain: Ecommerce
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-07
---

# Product Catalogue

Centralised product database. One update pushes to storefront, POS, marketplace channels, and quoting simultaneously.

**Who uses it:** Ecommerce team, operations, sales
**Filament Panel:** `ecommerce`
**Depends on:** [[File Storage]], Core
**Phase:** 4
**Build complexity:** High — 5 resources, 1 page, 6 tables

---

## Features

- **Centralised product database** — single product record powers storefront, POS, marketplace listings, quotes, and invoices; no data duplication
- **Variants and attributes** — define attribute sets (size, colour, material) per product; each combination generates a variant with own SKU, barcode, and optional stock management
- **Flexible pricing** — base price plus pricing rules for volume discounts, customer group pricing (B2B/B2C), and time-limited promotional prices
- **Tax class assignment** — assign tax class per product; tax rate resolved at point of sale based on buyer location
- **Image gallery** — multiple images per product with sort order and alt text; images stored to S3 via FileStorageService
- **Brand management** — brand catalogue with logo; filter and report by brand
- **Category tree** — unlimited nesting of categories with parent/child; used for storefront navigation and filtering
- **Digital product flag** — mark a product as digital to trigger [[Digital Products & Downloads]] fulfilment flow instead of shipping
- **SEO metadata** — per-product `seo_title` and `seo_description` for storefront page meta tags
- **Bulk import/export** — CSV/XLSX import for initial catalogue load; export for marketplace channel feeds
- **Barcode support** — EAN/UPC/ISBN barcode field per product and variant; used by POS scanner
- **Stock integration** — products link to inventory; stock status (in stock/low stock/out of stock) shown on storefront
- **Compare-at price** — show original/RRP price crossed out on storefront for sale pricing
- **Audit trail** — all price and status changes logged via `LogsActivity`

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.
> Note: `ec_` prefix used on all tables to avoid conflict with Operations `products` table.

### `ec_products`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string unique per company | |
| `description` | text nullable | |
| `short_description` | text nullable | |
| `sku` | string nullable | |
| `barcode` | string nullable | EAN/UPC/ISBN |
| `category_id` | ulid FK nullable | → ec_product_categories |
| `brand_id` | ulid FK nullable | → ec_brands |
| `cost_price` | decimal(10,2) nullable | |
| `base_price` | decimal(10,2) | |
| `compare_at_price` | decimal(10,2) nullable | RRP / crossed-out price |
| `tax_class` | string nullable | |
| `weight` | decimal(8,3) nullable | kg |
| `is_active` | boolean default true | |
| `is_digital` | boolean default false | |
| `seo_title` | string nullable | |
| `seo_description` | text nullable | |
| `type` | enum | `simple`, `variable` |
| `sort_order` | integer default 0 | |

### `ec_product_variants`
| Column | Type | Notes |
|---|---|---|
| `ec_product_id` | ulid FK | → ec_products |
| `sku` | string nullable | |
| `barcode` | string nullable | |
| `attributes` | json | e.g. {"size": "L", "colour": "Red"} |
| `price_override` | decimal(10,2) nullable | if null, inherits base_price |
| `cost_price_override` | decimal(10,2) nullable | |
| `stock_managed_separately` | boolean default false | |
| `is_active` | boolean default true | |

### `ec_product_categories`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string unique per company | |
| `parent_id` | ulid FK nullable | → ec_product_categories (self-referential) |
| `description` | text nullable | |
| `image_file_id` | ulid FK nullable | → files |
| `sort_order` | integer default 0 | |
| `is_active` | boolean default true | |

### `ec_brands`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `slug` | string unique per company | |
| `description` | text nullable | |
| `logo_file_id` | ulid FK nullable | → files |
| `is_active` | boolean default true | |

### `ec_product_images`
| Column | Type | Notes |
|---|---|---|
| `ec_product_id` | ulid FK | → ec_products |
| `file_id` | ulid FK | → files |
| `sort_order` | integer default 0 | |
| `alt_text` | string nullable | |
| `is_primary` | boolean default false | |

### `ec_pricing_rules`
| Column | Type | Notes |
|---|---|---|
| `ec_product_id` | ulid FK | → ec_products |
| `type` | enum | `volume`, `customer_group`, `promotional` |
| `min_qty` | integer nullable | for volume pricing |
| `customer_group` | string nullable | e.g. "b2b", "vip" |
| `price` | decimal(10,2) | |
| `discount_pct` | decimal(5,2) nullable | alternative to fixed price |
| `valid_from` | date nullable | |
| `valid_until` | date nullable | |
| `is_active` | boolean default true | |

---

## Events Fired

None — Product Catalogue is a data source consumed by other modules.

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `InventoryStockUpdated` | [[Inventory Management]] | Refreshes stock status on product for storefront display |

---

## Permissions

```
ecommerce.ec-products.view
ecommerce.ec-products.create
ecommerce.ec-products.edit
ecommerce.ec-products.delete
ecommerce.ec-product-categories.view
ecommerce.ec-product-categories.create
ecommerce.ec-product-categories.edit
ecommerce.ec-product-categories.delete
ecommerce.ec-brands.view
ecommerce.ec-brands.create
ecommerce.ec-brands.edit
ecommerce.ec-brands.delete
ecommerce.ec-pricing-rules.view
ecommerce.ec-pricing-rules.create
ecommerce.ec-pricing-rules.edit
ecommerce.ec-pricing-rules.delete
```

---

## Related

- [[Ecommerce Overview]]
- [[Order Management]]
- [[Storefront & Checkout]]
- [[Inventory Management]]
- [[Point of Sale]]
- [[Marketplace Channel Sync]]
- [[Digital Products & Downloads]]
