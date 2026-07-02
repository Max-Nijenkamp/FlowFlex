---
domain: ecommerce
module: reviews
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Product Reviews

Customer product reviews with ratings, moderation, verified-purchase, and storefront display.

## Module-key

| Field | Value |
|---|---|
| key | `ecommerce.reviews` |
| priority | p3 |
| panel | ecommerce |
| permission-prefix | `ecommerce.reviews` |
| tables | `ec_reviews` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../products/_module\|Products]] · [[../orders/_module\|Orders]] | reviews per product; verified-purchase + request mails need orders |
| Hard | [[../../core/billing/_module\|Billing]] · [[../../core/rbac/_module\|RBAC]] | gating + permissions |

## Core Features

- **Review** — product, customer, rating (1–5), title, body, verified-purchase flag.
- **Moderation** — `pending → approved | rejected` (simple enum).
- **Verified purchase** — only buyers can review (company setting; default on *(assumed)*).
- **Review-request email** — +7d after fulfilment *(assumed)*, once per order.
- **Average rating** per product (cached, busted on approve/reject).
- **Helpful votes** (public, rate-limited) + **merchant reply**.
- **Storefront display** of approved reviews; public submission via signed link.

## See features/

- [[features/submit-review|Submit Review]] — public signed-link submission + verified check.
- [[features/moderate-review|Moderate Review]] — approve/reject queue + reply.

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Verified-purchase setting blocks non-buyers; token submit works.
- [ ] One review per (order, product).
- [ ] Pending invisible on storefront; approve publishes + busts rating cache.
- [ ] Request mail once per order at +7d.
- [ ] Bodies purified; public endpoints rate-limited.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | fulfilled orders | ecommerce.orders | Verified check + request-mail trigger; reviews never writes `ec_orders` |
| Feeds | approved reviews + avg rating | ecommerce.storefront | Storefront display (read) |

**Data ownership:** `ecommerce.reviews` writes only `ec_reviews`. It reads orders to verify purchases; it never writes order tables ([[../../../../security/data-ownership]]).

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../products/_module|Products]] · [[../orders/_module|Orders]] · [[../storefront/_module|Storefront]]
- [[../../../glossary]]
