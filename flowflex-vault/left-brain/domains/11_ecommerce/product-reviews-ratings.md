---
type: module
domain: E-commerce & Sales Channels
panel: ecommerce
cssclasses: domain-ecommerce
phase: 5
status: planned
migration_range: 600000–649999
last_updated: 2026-05-09
---

# Product Reviews & Ratings

Customer reviews, star ratings, Q&A, photo/video UGC, verified purchase badges, and review moderation. Replaces Yotpo and Stamped.io.

---

## Features

### Review Collection
- Post-purchase review request email (auto-sent N days after delivery)
- SMS review request
- In-store QR code → review page
- Star rating (1–5) + written review
- Photo and video upload
- Verified purchase badge (only buyers can review)

### Q&A
- Customer questions on product page
- Community answers + brand answers
- Upvote answers
- Auto-answer from FAQ (AI-powered)

### Moderation
- Review moderation queue (approve/reject/flag)
- AI auto-moderation (spam, profanity, off-topic)
- Brand response to reviews (public reply)
- Flag inappropriate reviews for platform review

### Display
- Star rating summary widget on storefront product page
- Review listing with filter (5★, 4★, most recent, most helpful)
- Review highlights (AI-extracted themes from review text)
- Review photos gallery
- Rich snippets (JSON-LD) for Google Star Ratings in search results

### Analytics
- Average rating per product
- Review volume trend
- Sentiment breakdown (positive/neutral/negative)
- Top complaint themes (AI NLP analysis)
- Response rate for brand replies

---

## Data Model

```erDiagram
    product_reviews {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        ulid order_id FK "nullable — verified purchase"
        string reviewer_name
        string reviewer_email
        integer rating
        string title
        text body
        string status
        boolean verified_purchase
        integer helpful_votes
        timestamp published_at
    }

    review_media {
        ulid id PK
        ulid review_id FK
        string type
        string url
    }

    product_qa {
        ulid id PK
        ulid product_id FK
        string question
        string asked_by_email
        string answer
        boolean is_brand_answer
        timestamp answered_at
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `ReviewPublished` | Review approved | Analytics (update average rating), Marketing (UGC pool) |
| `NegativeReviewPublished` | ≤2 stars published | Notifications (customer success team) |
| `ReviewRequestSent` | Post-purchase email sent | Analytics (track request rate) |

---

## Permissions

```
ecommerce.reviews.view-any
ecommerce.reviews.moderate
ecommerce.reviews.respond
ecommerce.qa.answer
```

---

## Competitors Displaced

Yotpo · Stamped.io · Okendo · Judge.me · Trustpilot (product level)

---

## Related

- [[MOC_Ecommerce]]
- [[entity-product]]
- [[MOC_Marketing]] — UGC feeds into Influencer & UGC module
