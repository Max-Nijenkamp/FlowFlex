---
type: module
domain: Product-Led Growth
panel: plg
cssclasses: domain-plg
phase: 7
status: planned
migration_range: 899000–909999
last_updated: 2026-05-09
---

# User Segmentation

Segment end-users by usage behaviour, account properties, cohort, plan, and geography. Segments feed feature flag targeting, tour triggering, NPS targeting, and email marketing.

---

## Segment Types

### Behavioural Segments
Built from product events (from [[product-usage-analytics]]):
- "Power users" = users who logged > 20 events in last 30 days
- "At risk" = active users who haven't logged in for 14+ days
- "Just activated" = users who fired `onboarding_completed` in last 7 days
- "Feature X non-adopters" = users who have NOT fired `feature_x_used` event

### Account Property Segments
Built from user/company traits:
- Plan tier: Starter / Growth / Enterprise
- Geographic region: EU / APAC / North America
- Company size: solo / SMB (2–50) / mid-market (51–500) / enterprise (500+)
- Signup source: organic / paid / referral
- Account age: < 30 days / 30–90 days / > 90 days

### Cohort Segments
- Users who signed up in a specific month/quarter
- Users who upgraded from plan A to plan B
- Users who came from a specific campaign (UTM source)

### Computed / AI Segments (Phase 8 extension)
- Churn propensity score > threshold
- LTV prediction tier
- Ideal Customer Profile (ICP) match score

---

## Segment Builder

No-code rule builder in admin:
```
Include users where:
  [plan] [is] [enterprise]
  AND [last_event_date] [is after] [30 days ago]
  AND [country] [is in] [NL, DE, BE, FR]
```

- AND / OR logic with nesting
- Preview: "This segment currently contains 847 users"
- Live sync: segments recomputed nightly (batch) or on-demand (for small segments)
- Segment size history chart

---

## Segment Destinations

Once a segment is defined, it can be used in:
- [[feature-flags]] — enable feature for this segment
- [[in-app-tours-onboarding]] — show tour to this segment
- [[in-app-nps-feedback]] — survey this segment
- [[in-app-changelog-announcements]] — show announcement to this segment
- Email marketing sync (Mailchimp / Klaviyo / custom webhook)
- CRM tagging: tag all CRM contacts matching a segment

---

## User Profiles

Each tracked end-user has a profile:
- Identity: user_id (from product), email (if provided), name
- Traits: all properties ever associated (last_seen, plan, company_id, etc.)
- Event history: latest 100 events
- Segment membership: which segments they currently belong to
- Survey responses: NPS score, last CES rating
- Tour completions: which tours shown, which completed

---

## Data Model

### `plg_segments`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| description | text | nullable |
| rules | json | rule tree |
| user_count | int | updated nightly |
| last_computed_at | timestamp | |

### `plg_segment_memberships`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| segment_id | ulid | FK |
| user_id | varchar | end-user identifier |
| added_at | timestamp | |
| removed_at | timestamp | nullable |

### `plg_user_profiles`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| user_id | varchar(255) | unique per tenant |
| email | varchar | nullable |
| display_name | varchar | nullable |
| traits | json | key-value pairs |
| first_seen_at | timestamp | |
| last_seen_at | timestamp | |
| nps_score | int | nullable, last received |

---

## Migration

```
899000_create_plg_segments_table
899001_create_plg_segment_memberships_table
899002_create_plg_user_profiles_table
899003_create_plg_user_traits_log_table
```

---

## Related

- [[MOC_PLG]]
- [[product-usage-analytics]] — event source for behavioural segments
- [[feature-flags]] — segment-based targeting
- [[in-app-tours-onboarding]] — segment-targeted tours
- [[in-app-nps-feedback]] — segment-targeted surveys
- [[MOC_CRM]] — segment sync to CRM contacts
- [[MOC_Marketing]] — segment sync to email platform
