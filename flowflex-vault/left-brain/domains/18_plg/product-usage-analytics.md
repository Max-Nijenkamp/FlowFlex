---
type: module
domain: Product-Led Growth
panel: plg
cssclasses: domain-plg
phase: 7
status: complete
migration_range: 895000–896999
last_updated: 2026-05-12
---

# Product Usage Analytics

Track events, analyse funnels, build retention curves, and measure feature adoption. Replaces Amplitude and Mixpanel for SaaS products built on FlowFlex.

---

## Event Tracking

### Event Ingestion
Events sent from the customer's product via:
- **JavaScript SDK**: `FlowFlex.track('button_clicked', { button_id: 'checkout_cta', page: '/pricing' })`
- **Server-side REST API**: `POST /api/plg/events`
- **Reverse proxy mode**: intercept existing analytics events (if migrating from Segment)

Event schema:
```json
{
  "event": "checkout_started",
  "user_id": "usr_abc123",
  "timestamp": "2026-05-09T14:23:00Z",
  "properties": {
    "plan": "pro",
    "cart_value": 149.00,
    "currency": "EUR"
  }
}
```

### Event Storage
Events stored in a columnar table optimised for aggregate queries:
- Partitioned by tenant_id + date
- No PII in event names or properties (guidance enforced by SDK lint check)
- Retention: 24 months default, configurable

---

## Funnels

Define a funnel as an ordered list of events:
```
1. signup_completed
2. profile_completed
3. integration_connected
4. first_export_run
```

Funnel report shows:
- Users entering step 1 (within date range)
- Conversion at each step (absolute count + %)
- Drop-off count + % per step
- Median time between steps
- Cohort breakdown: funnel conversion by user segment, plan, signup source

---

## Retention

### N-Day Retention Curve
- Day 0: users who triggered the activation event
- Day 1/7/14/30: % of those users who returned and triggered any event
- Classic retention curve chart

### Feature Retention
- Retention segmented by whether user used Feature X on Day 0
- Shows which features correlate with retention (not just correlation — actionable for product decisions)

---

## Feature Adoption

Measure which features are being used and by whom:
- Define a feature by an event or event + property value
- Feature adoption rate: users who used it / total active users in period
- Per-plan breakdown: is adoption higher on Pro vs Starter?
- Trend: week-over-week adoption change

---

## Sessions & Page Views

Optional: enable automatic page view tracking via SDK:
- Session detection: 30-min inactivity = new session
- Pages most visited, session duration, bounce rate equivalent
- User journey: sequence of pages/events in a session

---

## Data Model

### `plg_events`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| user_id | varchar(255) | end-user identifier |
| event_name | varchar(200) | |
| properties | json | |
| occurred_at | timestamp | |
| session_id | varchar | nullable |
| sdk_version | varchar(20) | |

Partitioned by `(tenant_id, date(occurred_at))`.

### `plg_funnel_definitions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| steps | json | ordered array of {event_name, filter_conditions} |
| conversion_window_days | int | default 30 |

---

## Integrations

- **CRM** — enrich user profile with product usage data (last active, features used, activation status)
- **User Segmentation** — segment users by usage behaviour (e.g., "power users" = > 20 sessions/month)
- **Feature Flags** — A/B test analysis: conversion by flag variant

---

## Migration

```
895000_create_plg_events_table
895001_create_plg_funnel_definitions_table
895002_create_plg_feature_definitions_table
895003_create_plg_session_summaries_table
```

---

## Related

- [[MOC_PLG]]
- [[feature-flags]] — A/B test variant tracking
- [[user-segmentation]] — behavioural segments
- [[in-app-nps-feedback]] — NPS correlated with usage
- [[MOC_CRM]] — usage data enriches contact records
