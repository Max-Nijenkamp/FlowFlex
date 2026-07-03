---
domain: customer-success
module: health-scores
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Customer Health Scores

Composite health score per customer account combining usage, support, sentiment, engagement, and payment signals into a 0–100 number with green/amber/red tiers. The early-warning core of Customer Success and the anchor module every other CS module hangs off. Hosted in the `/crm` panel under the **Customer Success** nav group.

---

## Module-key

`cs.health`

**Priority:** p3
**Panel:** crm (Customer Success nav group)
**Permission prefix:** `cs.health`
**Tables:** `cs_health_scores`, `cs_health_config`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../crm/contacts/_module\|crm.contacts]] | Scores are per CRM account (read-only) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Tier-drop alerts to CSM |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | Nightly recalc job |
| Soft | [[../../support/tickets/_module\|support.tickets]] | Ticket-volume signal; excluded + weights renormalised when inactive |
| Soft | [[../../finance/invoicing/_module\|finance.invoicing]] | Payment-status signal (overdue invoices) |
| Soft | [[../nps/_module\|cs.nps]] | Sentiment signal (latest NPS response per account) |

---

## Core Features

- Health score: 0–100 composite per account; tiers green (≥70) / amber (40–69) / red (<40) *(assumed)*
- Weighted score factors: support ticket volume, NPS sentiment, payment status (overdue invoices), engagement recency (last activity); product usage = engagement proxy v1 *(assumed: no usage telemetry yet)*
- Configurable factor weights per company (renormalised over active signal sources)
- Score trend over time (one history row per calculation)
- Score breakdown: per-factor contributions stored for explainability
- Account segmentation by health tier
- Automatic recalculation (nightly job)
- Health-change alerts: an account dropping a tier notifies its CSM once per drop

See [[./features/composite-scoring|Composite Scoring feature]] and [[./features/tier-drop-alerts|Tier-Drop Alerts feature]].

---

## Build Manifest

```
database/migrations/xxxx_create_cs_health_scores_table.php
database/migrations/xxxx_create_cs_health_config_table.php
app/Models/CS/{HealthScore,HealthConfig}.php
app/Data/CS/ConfigureHealthData.php
app/Services/CS/HealthScoreService.php
app/Services/CS/SignalRegistry.php
app/Providers/CS/CsServiceProvider.php
app/Console/Commands/CS/RecalculateHealthScoresCommand.php
app/Filament/CRM/Resources/HealthScoreResource.php (CS nav group)
app/Filament/CRM/Pages/HealthDashboardPage.php
database/factories/CS/HealthScoreFactory.php
tests/Feature/CS/HealthScoreTest.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's health scores data
- [ ] Module gating: artifacts hidden when `customer-success.health-scores` inactive
- [ ] Weighted score math; inactive-module factors excluded + weights renormalised
- [ ] Tier thresholds; drop alert once per drop
- [ ] Recalc idempotent, per-account failure continues batch
- [ ] Breakdown contributions sum to score
- [ ] Signal reads never write CRM / support / finance tables

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `AccountService` / `crm_accounts` (read API) | crm.contacts | Iterate customer accounts to score; never writes CRM tables |
| Reads | `TicketService` metrics (read API) | support.tickets | Ticket-volume signal; soft — excluded when inactive |
| Reads | `InvoiceService` payment status (read API) | finance.invoicing | Overdue-invoice signal; soft |
| Reads | latest NPS response (read API) | cs.nps | Sentiment signal; soft |
| Consumes | (none v1) | — | Signals pulled on schedule, not event-driven v1 *(assumed)* |
| Fires | (none) | — | Tier-drop alert is a notification, not a cross-domain domain event *(assumed)* |

**Data ownership:** `cs.health` writes only `cs_health_scores`, `cs_health_config`. All signal inputs are read-only queries through the owning module's service/read API; it never writes CRM, support, or finance tables ([[../../../security/data-ownership]]).

---

## Related

- [[../churn-risk/_module|cs.churn]]
- [[../nps/_module|cs.nps]]
- [[../qbr/_module|cs.qbr]]
- [[../success-analytics/_module|cs.analytics]]
- [[../../crm/contacts/_module|crm.contacts]]
- [[../../../architecture/caching]]
- [[../../../architecture/queue-jobs]]
