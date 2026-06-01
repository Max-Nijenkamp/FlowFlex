---
type: module
domain: Customer Success
panel: crm
module-key: cs.health
status: planned
color: "#4ADE80"
---

# Customer Health Scores

Composite health score per customer account combining usage, support, sentiment, and engagement signals. Early warning for churn.

## Core Features

- Health score: 0–100 composite per account, colour-coded (green/amber/red)
- Score factors (weighted): product usage, support ticket volume, NPS/sentiment, payment status, engagement recency
- Configurable factor weights per company
- Score trend over time
- Score breakdown: see which factors drive the score
- Account segmentation by health tier
- Automatic recalculation (scheduled job)
- Health change alerts (account drops a tier)

## Data Model

| Table | Key Columns |
|---|---|
| `cs_health_scores` | company_id, account_id, score, factors (json breakdown), tier, calculated_at |
| `cs_health_config` | company_id, factor_weights (json) |

## Filament

**Nav group:** Accounts

- `HealthScoreResource` — list accounts by health, drill into factors
- `HealthDashboardPage` (custom page) — health distribution, at-risk accounts
- Health trend chart per account

## Cross-Domain / Jobs

- Pulls signals from CRM (account), Support (tickets), Finance (payment status)
- Recalculated via scheduled job (see [[architecture/queue-jobs]])

## Related

- [[domains/customer-success/churn-risk]]
- [[domains/crm/contacts]]
- [[domains/support/tickets]]
