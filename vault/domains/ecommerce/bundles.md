---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.bundles
status: planned
color: "#4ADE80"
---

# Bundles

> Create fixed product bundles and mix-and-match bundles with dedicated bundle pricing to increase average order value.

**Panel:** `ecommerce`
**Module key:** `ecommerce.bundles`

## What It Does

Bundles lets merchants group products together and sell them at a combined price that is lower than purchasing each item individually. Two bundle types are supported: fixed bundles (a specific set of products, e.g., a starter kit), and mix-and-match bundles (the customer picks X items from a defined pool, e.g., any 3 candles for €30). Bundles appear as distinct purchasable products on the storefront with their own listing page, images, and description. Stock for the individual component products is decremented when a bundle is purchased.

## Features

### Core
- Fixed bundle: define a fixed set of component products and quantities; set a bundle price lower than the sum of parts
- Bundle listing: the bundle appears as its own product on the storefront with its own title, images, and description
- Component stock check: bundle can only be added to cart if all component variants have sufficient stock
- Component inventory deduction: on purchase, each component's stock is decremented by its quantity in the bundle
- Bundle price vs individual savings callout: storefront shows "Save €X vs buying separately"
- Bundle status: draft, active, archived

### Advanced
- Mix-and-match bundle: customer selects N items from a defined pool of eligible products; fixed bundle price applies once N items are selected
- Tiered mix-and-match: buy any 3 for €X, buy any 6 for €Y — multiple quantity tiers per pool
- Bundle exclusivity: configure whether component products can also be purchased individually or only as part of the bundle
- Bundle analytics: revenue from bundles, units sold, most popular bundle combinations (for mix-and-match)
- Bundle discount display: show each component item with the portion of the discount allocated to it (useful for gift bundles requiring VAT itemisation)
- Virtual bundles: bundle products that ship separately (e.g., a hardware device + a SaaS subscription); no inventory aggregation

### AI-Powered
- Bundle suggestion: identify product pairs that are frequently purchased together and suggest as a bundle candidate
- Bundle price optimisation: recommend a bundle price that maximises conversion rate based on individual product prices and historical sales data

## Data Model

```erDiagram
    ec_bundles {
        ulid id PK
        ulid company_id FK
        string name
        string bundle_type
        text description
        decimal bundle_price
        string status
        json seo_meta
        timestamps timestamps
        softDeletes deleted_at
    }

    ec_bundle_components {
        ulid id PK
        ulid bundle_id FK
        ulid product_variant_id FK
        integer quantity
        boolean is_pool_item
        integer min_selection
        integer max_selection
    }

    ec_bundles ||--o{ ec_bundle_components : "contains"
```

| Table | Purpose |
|---|---|
| `ec_bundles` | Bundle configuration and pricing |
| `ec_bundle_components` | Component products with quantity and pool configuration |

## Permissions

```
ecommerce.bundles.view-any
ecommerce.bundles.create
ecommerce.bundles.update
ecommerce.bundles.publish
ecommerce.bundles.delete
```

## Filament

**Resource class:** `BundleResource`
**Pages:** List, Create, Edit, View
**Custom pages:** none
**Widgets:** `BundlePerformanceWidget` (revenue and units per active bundle)
**Nav group:** Catalog

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Bundles app | Native product bundling |
| Bold Bundles | Fixed and mix-and-match bundle creation |
| Bundler app (Shopify) | Product bundle pricing |
| WooCommerce Product Bundles | Bundle configuration for WooCommerce |

## Related

- [[products]] — component products linked to bundles
- [[inventory-sync]] — component stock decremented on bundle purchase
- [[promotions]] — bundle pricing is distinct from discount promotions
- [[analytics]] — bundle revenue tracked separately in ecommerce analytics
