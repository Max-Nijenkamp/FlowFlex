---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.reviews
status: planned
color: "#4ADE80"
---

# Product Reviews

Customer product reviews with ratings, moderation, and display on the storefront.

## Core Features

- Review: product, customer, rating (1–5), title, body, verified-purchase flag
- Moderation: pending → approved | rejected (prevent spam/abuse)
- Verified purchase: only customers who bought can review (optional setting)
- Review request email after order delivery
- Average rating per product (cached)
- Helpful votes on reviews
- Merchant reply to reviews
- Display approved reviews on storefront product page

## Data Model

| Table | Key Columns |
|---|---|
| `ec_reviews` | company_id, product_id, customer_contact_id, order_id, rating, title, body, status, is_verified, helpful_count, merchant_reply |

## Filament

**Nav group:** Catalogue

- `ReviewResource` — list (moderation queue), approve/reject, reply
- Average rating shown on product

## Cross-Domain / Jobs

- Review request email triggered after order delivery (queued)

## Related

- [[domains/ecommerce/products]]
- [[domains/ecommerce/orders]]
