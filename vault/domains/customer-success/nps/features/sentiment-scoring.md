---
domain: customer-success
module: nps
feature: sentiment-scoring
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Sentiment Scoring

Compute the NPS metric and expose per-account sentiment as a read API for health scoring and analytics.

## Behaviour

- Categorisation on capture: promoter 9–10, passive 7–8, detractor 0–6.
- `scoreFor(surveyId)` = %promoters − %detractors over answered rows (−100…+100).
- `trend()` returns NPS across surveys over time.
- `latestForAccount(accountId)` returns the most recent answered response per account — the signal `cs.health` pulls for its sentiment factor and `cs.analytics` for the NPS-trend panel.
- All computed from stored responses; no external calls.

## UI

- **Kind**: custom-page — `NpsDashboardPage` (with chart widgets).
- **Page**: "NPS" at `/crm/nps` (Customer Success nav group).
- **Layout**: headline NPS number + delta; trend line chart over surveys; promoter/passive/detractor breakdown bar; recent detractor comments list.
- **Key interactions**: date/survey range filter; drill into a survey's responses (→ `NpsResponseResource`).
- **States**: empty (no answered responses → "no NPS data yet") · loading (chart skeletons) · error (aggregate query fails → retry) · selected (a survey/segment focused).
- **Gating**: `cs.nps.view-any`.

## Data

- Owns / writes: nothing new — reads `cs_nps_responses` (own table).
- Reads: own responses only. Exposes `latestForAccount` as an internal read API.
- Cross-domain writes: none — sentiment is **pulled** by `cs.health` / `cs.analytics`; this module never writes into them ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: `cs.health` (sentiment factor), `cs.analytics` (NPS trend) — both via `latestForAccount` / `trend` read APIs.
- Shared entity: `crm_accounts` (read-only key on responses).

## Test Checklist

### Unit
- [ ] NPS = %promoters - %detractors over ANSWERED rows only

### Feature (Pest)
- [ ] `latestForAccount` returns the newest answered response for the health read API
- [ ] Tenant isolation: scores computed per company

### Livewire
- [ ] Survey results view shows score + distribution; hidden without the nps permission/module

## Unknowns

- NPS window/segmentation defaults (all-time vs rolling) not specified — [[../unknowns]].

## Related

- [[../_module|NPS]] · [[./public-collector|Public Collector]]
- [[../../health-scores/_module|cs.health]] · [[../../success-analytics/_module|cs.analytics]]
