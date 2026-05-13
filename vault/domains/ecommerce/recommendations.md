---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.recommendations
status: planned
color: "#4ADE80"
---

# Recommendations

> AI-powered product recommendations displayed on product pages, in the cart, and in post-purchase emails to increase average order value.

**Panel:** `ecommerce`
**Module key:** `ecommerce.recommendations`

## What It Does

Recommendations surfaces relevant product suggestions to customers at the moments most likely to drive an additional purchase. The engine analyses co-purchase history, browsing patterns, and product attributes to compute "related items", "frequently bought together", and "customers who bought this also bought" recommendations. Merchants can also manually curate recommendation sets for specific products. Recommendations appear on product detail pages, the cart page, and optionally in post-purchase and abandoned cart emails.

## Features

### Core
- Recommendation types: related products (similar attributes), frequently bought together (co-purchase analysis), customers also bought (collaborative filtering), new arrivals in the same category, top sellers in the same category
- Placement configuration: enable/disable recommendation widgets on product page, cart page, checkout, and thank-you page
- Widget appearance: carousel or grid; configurable number of products shown (2–12)
- Manual overrides: curate a specific recommendation set per product; manual set takes priority over algorithmic output
- Exclusions: exclude specific products or categories from appearing as recommendations (e.g., exclude out-of-stock variants)

### Advanced
- Personalised recommendations: for logged-in customers, factor in purchase history and browsing behaviour for individualised suggestions
- Cross-sell rules: configure "if product A is in cart, always show product B as a recommendation" — e.g., always recommend the warranty when a high-value electronic is in cart
- Email integration: embed personalised recommendations in abandoned cart and post-purchase emails via [[../marketing/email-marketing]]
- A/B test recommendation algorithms: test collaborative filtering vs content-based filtering for the same placement and measure AOV lift
- Revenue attribution: track add-to-cart and purchase events from recommendation clicks to measure AOV and revenue contribution per placement

### AI-Powered
- Collaborative filtering model: compute item-item similarity from purchase co-occurrence matrix updated daily
- Content-based fallback: for new products without purchase history, use attribute similarity (category, price range, brand) for initial recommendations
- Trend-aware weighting: boost recently trending products in recommendation outputs

## Data Model

```erDiagram
    ec_recommendation_sets {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        string set_type
        boolean is_manual
        json recommended_product_ids
        timestamp last_computed_at
        timestamps timestamps
    }

    ec_recommendation_events {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        ulid recommended_product_id FK
        string set_type
        string placement
        string event_type
        ulid customer_id FK
        timestamp occurred_at
    }

    ec_recommendation_sets ||--o{ ec_recommendation_events : "tracked by"
```

| Table | Purpose |
|---|---|
| `ec_recommendation_sets` | Computed and manual recommendation lists per product |
| `ec_recommendation_events` | Click and purchase events for attribution |

## Permissions

```
ecommerce.recommendations.view-any
ecommerce.recommendations.configure-placements
ecommerce.recommendations.manage-overrides
ecommerce.recommendations.view-analytics
ecommerce.recommendations.run-compute
```

## Filament

**Resource class:** `RecommendationSetResource`
**Pages:** List, Edit
**Custom pages:** `PlacementConfigPage` (enable/disable widgets per storefront placement)
**Widgets:** `RecommendationAovWidget` (AOV uplift attributed to recommendation clicks)
**Nav group:** Marketing

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Yotpo UGC + Recommendations | Product recommendations and social proof |
| Nosto | Personalised ecommerce recommendations |
| LimeSpot | Product recommendation widgets |
| Frequently Bought Together (Shopify) | Co-purchase recommendation app |

## Implementation Notes

**AI/ML mechanism — collaborative filtering:** The spec describes a "co-purchase co-occurrence matrix updated daily." This is NOT an LLM feature — it is a PHP/SQL algorithm. The daily `ComputeRecommendationsJob` (scheduled 2am nightly) runs a SQL query against `order_items` to find product pairs that appear together in the same order, counts co-occurrences, normalises by product popularity, and writes the results to `ec_recommendation_sets` with `is_manual = false`. No external ML service is required for the collaborative filtering baseline.

**Personalised recommendations** (for logged-in customers) require querying the specific customer's `orders` to find their purchase history, then filtering the co-occurrence results to exclude already-purchased items and boost recency-weighted items. This runs as a real-time query at page render time — cache per customer with a 1-hour TTL in Redis.

**Recommendation widget rendering:** Recommendation widgets on the storefront are rendered by the Vue 3 + Inertia storefront (not by Filament). The storefront calls an API endpoint `GET /api/v1/recommendations/{productId}?type={related|frequently-bought|customers-also-bought}&limit=8` which returns a JSON array of product data. This endpoint is served from `app/Http/Controllers/Api/V1/RecommendationController.php` — it reads from `ec_recommendation_sets` (pre-computed) for performance.

**Email integration:** Personalised recommendations in abandoned cart and post-purchase emails are rendered server-side in the Mailable class — the email template calls the same `RecommendationService::getForProduct()` at send time and includes product cards as HTML in the email body.

**A/B test framework:** The spec mentions A/B testing recommendation algorithms. This requires an `ec_recommendation_ab_tests {ulid id, ulid company_id, string placement, string variant_a_algorithm, string variant_b_algorithm, decimal traffic_split, boolean is_active, json results}` table — not currently defined. Add it before building the A/B testing feature.

**Filament:** `PlacementConfigPage` is a custom `Page` — it shows a visual representation of the storefront layout with toggle switches for each widget placement position. Not a standard Resource. `RecommendationSetResource` is a standard `ListRecords` + `EditRecord` Resource for viewing and manually overriding recommendation sets.

## Related

- [[products]] — recommendations displayed on product detail pages
- [[orders]] — purchase history feeds collaborative filtering model
- [[product-reviews]] — review ratings influence recommendation ranking
- [[abandoned-carts]] — personalised recommendations in cart recovery emails
- [[analytics]] — recommendation revenue contribution tracked in analytics
