---
type: module
domain: E-commerce
domain-key: ecommerce
panel: ecommerce
module-key: ecommerce.reviews
status: planned
priority: p3
depends-on: [ecommerce.products, ecommerce.orders, core.billing, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: []
tables: [ec_reviews]
permission-prefix: ecommerce.reviews
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Product Reviews

Customer product reviews with ratings, moderation, and display on the storefront.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/ecommerce/products\|ecommerce.products]] + [[domains/ecommerce/orders\|ecommerce.orders]] | reviews per product; verified-purchase + request mails need orders |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |

---

## Core Features

- Review: product, customer, rating (1–5), title, body, verified-purchase flag
- Moderation: `pending → approved | rejected` (simple enum)
- Verified purchase: only customers who bought can review (company setting; default on *(assumed)*)
- Review request email after order fulfilment (+7d *(assumed)*, once per order)
- Average rating per product (cached, busted on approve/reject)
- Helpful votes on reviews (public, rate-limited)
- Merchant reply to reviews
- Display approved reviews on storefront product page
- Public submission via signed link from request mail (or storefront when verified-purchase off)

---

## Data Model

### ec_reviews

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), product_id FK | ulid | |
| customer_contact_id | ulid nullable | |
| customer_email / customer_name | string | |
| order_id | ulid nullable | verified link; unique `(order_id, product_id)` |
| rating | int 1–5 | |
| title / body | string / text | purified |
| status | string default `pending` | pending/approved/rejected |
| is_verified | boolean | order-linked |
| helpful_count | int default 0 | |
| merchant_reply | text nullable | purified |
| review_token | uuid unique | request-mail link |
| deleted_at | timestamp nullable | |

---

## DTOs

### SubmitReviewData (public) — token or (product + email when open), rating (1–5), title (max:150), body (max:3000) — rate-limited

## Services & Actions

- `ReviewService::submit(...)` — verified check, dedupe per (order, product)
- `ModerateReviewAction` (approve/reject) — busts product rating cache
- `ReviewRequestCommand` — fulfilment+7d, once per order
- `ProductRating::average(productId)` — cached

---

## Filament

**Nav group:** Catalogue

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ReviewResource` | #1 CRUD resource | moderation queue tab, approve/reject, reply |

Storefront review display + submit: Vue + Inertia (storefront module).

---

## Permissions

`ecommerce.reviews.view-any` · `ecommerce.reviews.moderate` · `ecommerce.reviews.reply`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Verified-purchase setting blocks non-buyers; token submit works
- [ ] One review per (order, product)
- [ ] Pending invisible on storefront; approve publishes + busts rating cache
- [ ] Request mail once per order at +7d
- [ ] Bodies purified; public endpoints rate-limited

---

## Build Manifest

```
database/migrations/xxxx_create_ec_reviews_table.php
app/Models/Ecommerce/Review.php
app/Data/Ecommerce/SubmitReviewData.php
app/Services/Ecommerce/ReviewService.php
app/Actions/Ecommerce/ModerateReviewAction.php
app/Support/Ecommerce/ProductRating.php
app/Console/Commands/Ecommerce/ReviewRequestCommand.php
app/Mail/Ecommerce/ReviewRequestMail.php
app/Filament/Ecommerce/Resources/ReviewResource.php
database/factories/Ecommerce/ReviewFactory.php
tests/Feature/Ecommerce/ReviewTest.php
```

---

## Related

- [[domains/ecommerce/products]]
- [[domains/ecommerce/orders]]
