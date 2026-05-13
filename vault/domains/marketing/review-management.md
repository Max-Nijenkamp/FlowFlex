---
type: module
domain: Marketing
panel: marketing
module-key: marketing.reviews
status: planned
color: "#4ADE80"
---

# Review Management

> Aggregate reviews from G2, Trustpilot, Google, and Capterra into one dashboard, respond without leaving FlowFlex, run automated review request campaigns, and embed a live ratings badge on your website.

**Panel:** `/marketing`
**Module key:** `marketing.reviews`

## What It Does

Review Management consolidates a company's online reputation into one place. Instead of checking G2, Trustpilot, Google Business Profile, and Capterra in separate tabs, marketing teams see all reviews in a unified feed, respond directly through FlowFlex, and get alerted immediately when a negative review is posted. Review request campaigns automatically email customers after a deal closes or a support ticket is resolved — sending the ask at the moment of highest satisfaction. A weighted reputation score across all platforms is tracked over time, and an embeddable star rating badge keeps marketing sites always showing the latest scores.

## Features

### Core
- Multi-platform aggregation: connect G2, Trustpilot, Google Business Profile, and Capterra via platform APIs — reviews are synced on a configurable schedule (hourly for Trustpilot and Google, daily for G2 and Capterra due to API rate limits) and stored in `marketing_reviews`
- Unified review feed: all reviews from all connected platforms shown in one chronological list — filterable by platform, rating, sentiment, date range, and response status
- Respond to reviews: post responses to Trustpilot and Google reviews directly from the Filament UI without opening the platform — response is published via the platform API; G2 and Capterra responses are opened in the platform (deep link) as their APIs are read-only
- Reputation score dashboard: a weighted average star rating across all connected platforms — weighting per platform is configurable (default: all equal weight); displayed as a large number with trend arrows and a per-platform breakdown
- New negative review alert: when a review with rating ≤ 2 is synced, a notification is sent immediately to all users with `marketing.reviews.view-reviews` permission — with the review text, platform, and a one-click "Respond" link

### Advanced
- Review request campaigns: define triggered campaigns that send an automated email to a contact after a specified event: `deal_closed_won` (CRM), `support_ticket_resolved` (Support domain), or `manual_send`; the email includes a direct link to the preferred review platform; delay is configurable (default: 2 days after trigger event)
- Campaign A/B testing: test two subject lines or email body variants for review request emails — uses the Email Marketing module's A/B testing capability; winner selected after 50 responses
- Sentiment analysis: all review text is processed by AI on sync — classified as positive / neutral / negative and keyed themes are extracted; sentiment is shown as a badge on each review in the feed
- Review response templates: pre-saved response templates per platform (e.g. "Thank you for your positive review template" / "Apology for negative experience template") — populated with merge tags (reviewer name, company name, response time) for fast personalised responses
- Review widgets: generate embeddable HTML widgets showing the current aggregate rating — three widget styles: star rating badge, review count badge, and a scrolling review carousel (last 5 positive reviews); widgets fetch live data from a public FlowFlex API endpoint so they update automatically without re-embedding

### AI-Powered
- Response drafting: for any review, click "Draft Response" and AI generates a contextually appropriate response using GPT-4o — taking the review text, rating, and platform conventions into account; the draft is editable before posting
- Theme trend detection: AI identifies recurring themes across reviews (e.g. "customer support response time", "ease of onboarding", "pricing") and tracks their frequency month-over-month — surfaced as a "Review Themes" section on the reputation dashboard; rising negative themes trigger a marketing team alert
- Competitor sentiment comparison: (V2 feature) periodically pull public competitor reviews from G2 and Trustpilot (where data is available) and run theme comparison — shows which themes your competitors are winning and losing on vs your own reviews

## Data Model

