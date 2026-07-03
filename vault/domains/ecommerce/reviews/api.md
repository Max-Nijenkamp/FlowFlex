---
domain: ecommerce
module: reviews
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Reviews — API / DTOs

## `SubmitReviewData` (public)

| Field | Type | Rules |
|---|---|---|
| `token` | uuid | present when submitting via request-mail link |
| `product_id` + `email` | ulid + string | when submitting openly (verified-purchase off) |
| `rating` | int | required, 1–5 |
| `title` | string | max:150 |
| `body` | text | max:3000, purified |

Rate-limited. Dedupe per `(order_id, product_id)`.

## `ReviewService`

- `submit(SubmitReviewData)` — verified check, dedupe, purify.
- `ProductRating::average(productId): float` — cached.

## `ModerateReviewAction`

- approve / reject → busts rating cache.

## Public / Portal Endpoints

| Route | Guard | Notes |
|---|---|---|
| `POST /reviews/submit` | public/guest + signed URL | validates `review_token`; `throttle:public` |
| `POST /reviews/{review}/helpful` | public/guest | rate-limited helpful vote |

Public submission/helpful-vote routes run on the **public/guest guard** with signed-URL validation of `review_token`, distinct from the authenticated Filament panel guard ([[../../../../_archive/build-history/security-audit-2026-06-11]] — HIGH).
