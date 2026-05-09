---
type: module
domain: Marketing & Content
panel: marketing
cssclasses: domain-marketing
phase: 5
status: planned
migration_range: 400000–449999
last_updated: 2026-05-09
---

# Review & Reputation Management

Monitor, request, and respond to reviews across Google, Trustpilot, G2, Capterra, and other platforms. Protect and grow brand reputation. Replaces Birdeye and Podium.

---

## Features

### Review Monitoring
- Connect Google Business Profile, Trustpilot, G2, Capterra, Facebook, TripAdvisor, Yelp
- Unified review inbox (all platforms in one feed)
- Real-time new review alerts
- Sentiment analysis (positive/neutral/negative) per review
- Rating trend over time per platform
- Competitor comparison (monitor competitor ratings on same platforms)

### Review Requests
- Post-interaction review request (email + SMS)
- Gating: check satisfaction first, only send to happy customers (NPS ≥ 8)
- Auto-route happy → review platform, unhappy → internal feedback form
- QR code cards for in-person review requests
- Follow-up sequence (1 request + 1 reminder, then stop)

### Response Management
- Respond to reviews without leaving FlowFlex
- AI-suggested response based on review content and brand voice
- Response templates per star rating
- Escalation: negative reviews auto-assigned to CS manager
- Response time tracking (SLA on negative reviews)

### Brand Score Dashboard
- Overall brand score (weighted average across platforms)
- Platform breakdown
- Volume and trend
- Key complaint themes (NLP extraction)
- Location-level scores (for multi-location businesses)

---

## Data Model

```erDiagram
    review_sources {
        ulid id PK
        ulid company_id FK
        string platform
        string external_id
        string access_token
        boolean is_connected
    }

    external_reviews {
        ulid id PK
        ulid source_id FK
        string external_review_id
        integer rating
        text body
        string author_name
        string status
        string sentiment
        timestamp review_date
        timestamp responded_at
        text response_text
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `NegativeReviewReceived` | ≤2 star review arrives | Notifications (CS manager alert) |
| `ReviewResponseSent` | Response published | Analytics (track response rate) |

---

## Permissions

```
marketing.reputation.view-any
marketing.reputation.respond
marketing.reputation.manage-sources
```

---

## Competitors Displaced

Birdeye · Podium · Reputation.com · Yext Reviews · ReviewTrackers

---

## Related

- [[MOC_Marketing]]
- [[MOC_CRM]] — negative reviews can trigger support tickets