```erDiagram
    marketing_review_platforms {
        ulid id PK
        ulid company_id FK
        enum platform
        json credentials_encrypted
        timestamp last_synced_at
        boolean is_active
        timestamps created_at/updated_at
    }

    marketing_reviews {
        ulid id PK
        ulid company_id FK
        ulid platform_id FK
        enum platform
        string external_id
        string author_name
        tinyint rating
        string title "nullable"
        text body
        timestamp published_at
        timestamp responded_at "nullable"
        text response_body "nullable"
        enum sentiment
        json ai_themes "nullable"
        timestamps created_at/updated_at
    }

    marketing_review_campaigns {
        ulid id PK
        ulid company_id FK
        string name
        enum trigger_type
        ulid email_template_id FK
        integer delay_days
        enum preferred_platform
        boolean is_active
        timestamps created_at/updated_at
    }

    marketing_review_campaign_sends {
        ulid id PK
        ulid campaign_id FK
        ulid contact_id FK
        ulid company_id FK
        enum trigger_event
        timestamp triggered_at
        timestamp sent_at "nullable"
        timestamp responded_at "nullable"
        ulid resulting_review_id FK "nullable"
        timestamps created_at/updated_at
    }

    marketing_review_response_templates {
        ulid id PK
        ulid company_id FK
        string name
        enum platform
        enum rating_band
        text body
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `marketing_review_platforms.platform` | enum: `g2` / `trustpilot` / `google_business` / `capterra` / `clutch` |
| `marketing_review_platforms.credentials_encrypted` | AES-256 encrypted JSON — stores OAuth tokens, API keys as appropriate per platform |
| `marketing_reviews.rating` | 1–5 tinyint (all platforms normalised to 5-star scale) |
| `marketing_reviews.sentiment` | enum: `positive` / `neutral` / `negative` — populated by AI sync job |
| `marketing_reviews.external_id` | Platform's own review ID — unique per platform; used to prevent duplicate imports |
| `marketing_review_campaigns.trigger_type` | enum: `deal_closed_won` / `ticket_resolved` / `manual` |
| `marketing_review_response_templates.rating_band` | enum: `positive` (4–5 stars) / `neutral` (3 stars) / `negative` (1–2 stars) |

## Permissions

```
marketing.reviews.view-reviews
marketing.reviews.respond-to-reviews
marketing.reviews.manage-campaigns
marketing.reviews.manage-platforms
marketing.reviews.view-analytics
```

## Filament

- **Resource:** `ReviewResource` — the primary working view; filterable list of all reviews across all platforms; columns: platform icon, author name, star rating (star icons), sentiment badge, date, response status (responded / awaiting / N/A); row actions: "Respond" (opens an inline response form for supported platforms, or a deep link for others), "Draft with AI" (populates response field with AI draft), mark as "Reviewed" (internal only — does not post anything)
- **Resource:** `ReviewCampaignResource` — CRUD for review request campaigns; shows each campaign with trigger event, delay, connected email template, active status, and stats (sent count, response rate, average rating received from campaign-triggered reviews)
- **Custom page:** `ReputationDashboardPage` — the entry point for the module; sections: reputation score card (large number + trend), per-platform score breakdown (horizontal bar charts), aggregate rating over time (line chart — 12 months), review volume by rating (stacked bar — 12 months), AI theme word cloud (coloured by sentiment), recent review feed (last 5 reviews across all platforms), pending responses table (reviews with no response older than 48 hours)
- **Nav group:** Analytics (marketing panel) — positioned alongside marketing analytics and SEO tools
- **Widget:** `ReputationScoreWidget` on marketing dashboard — shows the current weighted reputation score with trend arrow and the count of unanswered negative reviews (highlighted in red if >0)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Birdeye | Review aggregation, response management, reputation score |
| Podium | Review requests, messaging, and reputation monitoring |
| Yotpo | Review campaigns and widget embedding |
| Grade.us | Review management and multi-platform aggregation |
| Reputation.com | Reputation score and review response workflow |
| NiceReply | Post-support CSAT and review request campaigns |

## Related

- [[campaigns]]
- [[email-marketing]]
- [[analytics]]
- [[../crm/contacts]]
- [[../crm/deals]]
- [[../support/INDEX]]

## Implementation Notes

### Platform API Access
Each platform has different API access requirements and capabilities:

**Trustpilot:** Business API requires a Trustpilot Business account. REST API provides read access to reviews and write access for responses via the `POST /v1/private/business-units/{id}/reviews/{reviewId}/reply` endpoint. Webhook available for real-time new review notifications (preferred over polling). API key stored encrypted in `credentials_encrypted`.

**Google Business Profile:** OAuth2 via Google Identity Services. Requires the `https://www.googleapis.com/auth/business.manage` scope. Review retrieval via the `My Business API v4.9` (`accounts.locations.reviews.list`). Review replies via `accounts.locations.reviews.updateReply`. Note: Google deprecated some Business Profile API endpoints in 2022 — verify current endpoint availability before build.

**G2:** API is partner-programme restricted — requires a formal G2 partner agreement. API provides read access to reviews for your own product listing. No write access for responses (responses must be posted through G2 UI). If G2 partner access is not available at build time, implement a manual import CSV flow as fallback.

**Capterra:** Capterra has no public API. Reviews can be exported as CSV from the vendor portal. Implement a manual CSV import (same UX as the Salary Benchmarking import — column mapping step) as the V1 approach. Track the Capterra API availability for future integration.

**Clutch:** Similar to Capterra — limited API. V1 manual import.

### Sync Architecture
Use a `SyncReviewsJob` per platform, dispatched by a scheduler: hourly for Trustpilot (webhook preferred + polling fallback) and Google Business, daily for G2 and Capterra. Each sync job:
1. Fetches reviews since `last_synced_at` from the platform API
2. Upserts `marketing_reviews` records using `external_id` as the unique key
3. Dispatches a `ClassifyReviewSentimentJob` for any reviews without `sentiment` set
4. Updates `marketing_review_platforms.last_synced_at`

Sentiment classification is a separate job (not inline in sync) to avoid blocking the sync if the OpenAI API is slow.

### Review Request Campaign Trigger
The `deal_closed_won` trigger listens to the `DealStatusChanged` event. The `ticket_resolved` trigger listens to the `SupportTicketStatusChanged` event. Both listeners check if there is an active campaign for the company with the matching trigger type, verify the contact has not already received a review request in the past 90 days (prevents repeated asks), and dispatch a `SendReviewRequestJob` after the configured `delay_days` using Laravel's delayed job dispatch.

The review request email contains a unique trackable link (`/review-request/{campaign_send_ulid}`). When the contact clicks through to the review platform, `marketing_review_campaign_sends.responded_at` is set. If the contact subsequently leaves a review that is synced back to FlowFlex, `resulting_review_id` is populated by the sync job that matches the contact email to the reviewer on the review platform (where reviewer email is available — Trustpilot only).
