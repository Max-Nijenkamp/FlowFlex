---
type: domain-index
domain: E-commerce
panel: ecommerce
color: "#4ADE80"
---

# E-commerce

Products, variants, orders, payments, promotions, reviews, abandoned cart recovery, and storefront. **Panel:** `/ecommerce` (Teal) — Phase 3.

---

## Navigation Groups

- **Catalogue** — Products, Categories, Variants, Reviews
- **Orders** — Orders, Payments, Fulfilment
- **Marketing** — Coupons, Promotions, Abandoned Carts
- **Settings** — Storefront Configuration

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/ecommerce/products\|Product Catalogue]] | `ecommerce.products` | planned | **P3 core** |
| [[domains/ecommerce/orders\|Orders]] | `ecommerce.orders` | planned | **P3 core** |
| [[domains/ecommerce/payments\|Payments]] | `ecommerce.payments` | planned | **P3 core** |
| [[domains/ecommerce/variants\|Product Variants]] | `ecommerce.variants` | planned | P3 |
| [[domains/ecommerce/storefront\|Storefront Configuration]] | `ecommerce.storefront` | planned | P3 |
| [[domains/ecommerce/promotions\|Promotions & Coupons]] | `ecommerce.promotions` | planned | P3 |
| [[domains/ecommerce/reviews\|Product Reviews]] | `ecommerce.reviews` | planned | P3 |
| [[domains/ecommerce/abandoned-cart\|Abandoned Cart]] | `ecommerce.abandoned-cart` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-model-states` — order status, payment status
- `brick/money` — all order totals and pricing
- `stripe/stripe-php` raw SDK — payments (not Cashier)
- `spatie/laravel-sluggable` — product/category slugs
- Storefront rendered via Vue + Inertia (see [[frontend/_index]])
- Cross-domain: `CheckoutCompleted` → Finance, Operations (stock), CRM; `CartAbandoned` → recovery sequence
- Links to [[domains/operations/inventory]] for stock when active
