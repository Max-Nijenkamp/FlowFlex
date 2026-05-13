---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.reviews
status: planned
color: "#4ADE80"
---

# Product Reviews

> Collect, moderate, and display customer product reviews with star ratings, review aggregation, and verified purchase badges.

**Panel:** `ecommerce`
**Module key:** `ecommerce.reviews`

## What It Does

Product Reviews enables customers to leave star-rated reviews on products they have purchased. Review requests are sent automatically after delivery confirmation. Merchants moderate incoming reviews (approve, reject, or flag for response) before they appear on the storefront. Aggregate ratings are computed per product and displayed as star ratings in the product listing and detail pages. Positive reviews build purchase confidence; review data highlights quality issues that may need addressing.

## Features

### Core
- Review request emails: automatically sent after delivery confirmation (configurable delay); includes direct link to leave a review
- Review submission: star rating (1–5) and optional text review; photo upload optional
- Verified purchase badge: reviews from confirmed purchasers are marked as verified
- Moderation queue: incoming reviews require merchant approval before display; approve, reject (with reason), or respond publicly
- Star rating aggregation: average rating and rating distribution (1–5 star count) computed per product; displayed on storefront
- Review display: published reviews shown on product detail page, sorted by most recent or most helpful

### Advanced
- Bulk moderation: filter by rating, status, or product; bulk approve low-risk reviews
- Merchant reply: add a public merchant response to any published review; displayed below the review
- Review incentive: configure an optional thank-you discount code sent after a review is submitted
- Q&A section: customers can post questions on a product; other customers or merchants answer; displayed alongside reviews
- Review widgets: embed aggregate rating (stars + count) in product listing tiles and collection pages
- Review export: export all reviews as CSV for use in external marketing or product analysis

### AI-Powered
- Sentiment analysis: classify review sentiment per product (positive themes: quality, delivery, fit; negative themes: size issues, damaged, wrong item)
- Review fraud detection: flag reviews that appear to come from the same source or contain suspicious patterns

## Data Model

```erDiagram
    ec_reviews {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        ulid order_line_id FK
        ulid customer_id FK
        integer star_rating
        string headline
        text body
        json photo_urls
        boolean verified_purchase
        string status
        ulid moderated_by FK
        timestamp submitted_at
        timestamp published_at
        timestamps timestamps
    }

    ec_review_replies {
        ulid id PK
        ulid review_id FK
        ulid author_id FK
        text body
        timestamp posted_at
    }

    ec_reviews ||--o{ ec_review_replies : "has"
```

| Table | Purpose |
|---|---|
| `ec_reviews` | Review records with rating, text, and moderation status |
| `ec_review_replies` | Merchant or community replies to reviews |

## Permissions

```
ecommerce.reviews.view-any
ecommerce.reviews.moderate
ecommerce.reviews.reply
ecommerce.reviews.export
ecommerce.reviews.delete
```

## Filament

**Resource class:** `ReviewResource`
**Pages:** List, View
**Custom pages:** `ReviewModerationPage` (moderation queue with bulk actions)
**Widgets:** `ReviewSummaryWidget` (average rating, pending moderation count)
**Nav group:** Marketing

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Yotpo Reviews | Product reviews and UGC management |
| Trustpilot (product reviews) | Verified review collection and display |
| Okendo | Shopify-native reviews and Q&A |
| Judge.me | Review collection and widget display |

## Related

- [[products]] — aggregate rating stored and displayed per product
- [[orders]] — review requests triggered after order delivery
- [[recommendations]] — review ratings feed into recommendation scoring
- [[analytics]] — review metrics (collection rate, average rating) in ecommerce analytics
