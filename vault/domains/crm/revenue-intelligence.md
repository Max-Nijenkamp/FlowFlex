---
type: module
domain: CRM & Sales
panel: crm
module-key: crm.revenue-intelligence
status: planned
color: "#4ADE80"
---

# Revenue Intelligence

AI-driven deal health scoring, at-risk deal alerts, and win/loss analysis. Surface which deals need attention and why deals are won or lost.

## Core Features

- Deal health score: 0–100 from activity recency, stage velocity, engagement, deal age
- At-risk alerts: stalled deals, no recent activity, slipping close dates
- Win/loss analysis: reasons logged on closed deals, patterns surfaced
- Stage conversion rates: where deals get stuck
- Sales velocity: avg time per stage, deal cycle length
- Forecast risk: deals likely to slip the quarter
- Activity correlation: which activities correlate with wins
- Rep coaching insights

## Data Model

| Table | Key Columns |
|---|---|
| `crm_deal_health` | company_id, deal_id, score, factors (json), calculated_at |
| `crm_win_loss` | company_id, deal_id, outcome (won/lost), reason, competitor, notes |

Reads from `crm_deals`, `crm_activities`.

## Filament

**Nav group:** Intelligence

- `DealHealthResource` — at-risk deal queue, sorted by risk
- `WinLossPage` (custom page) — win/loss analysis charts
- `RevenueIntelligenceDashboard` (custom page) — velocity, conversion, health

## Cross-Domain / Jobs

- Health recalculated via scheduled job
- Optionally uses [[domains/ai/copilot]] LLM for win/loss summarisation

## Related

- [[domains/crm/deals]]
- [[domains/crm/forecasting]]
- [[architecture/caching]]
