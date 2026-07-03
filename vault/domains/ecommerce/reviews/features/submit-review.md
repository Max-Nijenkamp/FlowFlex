---
domain: ecommerce
module: reviews
feature: submit-review
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Submit Review

A customer submits a product review via a signed request-mail link (or openly when verified-purchase is off).

## Behaviour

1. Request-mail link carries a signed URL validating `review_token`; or, when verified-purchase is off, an open form takes `product_id` + `email`.
2. `ReviewService::submit` runs the verified-purchase check, dedupes per `(order_id, product_id)`, purifies title/body.
3. New review starts `pending` (invisible on storefront until approved).
4. Public submission + helpful votes are rate-limited on the guest guard.

## UI

- **Kind**: public-vue
- **Page**: review form at `/shop/{company-slug}/review/{token}` and inline on the product page (Vue + Inertia, [[../../storefront/_module|storefront]]).
- **Layout**: star-rating input, title, body, submit; helpful-vote buttons on displayed reviews.
- **Key interactions**: submit → server verifies token + purchase → "thanks, pending moderation"; helpful vote increments `helpful_count` (rate-limited).
- **States**: empty (no reviews yet on product) · loading (submitting) · error (invalid/expired token, duplicate review, rate-limited) · selected (form focused).
- **Gating**: public/guest guard + signed-URL `review_token`; `throttle:public`.

## Data

- Owns / writes: `ec_reviews` only.
- Reads: `ec_orders` (verified-purchase check), company review setting.
- Cross-domain writes: none — reviews reads orders but never writes `ec_orders` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: fulfilled order (verified check) from [[../../orders/_module|orders]].
- Feeds: approved reviews + average rating displayed by [[../../storefront/_module|storefront]].
- Shared entity: `ec_orders`, `ec_products`, `crm_contacts` (soft).

## Test Checklist

### Unit
- [ ] Verified-purchase check honoured when on; dedupe key `(order_id, product_id)`
- [ ] Title/body purified before storage

### Feature (Pest)
- [ ] Double-submit for the same order+product yields one review (unique-key race safe)
- [ ] New review starts `pending`, invisible on storefront until approved
- [ ] Public submission + helpful votes rate-limited on the guest guard (over-limit 429)

### Livewire
- (none -- public Vue surface; admin side covered in moderate-review)

## Unknowns

- Helpful-vote abuse controls; media uploads (see [[../unknowns]]).

## Related

- [[../_module|Product Reviews]] · [[moderate-review]]
