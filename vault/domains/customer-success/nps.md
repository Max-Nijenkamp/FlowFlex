---
type: module
domain: Customer Success
panel: crm
module-key: cs.nps
status: planned
color: "#4ADE80"
---

# NPS Surveys

Net Promoter Score surveys: send, collect, categorise responses, and feed sentiment into health scores.

## Core Features

- NPS survey: "How likely are you to recommend us?" (0–10) + follow-up comment
- Survey sending: scheduled, post-interaction, or manual to account contacts
- Response categorisation: promoter (9–10), passive (7–8), detractor (0–6)
- NPS calculation: % promoters − % detractors
- NPS trend over time
- Detractor alerts: notify CSM when a detractor responds
- Response feeds into customer health score sentiment factor
- Survey via email with embedded score buttons

## Data Model

| Table | Key Columns |
|---|---|
| `cs_nps_surveys` | company_id, name, audience (json), sent_at |
| `cs_nps_responses` | survey_id, company_id, account_id, contact_id, score, category, comment, responded_at |

## Filament

**Nav group:** Accounts

- `NpsSurveyResource` — create, send surveys
- `NpsResponseResource` — responses with category filter
- `NpsDashboardPage` (custom page) — NPS score, trend, breakdown

## Cross-Domain / Events

- Feeds sentiment into [[domains/customer-success/health-scores]]
- Detractor → churn risk signal

## Related

- [[domains/customer-success/health-scores]]
- [[architecture/email]]
