---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.nps
status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications, foundation.email, foundation.queues]
soft-depends: [cs.health]
fires-events: []
consumes-events: []
patterns: [email, queues]
tables: [cs_nps_surveys, cs_nps_responses]
permission-prefix: cs.nps
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# NPS Surveys

Net Promoter Score surveys: send, collect, categorise responses, and feed sentiment into health scores.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | audiences are account contacts |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] + [[domains/foundation/email-setup\|foundation.email]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, detractor alerts, batched sending |
| Soft | [[domains/customer-success/health-scores\|cs.health]] | sentiment signal source |

---

## Core Features

- NPS survey: "How likely are you to recommend us?" (0–10) + follow-up comment
- Survey sending: manual to audience (segment/accounts) v1; scheduled/post-interaction later *(assumed)*
- Response capture via email score buttons → public response page (token per recipient)
- Categorisation: promoter (9–10), passive (7–8), detractor (0–6)
- NPS = % promoters − % detractors; trend over surveys
- Detractor alerts: notify CSM on detractor response
- Feeds health score sentiment factor (latest response per account)
- One response per recipient per survey

---

## Data Model

### cs_nps_surveys — id, company_id (indexed), name, audience (jsonb), sent_at nullable, deleted_at
### cs_nps_responses

| Column | Type | Notes |
|---|---|---|
| id, survey_id FK, company_id (indexed) | ulid | |
| account_id / contact_id | ulid | |
| token | uuid unique | response link |
| score | int 0–10 nullable | null = sent, unanswered |
| category | string nullable | computed |
| comment | text nullable | |
| responded_at | timestamp nullable | |

Unique `(survey_id, contact_id)`.

---

## DTOs

### CreateNpsSurveyData — name, audience {segment_id or account_ids[]}
### NpsResponseData (public) — token (valid, unanswered), score (0–10), comment? — rate-limited

## Services & Actions

- `NpsService::send(surveyId)` — materialises recipient rows + batched mails (suppression honored *(assumed: marketing suppression shared)*)
- `RecordNpsResponseAction` — categorise, detractor → CSM alert, health signal update
- `NpsService::scoreFor(surveyId)` / `trend()`

---

## Filament

**Nav group:** Customer Success

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `NpsSurveyResource` | #1 CRUD resource | send action, response stats |
| `NpsResponseResource` | #1 (read-only) | category filter |
| `NpsDashboardPage` | #6 dashboard page | score, trend, breakdown |

Public response page: Vue + Inertia `/nps/{token}` — ui-strategy row #16.

---

## Permissions

`cs.nps.view-any` · `cs.nps.manage` · `cs.nps.send`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] One response per recipient; duplicate token use rejected
- [ ] Categorisation boundaries (6/7, 8/9)
- [ ] NPS math over fixtures
- [ ] Detractor alert fires; health signal updated
- [ ] Public endpoint rate-limited

---

## Build Manifest

```
database/migrations/xxxx_create_cs_nps_surveys_table.php
database/migrations/xxxx_create_cs_nps_responses_table.php
app/Models/CS/{NpsSurvey,NpsResponse}.php
app/Data/CS/{CreateNpsSurveyData,NpsResponseData}.php
app/Services/CS/NpsService.php
app/Actions/CS/RecordNpsResponseAction.php
app/Mail/CS/NpsSurveyMail.php
app/Http/Controllers/NpsResponseController.php + resources/js/Pages/Nps/Respond.vue
app/Filament/CRM/Resources/{NpsSurveyResource,NpsResponseResource}.php
app/Filament/CRM/Pages/NpsDashboardPage.php
database/factories/CS/{NpsSurveyFactory,NpsResponseFactory}.php
tests/Feature/CS/NpsTest.php
```

---

## Related

- [[domains/customer-success/health-scores]]
- [[architecture/email]]
