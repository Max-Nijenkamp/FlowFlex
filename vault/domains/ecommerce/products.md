---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.products
status: planned
color: "#4ADE80"
---

# Product Catalogue

Product records with pricing, images, categories, and inventory linkage. The core catalogue of the storefront.

## Core Features

- Product record: name, slug, description, SKU, price, compare-at price, category, status
- Rich description (Tiptap), image gallery (Media Library)
- Categories and collections
- Status: draft / active / archived
- Inventory linkage: track stock (links to Operations inventory if active, else internal stock field)
- SEO fields: meta title, description
- Slugs via `spatie/laravel-sluggable`
- Pricing in minor units; tax class per product
- Digital vs physical product flag

## Data Model

| Table | Key Columns |
|---|---|
| `ec_products` | company_id, name, slug, description, sku, price_cents, compare_at_cents, category_id, status, is_digital, tax_class, stock_quantity |
| `ec_categories` | company_id, name, slug, parent_category_id |

## Filament

**Nav group:** Catalogue

- `ProductResource` — list, create (gallery + description), edit, publish
- `CategoryResource` — category tree
- Import via Core Data Import

## Related

- [[domains/ecommerce/variants]]
- [[domains/ecommerce/orders]]
- [[domains/operations/inventory]]
