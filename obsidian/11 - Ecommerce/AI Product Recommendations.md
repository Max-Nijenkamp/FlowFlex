---
tags: [flowflex, domain/ecommerce, ai, recommendations, phase/5]
domain: Ecommerce
panel: ecommerce
color: "#0891B2"
status: planned
last_updated: 2026-05-08
---

# AI Product Recommendations

Personalised "you might also like" and "frequently bought together" — powered by actual purchase and browse data, not just random upsells. Increases average order value without a Shopify app subscription.

**Who uses it:** Ecommerce managers, merchandisers
**Filament Panel:** `ecommerce`; Vue components on storefront
**Depends on:** Core, [[Product Catalogue]], [[Storefront & Checkout]], [[Order Management]], [[AI Infrastructure]]
**Phase:** 5

---

## Features

### Recommendation Types

- **Frequently bought together**: products commonly purchased in same order
- **Customers also viewed**: collaborative filtering (viewed X, viewed Y)
- **Similar products**: content-based (same category, similar attributes, price range)
- **You may also like**: personalised for returning visitors based on browse/purchase history
- **Trending now**: most-purchased last 7 days, refreshed hourly
- **Recently viewed**: visitor's own browse history (local + server-side)
- **Complete the look**: visual merchandising sets (for apparel, home decor)
- **Bundle deals**: AI-suggested bundles with discount incentive

### Placement

- Product detail page (PDP): "Frequently bought together" + "Similar products" sections
- Cart page: "Don't forget" + "Others bought"
- Checkout upsell: one-click add to order before payment
- Post-purchase: "You might like" on order confirmation
- Email: personalised product block in abandoned cart emails
- Push notification: "We found something you'll like"

### Merchandising Controls

- Pin products: always show specific products in recommendation slot (e.g. new arrivals)
- Exclude products: remove out-of-stock or discontinued from recommendations
- Category affinity rules: "On /accessories, only recommend other accessories"
- Price range filtering: don't recommend if 3× price of viewed product
- Seasonal boosts: increase score for products tagged `summer-collection` in summer months

### Algorithm

- Collaborative filtering: cosine similarity on purchase/view co-occurrence matrix
- Content-based: attribute vector similarity (category, tags, price bucket)
- Hybrid: weighted blend of both, adjustable per placement
- Cold start: new products default to category trending + manual merchandising
- pgvector for embedding-based similarity (product descriptions embedded via AI)
- Recomputed nightly or on significant new order volume

### A/B Testing

- Test recommendation algorithm variant A vs B
- Test placement (e.g. cart page vs checkout page)
- Metric: click-through rate, add-to-cart rate, revenue per session
- Auto-winner deployment after statistical significance reached

### Analytics

- Click-through rate per placement
- Add-to-cart from recommendation
- Revenue attributed to recommendations
- Average order value: sessions with recommendations vs without
- Top recommended products this week

---

## Database Tables (3)

### `ecommerce_recommendation_models`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `type` | enum | `collaborative`, `content`, `hybrid`, `trending` |
| `placement` | enum | `pdp`, `cart`, `checkout`, `post_purchase`, `email` |
| `config` | json | weights, filters |
| `last_trained_at` | timestamp nullable | |
| `is_active` | boolean | |

### `ecommerce_recommendations`
| Column | Type | Notes |
|---|---|---|
| `model_id` | ulid FK | |
| `source_product_id` | ulid FK nullable | if product-based |
| `visitor_id` | string nullable | session for personalised |
| `recommended_product_ids` | json | ulid[], ranked |
| `generated_at` | timestamp | |
| `expires_at` | timestamp | |

### `ecommerce_recommendation_events`
| Column | Type | Notes |
|---|---|---|
| `model_id` | ulid FK | |
| `session_id` | string | |
| `product_id` | ulid FK | |
| `event_type` | enum | `impression`, `click`, `add_to_cart`, `purchase` |
| `occurred_at` | timestamp | |

---

## Permissions

```
ecommerce.recommendations.view
ecommerce.recommendations.configure
ecommerce.recommendations.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | Nosto | LimeSpot | Shopify Product Recs |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€/mo) | ❌ (€30+/mo) | ✅ (Shopify only) |
| Personalised per visitor | ✅ | ✅ | ✅ | partial |
| pgvector similarity | ✅ | ❌ | ❌ | ❌ |
| A/B test placements | ✅ | ✅ | ✅ | ❌ |
| Email block integration | ✅ | ✅ | partial | ❌ |
| Merchandising overrides | ✅ | ✅ | ✅ | partial |

---

## Related

- [[Ecommerce Overview]]
- [[Product Catalogue]]
- [[Storefront & Checkout]]
- [[Order Management]]
- [[Email Marketing]]
