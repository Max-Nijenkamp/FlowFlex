---
domain: ecommerce
module: storefront
feature: browse-and-cart
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Browse & Cart

Public catalog browse/search, product page (variants + reviews), and a session cart — the shopper's path to checkout.

## Behaviour

- Browse/search only `status = active` products of the company (Meilisearch, `status = active` + company filter).
- Product page shows variants (selectable, out-of-stock disabled) and approved reviews + average rating.
- Add-to-cart builds a session `CartData`; cart view re-validates each line against live stock/price.

## UI

- **Kind**: public-vue
- **Page**: `Shop/Index.vue` (`/shop/{slug}`), `Shop/Product.vue` (`/shop/{slug}/p/{product-slug}`), `Shop/Cart.vue` (`/shop/{slug}/cart`).
- **Layout**: index = filterable product grid + search; product = gallery, variant selector, price, reviews; cart = line list with qty steppers + totals.
- **Key interactions**: search/filter; select variant; add to cart (optimistic) → server re-validates; adjust qty; proceed to checkout.
- **States**: empty (no products → "store coming soon"; empty cart → "your cart is empty") · loading (grid/skeleton) · error (out-of-stock variant disabled; stale price corrected with a note) · selected (variant chosen, cart line highlighted).
- **Gating**: public/guest guard, company-scoped by slug.

## Data

- Owns / writes: session cart only (no DB writes here; abandoned-cart module captures `ec_carts` at checkout start).
- Reads: active `ec_products`/`ec_variants` (products/variants), approved `ec_reviews` + rating (reviews).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: catalog + reviews (read APIs of products/variants/reviews).
- Feeds: cart into [[checkout]].
- Shared entity: `ec_products`, `ec_variants`, `ec_reviews`.

## Unknowns

- Multi-language product content (see [[../unknowns]]).

## Related

- [[../_module|Storefront]] · [[checkout]] · [[configure-storefront]]
