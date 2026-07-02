---
domain: crm
module: revenue-intelligence
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# CRM Revenue Intelligence

Rule-driven deal health scoring, at-risk deal alerts, and win/loss analysis. Surfaces which deals need attention and why deals are won or lost. v1 scoring is deterministic rules, not ML *(assumed)*; LLM summaries via ai.copilot are P3.

> This module is planned for rebuild. Prior "shipped/complete" references reflect the stripped codebase; see [[../../../decisions/decision-2026-06-19-strip-to-app-admin-shell]] for context.

## Module Key

```
module-key:        crm.revenue-intelligence
priority:          v1
panel:             crm
permission-prefix: crm.revenue-intelligence
tables:            [crm_deal_health, crm_win_loss]
```

## Dependencies

| Kind | Module | Why |
|---|---|---|
| Hard | [[../../crm/deals/_module\|Deals]] | Scoring inputs. |
| Hard | [[../../crm/activities/_module\|Activities]] | Scoring inputs. |
| Hard | [[../../../infrastructure/module-catalog\|core.billing]] | Module gating. |
| Hard | [[../../../security/authn-authz\|core.rbac]] | Permission enforcement. |
| Soft | [[../../ai/copilot/_module\|AI Copilot]] | Win/loss LLM summarisation (P3). |
| Soft | [[../../crm/forecasting/_module\|Forecasting]] | Forecast-risk overlay. |

## Core Features

- Deal health score 0–100 from weighted factors — activity recency, stage velocity vs average, engagement, deal age vs cycle norm. Factor weights are configurable, defaults assumed 30/30/20/20.
- At-risk alerts — stalled deals, no recent activity, slipping close dates.
- Win/loss analysis — reasons logged on closed deals, auto-row from DealWon/DealLost data, patterns surfaced.
- Stage conversion rates — where deals get stuck.
- Sales velocity — average time per stage, deal cycle length.
- Forecast risk — deals likely to slip the quarter.
- Activity correlation — which activity counts correlate with wins (descriptive stats v1).
- Rep coaching insights — per-rep velocity / conversion comparison.

See [[features/deal-health-scoring]] and [[features/win-loss-analysis]] for the flows.

## Build Manifest

```
database/migrations/xxxx_create_crm_deal_health_table.php
database/migrations/xxxx_create_crm_win_loss_table.php
app/Models/CRM/{DealHealth,WinLoss}.php
app/Data/CRM/{DealHealthData,WinLossAnalysisData}.php
app/Services/CRM/{DealHealthService,WinLossService}.php
app/Console/Commands/CRM/RecalculateDealHealthCommand.php
app/Filament/CRM/Resources/DealHealthResource.php
app/Filament/CRM/Pages/{WinLossPage,RevenueIntelligenceDashboard}.php
database/factories/CRM/DealHealthFactory.php
tests/Feature/CRM/{DealHealthTest,WinLossAnalysisTest}.php
```

## Test Checklist

- [ ] Tenant isolation + module gating enforced.
- [ ] Health score deterministic over fixture deals (factor math).
- [ ] Stalled deal (no activity 14d *(assumed)*) scores low and appears at-risk.
- [ ] Recalc idempotent; a per-deal failure doesn't stop the batch.
- [ ] Win/loss rows created on close with reason / competitor.
- [ ] Conversion funnel percentages correct over fixtures.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | Deal read API | [[../deals/_module\|crm.deals]] | Scoring / funnel / velocity inputs (read-only). |
| Reads | Activity read API | [[../activities/_module\|crm.activities]] | Recency + engagement signals. |
| Reads | `crm_email_*` read | [[../email/_module\|crm.email]] *(assumed)* | Email-tracking signals. |
| Consumes | `DealWon` / `DealLost` | crm.deals | Writes `crm_win_loss` row. |
| Consumes | `ActivityLogged` | crm.activities | Recompute health. |
| Consumes | `EmailTracked` | crm.email *(assumed)* | Engagement signal. |
| Consumes | `DealRoomViewed` | [[../deal-rooms/_module\|crm.deal-rooms]] | Engagement signal. |
| Fires | `DealHealthChanged` *(assumed)* | crm.sequences / notifications | At-risk alerting. |

> [!warning] UNVERIFIED
> This module must **not** write `crm_deals`. Health scores live in its own `crm_deal_health` table keyed by `deal_id`. If any spec asserts a write onto `crm_deals`, raise an ADR before build.

**Data ownership:** `revenue-intelligence` writes only `crm_deal_health`, `crm_win_loss`; all cross-domain effects go through events / owning-service APIs ([[../../../security/data-ownership]]).

## Related

- [[../deals/_module|Deals]]
- [[../forecasting/_module|Forecasting]]
- [[../../ai/copilot/_module|AI Copilot]]
- [[../../../architecture/caching|Caching]]
- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../../../glossary]]
