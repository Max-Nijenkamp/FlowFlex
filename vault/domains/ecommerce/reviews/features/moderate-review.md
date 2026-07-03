---
domain: ecommerce
module: reviews
feature: moderate-review
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Moderate Review

Merchant approves or rejects pending reviews and can post a public reply; approval publishes to the storefront and busts the rating cache.

## Behaviour

- `ModerateReviewAction` transitions `pending → approved | rejected`.
- Approving makes the review visible on the storefront and busts `ProductRating` cache (recomputes average).
- Rejecting hides it.
- Merchant may add a `merchant_reply` (purified) shown under the review.

## UI

- **Kind**: simple-resource (moderation queue)
- **Page**: `ReviewResource` (`/ecommerce/reviews`), nav group **Catalogue**, with a "Pending" queue tab.
- **Layout**: table of reviews (product, rating, snippet, status, verified badge) filtered by status; row actions Approve / Reject / Reply.
- **Key interactions**: approve/reject row action (busts cache); reply opens a modal; bulk approve/reject.
- **States**: empty (queue clear → "no reviews to moderate") · loading (table skeleton) · error (toast) · selected (review row → detail + reply).
- **Gating**: view `ecommerce.reviews.view-any`; approve/reject `ecommerce.reviews.moderate`; reply `ecommerce.reviews.reply`.

## Data

- Owns / writes: `ec_reviews` (`status`, `merchant_reply`) only.
- Reads: `ec_products` (for display context).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: approved reviews + refreshed average to [[../../storefront/_module|storefront]].
- Shared entity: `ec_products`.

## Test Checklist

### Unit
- [ ] Transition matrix: pending->approved / pending->rejected only; merchant_reply purified

### Feature (Pest)
- [ ] Approve makes review storefront-visible and busts the `ProductRating` cache (average recomputed)
- [ ] Reject hides it; raced approve+reject resolves to one final status (locked transition)
- [ ] Tenant isolation + permission: moderation verbs enforced per company

### Livewire
- [ ] Moderation-queue tab renders pending reviews; approve/reject actions gated; hidden without `ecommerce.reviews.view-any`

## Unknowns

- Auto-approve trusted verified reviews (see [[../unknowns]]).

## Related

- [[../_module|Product Reviews]] · [[submit-review]]
