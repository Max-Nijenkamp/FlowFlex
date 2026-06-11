---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.revenue-intelligence
status: planned
priority: v1
depends-on: [crm.deals, crm.activities, core.billing, core.rbac]
soft-depends: [ai.copilot, crm.forecasting]
fires-events: []
consumes-events: []
patterns: [custom-pages, queues]
tables: [crm_deal_health, crm_win_loss]
permission-prefix: crm.revenue-intelligence
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Revenue Intelligence

Rule-driven deal health scoring, at-risk deal alerts, and win/loss analysis. Surface which deals need attention and why deals are won or lost. (v1 scoring = deterministic rules, not ML *(assumed)*; LLM summaries via ai.copilot in P3.)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/deals\|crm.deals]] + [[domains/crm/activities\|crm.activities]] | the scoring inputs |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/ai/copilot\|ai.copilot]] | win/loss LLM summarisation (P3) |
| Soft | [[domains/crm/forecasting\|crm.forecasting]] | forecast-risk overlay |

---

## Core Features

- Deal health score: 0–100 from weighted factors — activity recency, stage velocity vs average, engagement, deal age vs cycle norm *(factor weights configurable, defaults assumed: 30/30/20/20)*
- At-risk alerts: stalled deals, no recent activity, slipping close dates
- Win/loss analysis: reasons logged on closed deals (auto-row from `DealWon`/`DealLost` data), patterns surfaced
- Stage conversion rates: where deals get stuck
- Sales velocity: avg time per stage, deal cycle length
- Forecast risk: deals likely to slip the quarter
- Activity correlation: which activity counts correlate with wins (descriptive stats v1)
- Rep coaching insights (per-rep velocity/conversion comparison)

---

## Data Model

### crm_deal_health

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), deal_id FK unique | ulid | one row per open deal |
| score | int | 0–100 |
| factors | jsonb | [{factor, score, weight, detail}] — explainability |
| calculated_at | timestamp | |

### crm_win_loss

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed), deal_id FK unique | ulid | |
| outcome | string | won / lost |
| reason | string | from close flow |
| competitor | string nullable | |
| notes | text nullable | |

Reads `crm_deals`, `crm_activities`.

---

## DTOs

Output only: `DealHealthData` (deal, score, factors[], risk_level), `WinLossAnalysisData` (reason breakdown, competitor table, conversion funnel, velocity stats).

## Services & Actions

- `DealHealthService::recalculate(): RecalcResult` — open deals, per-deal try/catch, upsert health rows
- `DealHealthService::atRisk(int $threshold = 40): Collection` *(assumed threshold)*
- `WinLossService::analysis(CarbonImmutable $from, CarbonImmutable $to): WinLossAnalysisData`
- Win/loss rows created by listeners on deal close *(internal to CRM — direct service call from DealService close path, same-domain rule)*

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RecalculateDealHealthCommand` | default | nightly 04:15 | upsert per deal — re-run safe |

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:crm:winloss:{from}:{to}` | 1 h | TTL only (analysis dashboard) |

---

## Filament

**Nav group:** Intelligence

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DealHealthResource` | #1 (read-only) | at-risk queue sorted by score, factor breakdown |
| `WinLossPage` | #9 report custom page + apex charts | reasons, competitors, funnel |
| `RevenueIntelligenceDashboard` | #6 dashboard page | velocity, conversion, health distribution |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.revenue-intelligence.view-any') && BillingService::hasModule('crm.revenue-intelligence')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`crm.revenue-intelligence.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Health score deterministic over fixture deals (factor math)
- [ ] Stalled deal (no activity 14d *(assumed)*) scores low + appears at-risk
- [ ] Recalc idempotent; per-deal failure doesn't stop batch
- [ ] Win/loss rows created on close with reason/competitor
- [ ] Conversion funnel percentages over fixtures

---

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

---

## Related

- [[domains/crm/deals]]
- [[domains/crm/forecasting]]
- [[domains/ai/copilot]]
- [[architecture/caching]]
