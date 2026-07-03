---
domain: customer-success
module: nps
feature: survey-send
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Survey Send

Create an NPS survey, choose its audience, and send it as batched, token-scoped emails.

## Behaviour

- A survey is created as a draft (`sent_at` null) with a name and an `audience` (`{segment_id}` or `{account_ids}`).
- **Send** resolves the audience to recipient contacts, materialises one `cs_nps_responses` row per recipient with a unique single-use `token` and `score` null, then dispatches batched `NpsSurveyMail` via the queue and sets `sent_at`.
- Re-sending the same survey is guarded by `sent_at` (idempotent — no duplicate recipient rows). Shared suppression is honoured *(assumed)*.
- Each email contains 0–10 score buttons linking to `/nps/{token}`.

## UI

- **Kind**: simple-resource — `NpsSurveyResource`.
- **Page**: "NPS Surveys" at `/crm/nps-surveys` (Customer Success nav group).
- **Layout**: table (name, audience summary, sent_at, response count / NPS); create/edit form with name + audience picker (segment select or account multi-select).
- **Key interactions**: create draft · pick audience · **Send** row action (confirm → `NpsService::send`) · view per-survey stats.
- **States**: empty (no surveys → "create your first NPS survey") · loading (table skeleton) · error (send failure → toast + retry; audience empty → validation) · selected (survey row opened → stats). Send disabled once `sent_at` set.
- **Gating**: `cs.nps.view-any` to view; `cs.nps.manage` to create/edit; `cs.nps.send` to send.

## Data

- Owns / writes: `cs_nps_surveys`, `cs_nps_responses` (recipient rows).
- Reads: audience contacts via `crm.contacts` read API (segment or account_ids) — never CRM tables.
- Cross-domain writes: none — emails dispatch through `foundation.email`; no other domain's tables touched ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: recipient rows consumed by the [[./public-collector|Public Collector]]; delivery via `foundation.email`.
- Shared entity: `crm_contacts` / `crm_accounts` (read-only audience) + segments (via `crm.segments`).

## Test Checklist

### Unit
- [ ] Audience resolution (segment / account_ids) -> recipient contacts; suppression honoured *(assumed)*

### Feature (Pest)
- [ ] `send` materialises one token row per recipient and dispatches batched queued mail; double-send guarded by `sent_at` + lock
- [ ] Send path cites the queued mail throttle (`panel-action` on the trigger)
- [ ] Tenant isolation + permission: send gated, audience own-company only

### Livewire
- [ ] Survey form validates audience; send action confirms + reports recipient count

## Unknowns

- Manual send only v1; scheduled/lifecycle triggers deferred — [[../unknowns]].
- Suppression-list ownership assumed (comms).

## Related

- [[../_module|NPS]] · [[./public-collector|Public Collector]] · [[./sentiment-scoring|Sentiment Scoring]]
- [[../../../../architecture/email]] · [[../../../../security/data-ownership]]
