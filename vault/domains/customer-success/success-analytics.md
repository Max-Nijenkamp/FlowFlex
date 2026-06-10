---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.analytics
status: planned
priority: p3
depends-on: [cs.health, core.billing, core.rbac]
soft-depends: [cs.churn, cs.nps, cs.playbooks, finance.invoicing]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: []
permission-prefix: cs.analytics
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Success Analytics

Retention, churn rate, NPS trends, health distribution, and CSM performance dashboards. Owns no tables.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/customer-success/health-scores\|cs.health]] | core metrics |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | churn / nps / playbooks / finance.invoicing | sections hidden when inactive (NRR needs invoicing) |

---

## Core Features

- Retention rate and churn rate over time (account lifecycle stage transitions *(assumed: churned = lifecycle_stage churned)*)
- Net revenue retention (NRR) — expansion vs churn from invoice revenue per account
- Health score distribution across the customer base
- NPS trend
- At-risk account count and recovery rate
- CSM performance: accounts managed, avg health, at-risk recovered
- Playbook effectiveness: completion rates, health delta after run
- Export reports

---

## Data Model

No additional tables. Aggregates from `cs_health_scores`, `cs_churn_risks`, `cs_nps_responses`, `cs_playbook_runs`, CRM accounts, invoices.

## DTOs

Output only: `CsMetricsData`.

## Services & Actions

- `CsAnalyticsService::metrics(from, to): CsMetricsData` — brick/money for NRR; soft sections conditional; no N+1

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:cs:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

---

## Filament

**Nav group:** Customer Success

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CsDashboardPage` | #6 dashboard page + apex charts | export |

---

## Permissions

`cs.analytics.view`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Churn/retention math over lifecycle fixtures
- [ ] NRR via brick/money; section hidden without invoicing
- [ ] Playbook health-delta over fixtures
- [ ] Soft sections hidden when inactive

---

## Build Manifest

```
app/Data/CS/CsMetricsData.php
app/Services/CS/CsAnalyticsService.php
app/Filament/CRM/Pages/CsDashboardPage.php
app/Filament/CRM/Widgets/{RetentionWidget,NrrWidget,HealthDistributionWidget,CsmPerformanceWidget}.php
tests/Feature/CS/CsAnalyticsTest.php
```

---

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/churn-risk]]
- [[architecture/caching]]
