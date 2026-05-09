---
type: module
domain: Product-Led Growth
panel: plg
cssclasses: domain-plg
phase: 7
status: planned
migration_range: 898000–898999
last_updated: 2026-05-09
---

# In-App NPS & Feedback

Triggered NPS surveys, CES (Customer Effort Score), custom microsurveys, and feature voting. Collects qualitative and quantitative feedback inside the product.

---

## Survey Types

### NPS (Net Promoter Score)
- Classic 0–10 scale: "How likely are you to recommend [Product] to a colleague?"
- Follow-up open text: "What's the main reason for your score?"
- Promoters (9–10), Passives (7–8), Detractors (0–6)
- Calculated NPS = % Promoters − % Detractors

### CES (Customer Effort Score)
- 7-point scale: "How easy was it to [complete this task]?"
- Triggered immediately after key flows (e.g., after completing first import, after onboarding checklist)

### Custom Microsurvey
- 1–3 questions, configurable types: scale / multiple choice / open text
- Use cases: churn survey ("Why are you cancelling?"), win/loss survey, feature satisfaction rating

### Feature Voting
- Public roadmap widget with upvote capability
- Users submit feature requests, vote on existing ones
- Admin tags requests: planned / in progress / shipped / won't do
- Voters notified when status changes

---

## Targeting & Triggers

Same engine as [[in-app-tours-onboarding]] targeting:

| Trigger | Example |
|---|---|
| Time in product | Show NPS to users active > 30 days |
| Event-based | Show CES after `export_completed` event |
| Segment | Show churn survey when cancellation flow opened |
| Frequency | NPS no more than once every 90 days per user |

Timing:
- Inline (appears in page content)
- Slide-in (bottom-right, non-blocking)
- Modal (blocks interaction, use sparingly for churn surveys)

---

## Response Dashboard

### NPS Dashboard
- Current NPS score (rolling 90 days)
- Trend: monthly NPS over 12 months
- Response breakdown: promoter/passive/detractor %
- Open-text verbatim responses, grouped by sentiment (auto-tagged: product/support/pricing/performance)
- Segment breakdown: NPS by active modules, cohort, geography

### CES Dashboard
- Average CES per flow
- Flows with worst effort scores (prioritise UX improvements)

### Feature Votes
- Top-voted features list
- Voter segments (what persona/module-set is asking for this?)

---

## CRM Integration

After survey completion:
- NPS score attached to CRM contact record
- Score change triggers: if score drops from ≥7 to <7 → alert CSM in CRM
- Churn risk flag: Detractor (0–6) → flag contact in CRM as churn risk
- Promoter flag: can trigger automated referral ask email via [[MOC_Marketing]]

---

## Data Model

### `plg_surveys`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| name | varchar(200) | |
| type | enum | nps/ces/custom/feature_voting |
| questions | json | array of question configs |
| targeting_rules | json | |
| trigger | json | {type, event, delay_seconds} |
| frequency_days | int | min days between shows per user |
| status | enum | draft/active/paused |

### `plg_survey_responses`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| survey_id | ulid | FK |
| user_id | varchar | end-user identifier |
| responses | json | {question_id: answer} |
| nps_score | int | nullable, 0–10 |
| submitted_at | timestamp | |
| crm_contact_id | ulid | nullable, FK if user identified |

---

## Migration

```
898000_create_plg_surveys_table
898001_create_plg_survey_responses_table
898002_create_plg_feature_votes_table
898003_create_plg_feature_requests_table
```

---

## Related

- [[MOC_PLG]]
- [[user-segmentation]] — target by segment
- [[product-usage-analytics]] — correlate NPS with usage patterns
- [[MOC_CRM]] — NPS score attached to contact record
- [[in-app-tours-onboarding]] — shared targeting engine
