---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.products
status: planned
color: "#4ADE80"
---

# Products

> Product catalogue with variants, SKUs, pricing, images, and rich descriptions — the master record for everything the storefront sells.

**Panel:** `ecommerce`
**Module key:** `ecommerce.products`

## What It Does

Products is the catalogue master for all goods and services sold through the ecommerce storefront and any connected sales channels. Each product has one or more variants (size, colour, material) each with its own SKU, barcode, price, and stock unit. Rich product content — multiple images, videos, a full HTML description, and structured attributes — is stored here and published to the storefront. Products link to the operations inventory module so stock levels are always current.

## Features

### Core
- Product record: name, description (rich text with HTML), brand, category, product type (physical, digital, service)
- Variants: define option types (size, colour) and generate all variant combinations automatically; each variant has its own SKU, barcode, weight, and price
- Pricing: base price, compare-at price (for showing a crossed-out original), and cost price (for margin calculation)
- Images: multiple images per product and per variant; drag-and-drop sort order; alt-text for SEO
- SEO meta: custom title tag, meta description, and URL slug per product
- Publication status: draft, active, archived; active products appear on the storefront

### Advanced
- Digital products: file attachment for download delivery after purchase; download link expires after configurable days
- Bulk editing: select multiple products and update price, category, or status in one action
- Product tags and collections: organise products into curated collections (Summer Sale, Best Sellers, New Arrivals)
- Attribute system: define structured attributes per product type (material, care instructions, dimensions, compatibility) for filtered browsing
- Product bundling link: products can be added to bundles in [[bundles]]
- Import/export: CSV bulk import for catalogue migration; CSV export for external feed (Google Shopping, Meta Catalogue)

### AI-Powered
- Description generator: produce SEO-optimised product descriptions from a set of bullet-point features
- Category suggestion: auto-suggest category placement based on product name and attributes

## Data Model

```erDiagram
    ec_products {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string product_type
        string status
        string brand
        string category
        json tags
        json seo_meta
        timestamps timestamps
        softDeletes deleted_at
    }

    ec_product_variants {
        ulid id PK
        ulid product_id FK
        string sku
        string barcode
        json option_values
        decimal price
        decimal compare_at_price
        decimal cost_price
        decimal weight_kg
        boolean track_inventory
        timestamps timestamps
    }

    ec_product_images {
        ulid id PK
        ulid product_id FK
        ulid variant_id FK
        string url
        string alt_text
        integer sort_order
    }

    ec_products ||--o{ ec_product_variants : "has"
    ec_products ||--o{ ec_product_images : "has"
```

| Table | Purpose |
|---|---|
| `ec_products` | Product master data and SEO |
| `ec_product_variants` | Per-variant SKU, pricing, and weight |
| `ec_product_images` | Images with sort order and alt text |

## Permissions

```
ecommerce.products.view-any
ecommerce.products.create
ecommerce.products.update
ecommerce.products.publish
ecommerce.products.delete
```

## Filament

**Resource class:** `ProductResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `BulkEditorPage` (multi-product field updates)
**Widgets:** `ProductCountByStatusWidget`
**Nav group:** Catalog

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Products | Product catalogue with variants |
| WooCommerce Products | Variable product management |
| BigCommerce Catalog | Product and variant management |
| Magento Catalog | Enterprise product catalogue |

## Related

- [[storefront]] — published products appear on the storefront
- [[inventory-sync]] — variant stock levels synced from operations inventory
- [[bundles]] — products grouped into bundles
- [[subscriptions]] — products set as recurring subscription items
- [[multi-channel]] — product listings pushed to external channels
