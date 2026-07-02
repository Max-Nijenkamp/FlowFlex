---
domain: ecommerce
module: reviews
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Reviews — Architecture

## Moderation Lifecycle

```
pending → approved   (visible on storefront)
        → rejected   (hidden)
```

Plain enum on `ec_reviews.status` (no state-machine class *(assumed)*). Approve/reject busts the product rating cache.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `ReviewService::submit(...)` | service | verified-purchase check, dedupe per `(order_id, product_id)`, purify body |
| `ModerateReviewAction` | action | approve/reject; busts `ProductRating` cache |
| `ProductRating::average(productId): float` | support | cached average of approved reviews |
| `ReviewRequestCommand` | command | fulfilment +7d, once per order, sends `ReviewRequestMail` |

## Events

None fired/consumed. See [[../../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy | Notes |
|---|---|---|---|
| `ReviewResource` | Catalogue | simple-resource | moderation-queue tab, approve/reject, reply |

Storefront review display + submission is Vue + Inertia ([[../../storefront/_module|storefront]]).

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('ecommerce.reviews.view-any')
        && BillingService::hasModule('ecommerce.reviews');
}
```

## Jobs & Scheduling

| Command | Queue | Schedule |
|---|---|---|
| `ReviewRequestCommand` | notifications | daily; sends at fulfilment +7d, once per order |

## Search & Realtime

None. Average rating cached in Redis, busted on moderation.
