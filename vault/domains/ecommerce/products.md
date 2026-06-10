---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.products
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files]
soft-depends: [operations.inventory, ecommerce.variants, core.import, finance.tax]
fires-events: []
consumes-events: []
patterns: [money, search]
tables: [ec_products, ec_categories]
permission-prefix: ecommerce.products
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Product Catalogue

Product records with pricing, images, categories, and inventory linkage. The core catalogue of the storefront — the E-commerce anchor, build first in `/ecommerce`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, image gallery |
| Soft | [[domains/operations/inventory\|operations.inventory]] | stock from ops item link; internal `stock_quantity` field otherwise |
| Soft | [[domains/ecommerce/variants\|ecommerce.variants]] | per-variant SKU/price/stock |
| Soft | [[domains/core/data-import\|core.import]], [[domains/finance/tax-management\|finance.tax]] | bulk import, tax classes |

---

## Core Features

- Product record: name, slug, description, SKU, price, compare-at price, category, status
- Rich description (Tiptap, purified), image gallery (Media Library)
- Categories (tree) and collections *(assumed: categories only v1)*
- Status: draft / active / archived
- Inventory linkage: `ops_item_id` when operations active (stock read/reserved via `StockService`); else internal `stock_quantity`
- SEO fields: meta title, description
- Slugs via `spatie/laravel-sluggable`
- Pricing in minor units; tax class per product
- Digital vs physical product flag (digital skips fulfilment/shipping)

---

## Data Model

### ec_products

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| slug | string | unique per company |
| description | text | purified |
| sku | string | unique per company |
| price_cents / compare_at_cents | bigint / nullable | compare-at > price when set |
| category_id | ulid nullable FK | |
| status | string default `draft` | draft/active/archived |
| is_digital | boolean default false | |
| tax_class | string nullable | finance.tax class |
| stock_quantity | int nullable | internal stock (when no ops link) |
| ops_item_id | ulid nullable | operations link |
| meta_title / meta_description | string nullable | |
| deleted_at | timestamp nullable | |

### ec_categories — id, company_id (indexed), name, slug (unique per company), parent_category_id nullable (cycle-checked)

---

## DTOs

### CreateProductData — name, sku (unique), price_cents (min:0), compare_at_cents? (> price), category_id?, is_digital, tax_class?, stock_quantity?/ops_item_id? (one or the other), description (purified), images[]

## Services & Actions

- `ProductStock::available(Product $p, ?Variant $v): int` — single stock API (ops or internal)
- `ProductStock::reserve/release/deduct` — delegates to `StockService` when linked

---

## Filament

**Nav group:** Catalogue

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `EcProductResource` | #1 CRUD resource | gallery, Tiptap, publish action |
| `EcCategoryResource` | #1 CRUD resource | tree |

Storefront browse: Vue + Inertia (ui-strategy row #16; storefront module owns rendering).

---

## Permissions

`ecommerce.products.view-any` · `ecommerce.products.create` · `ecommerce.products.update` · `ecommerce.products.publish` · `ecommerce.products.manage-categories`

---

## Search & Realtime

Meilisearch: name, description (stripped), sku, category — public storefront search filters `status = active` + company. No realtime.

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Duplicate SKU/slug rejected
- [ ] Stock API: ops-linked reads StockService; internal uses field
- [ ] Compare-at must exceed price
- [ ] Draft invisible on storefront + public search
- [ ] Description purified; category cycle rejected

---

## Build Manifest

```
database/migrations/xxxx_create_ec_categories_table.php
database/migrations/xxxx_create_ec_products_table.php
app/Models/Ecommerce/{EcProduct,EcCategory}.php
app/Data/Ecommerce/CreateProductData.php
app/Support/Ecommerce/ProductStock.php
app/Providers/Ecommerce/EcommerceServiceProvider.php
app/Filament/Ecommerce/Resources/{EcProductResource,EcCategoryResource}.php
database/factories/Ecommerce/{EcProductFactory,EcCategoryFactory}.php
tests/Feature/Ecommerce/{ProductTest,ProductStockTest}.php
```

---

## Related

- [[domains/ecommerce/variants]]
- [[domains/ecommerce/orders]]
- [[domains/operations/inventory]]
