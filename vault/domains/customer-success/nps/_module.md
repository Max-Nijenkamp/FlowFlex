---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.nps
status: planned
build-status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications, foundation.email, foundation.queues]
soft-depends: [cs.health]
fires-events: []
consumes-events: []
patterns: [email, queues]
tables: [cs_nps_surveys, cs_nps_responses]
permission-prefix: cs.nps
encrypted-fields: []
last-reviewed: 2026-06-20
color: "#4ADE80"
---

# NPS Surveys

Net Promoter Score surveys: send, collect via a public token-scoped page, categorise responses, and feed sentiment into health scores. Hosted in the `/crm` panel under the **Customer Success** nav group.

---

## Module-key

`cs.nps`

**Priority:** p3
**Panel:** crm (Customer Success nav group)
**Permission prefix:** `cs.nps`
**Tables:** `cs_nps_surveys`, `cs_nps_responses`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] | Audiences are account contacts (read-only) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Detractor alerts to CSM |
| Hard | [[../../foundation/email-setup/_module\|foundation.email]] | Survey delivery |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | Batched sending |
| Soft | [[../health-scores/_module\|cs.health]] | Sentiment signal consumer (latest response per account) |

---

## Core Features

- NPS survey: "How likely are you to recommend us?" (0–10) + follow-up comment
- Survey sending: manual to an audience (segment / accounts) v1; scheduled / post-interaction later *(assumed)*
- Response capture via email score buttons → public token-scoped response page (one token per recipient)
- Categorisation: promoter (9–10), passive (7–8), detractor (0–6)
- NPS = % promoters − % detractors; trend over surveys
- Detractor alerts: notify CSM on a detractor response
- Feeds the health-score sentiment factor (latest response per account)
- One response per recipient per survey

See [[./features/survey-send|Survey Send]], [[./features/public-collector|Public Collector]], [[./features/sentiment-scoring|Sentiment Scoring]].

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

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's nps data
- [ ] Module gating: artifacts hidden when `cs.nps` inactive
- [ ] One response per recipient; duplicate token use rejected
- [ ] Categorisation boundaries (6/7, 8/9)
- [ ] NPS math over fixtures
- [ ] Detractor alert fires; health sentiment signal exposed
- [ ] Public endpoint rate-limited + token-scoped (no session)
- [ ] Response reads/writes only cs.nps tables; contacts read-only

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | account contacts (read API) | crm.contacts | Survey audience; never writes CRM |
| Reads | account `owner_id` (read API) | crm.contacts | CSM detractor-alert recipient |
| Feeds | latest response per account (read API) | cs.health | Sentiment factor — cs.health pulls this |
| Consumes | (none v1) | — | Sending is user-initiated *(assumed)* |
| Fires | (none) | — | Detractor alert is a notification, not a cross-domain domain event *(assumed)* |

**Data ownership:** `cs.nps` writes only `cs_nps_surveys`, `cs_nps_responses`. Audience contacts are read-only from `crm.contacts`; sentiment is **exposed** to `cs.health` as a read API (cs.health pulls it), never written into cs.health. Detractor alerts dispatch via `core.notifications` ([[../../../security/data-ownership]]).

---

## Related

- [[../health-scores/_module|cs.health]]
- [[../success-analytics/_module|cs.analytics]]
- [[../../crm/contacts/_module|crm.contacts]]
- [[../../../architecture/email]]
- [[../../../architecture/ui-strategy]]
