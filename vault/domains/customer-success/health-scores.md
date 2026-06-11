---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.health
status: planned
priority: p3
depends-on: [crm.contacts, core.billing, core.rbac, core.notifications, foundation.queues]
soft-depends: [support.tickets, finance.invoicing, cs.nps]
fires-events: []
consumes-events: []
patterns: [custom-pages, queues]
tables: [cs_health_scores, cs_health_config]
permission-prefix: cs.health
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Customer Health Scores

Composite health score per customer account combining usage, support, sentiment, and engagement signals. Early warning for churn. The CS anchor — build first (in `/crm` panel, CS nav group).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/contacts\|crm.contacts]] | scores per CRM account |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, tier-drop alerts, recalc job |
| Soft | [[domains/support/tickets\|support.tickets]] (ticket volume), [[domains/finance/invoicing\|finance.invoicing]] (payment status), [[domains/customer-success/nps\|cs.nps]] (sentiment) | signal sources — factors of inactive modules excluded + weights renormalised |

---

## Core Features

- Health score: 0–100 composite per account, tiers green (≥70) / amber (40–69) / red (<40) *(assumed)*
- Score factors (weighted): support ticket volume, NPS sentiment, payment status (overdue invoices), engagement recency (last activity); product usage = engagement proxy v1 *(assumed: no usage telemetry yet)*
- Configurable factor weights per company (renormalised over active sources)
- Score trend over time (history rows)
- Score breakdown: factor contributions stored for explainability
- Account segmentation by health tier
- Automatic recalculation (nightly job)
- Health change alerts: account drops a tier → CSM notified (once per drop)

---

## Data Model

### cs_health_scores

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| account_id | ulid FK crm_accounts | |
| score | int 0–100 | |
| factors | jsonb | [{factor, value, weight, contribution}] |
| tier | string | green/amber/red |
| calculated_at | timestamp | history kept (one row per calc, latest flagged *(assumed: `is_current` bool)*) |

### cs_health_config — id, company_id unique, factor_weights (jsonb), tier_thresholds (jsonb)

---

## DTOs

### ConfigureHealthData — factor_weights{} (sum 100, factors in registry), tier_thresholds

## Services & Actions

- `HealthScoreService::recalculate(): RecalcResult` — per account: gather active signals, weighted score, tier; per-account try/catch; tier drop → notification
- `HealthScoreService::breakdown(accountId)` / `trend(accountId)`
- Signal providers registered per soft-dep module (gap pattern as analytics MetricRegistry *(assumed: CS-local signal registry)*)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RecalculateHealthScoresCommand` | default | nightly 04:30 | new snapshot rows; current-flag swap transactional |

---

## Filament

**Nav group:** Customer Success (in `/crm`)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `HealthScoreResource` | #1 (read-only) | tier filter, factor breakdown on view, trend chart |
| `HealthDashboardPage` | #6 dashboard page | distribution, at-risk list |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('cs.health.view-any') && BillingService::hasModule('cs.health')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`cs.health.view-any` · `cs.health.configure`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Weighted score math; inactive-module factors excluded + weights renormalised
- [ ] Tier thresholds; drop alert once per drop
- [ ] Recalc idempotent, per-account failure continues batch
- [ ] Breakdown contributions sum to score

---

## Build Manifest

```
database/migrations/xxxx_create_cs_health_scores_table.php
database/migrations/xxxx_create_cs_health_config_table.php
app/Models/CS/{HealthScore,HealthConfig}.php
app/Data/CS/ConfigureHealthData.php
app/Services/CS/HealthScoreService.php
app/Providers/CS/CsServiceProvider.php
app/Console/Commands/CS/RecalculateHealthScoresCommand.php
app/Filament/CRM/Resources/HealthScoreResource.php (CS nav group)
app/Filament/CRM/Pages/HealthDashboardPage.php
database/factories/CS/HealthScoreFactory.php
tests/Feature/CS/HealthScoreTest.php
```

---

## Related

- [[domains/customer-success/churn-risk]]
- [[domains/crm/contacts]]
- [[domains/support/tickets]]
