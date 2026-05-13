---
type: module
domain: Product-Led Growth
panel: plg
module-key: plg.usage
status: planned
color: "#4ADE80"
---

# Usage Analytics

> Read-only product usage analytics — feature adoption, session data, user journey maps, and retention metrics.

**Panel:** `plg`
**Module key:** `plg.usage`

---

## What It Does

Usage Analytics gives product teams a detailed view of how customers are actually using FlowFlex. It tracks which features, panels, and specific actions are most and least used, measures session frequency and depth, and maps common user journeys through the product. Retention metrics show how many users return day after day, week after week. All data is segmented by company, role, plan type, and cohort so teams can understand usage patterns specific to their target customer profile.

---

## Features

### Core
- Feature adoption tracking: percentage of active companies and users using each major feature or panel
- Page and action event tracking: record every panel visit, record creation, and key action
- Session metrics: session count, session duration, and sessions per user per week
- Daily/Weekly/Monthly Active Users (DAU/WAU/MAU): trend lines with period-over-period comparison
- User journey paths: most common sequences of panel visits within a session
- Retention curve: day-1, day-7, day-14, day-30 retention rates per sign-up cohort

### Advanced
- Feature correlation analysis: identify which features are most correlated with long-term retention
- Company-level usage: view the feature adoption profile of a specific company
- Power user identification: identify the top 10% most engaged users by action volume
- Churn leading indicators: track feature disengagement patterns that precede account cancellation
- Custom events: define additional product events to track beyond the default set

### AI-Powered
- Usage summary generation: AI generates a weekly product usage briefing for the PLG team
- Engagement scoring: composite engagement score per user and company updated daily
- Feature retirement candidates: flag features with consistently low adoption and engagement for review

---

## Data Model

```erDiagram
    product_events {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string event_name
        string panel
        string entity_type
        json properties
        timestamp occurred_at
    }

    usage_sessions {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string session_id
        timestamp started_at
        timestamp ended_at
        integer event_count
    }

    usage_daily_snapshots {
        ulid id PK
        ulid company_id FK
        date snapshot_date
        integer dau
        integer wau
        integer mau
        integer avg_session_minutes
        json top_features
    }

    product_events }o--|| usage_sessions : "belongs to"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `product_events` | Raw event stream | `id`, `company_id`, `user_id`, `event_name`, `panel`, `occurred_at` |
| `usage_sessions` | Session records | `id`, `company_id`, `user_id`, `session_id`, `started_at`, `ended_at` |
| `usage_daily_snapshots` | Aggregated metrics | `id`, `company_id`, `snapshot_date`, `dau`, `wau`, `mau`, `top_features` |

---

## Permissions

```
plg.usage.view
plg.usage.view-company-detail
plg.usage.view-retention
plg.usage.export
plg.usage.view-user-journeys
```

---

## Filament

- **Resource:** None (read-only, no CRUD)
- **Pages:** N/A
- **Custom pages:** `UsageDashboardPage`, `FeatureAdoptionPage`, `RetentionPage`, `UserJourneyPage`
- **Widgets:** `ActiveUsersWidget`, `TopFeaturesWidget`, `RetentionCurveWidget`
- **Nav group:** Analytics

---

## Displaces

| Feature | FlowFlex | Pendo | Amplitude | Mixpanel |
|---|---|---|---|---|
| Feature adoption tracking | Yes | Yes | Yes | Yes |
| Retention curves | Yes | Yes | Yes | Yes |
| User journey mapping | Yes | Yes | Yes | Yes |
| Native platform data | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[activation-metrics]] — activation events are a subset of product events
- [[feature-flags]] — usage data informs flag rollout decisions
- [[trial-management]] — trial company usage informs conversion scoring
- [[ai/predictive-analytics]] — usage signals feed churn and attrition models
