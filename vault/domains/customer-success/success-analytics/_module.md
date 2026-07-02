---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.analytics
status: planned
build-status: planned
priority: p3
depends-on: [cs.health, core.billing, core.rbac]
soft-depends: [cs.churn, cs.nps, cs.playbooks, finance.invoicing]
fires-events: []
consumes-events: []
patterns: [custom-pages, money]
tables: []
permission-prefix: cs.analytics
encrypted-fields: []
last-reviewed: 2026-06-20
color: "#4ADE80"
---

# Success Analytics

Retention, churn rate, NPS trend, health distribution, NRR, and CSM performance dashboards. **Owns no tables** — a pure read-only aggregation layer over other CS/finance modules. Hosted in the `/crm` panel under the **Customer Success** nav group.

---

## Module-key

`cs.analytics`

**Priority:** p3
**Panel:** crm (Customer Success nav group)
**Permission prefix:** `cs.analytics`
**Tables:** none (aggregates from other modules)

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../health-scores/_module\|cs.health]] | Core metrics (health distribution, at-risk) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Soft | [[../churn-risk/_module\|cs.churn]] | At-risk count + recovery rate |
| Soft | [[../nps/_module\|cs.nps]] | NPS trend section |
| Soft | [[../playbooks/_module\|cs.playbooks]] | Playbook effectiveness section |
| Soft | [[../../finance/invoicing/_module\|finance.invoicing]] | NRR (needs invoice revenue) |

---

## Core Features

- Retention rate + churn rate over time (account lifecycle transitions *(assumed: churned = lifecycle_stage churned)*)
- Net revenue retention (NRR) — expansion vs churn from invoice revenue per account
- Health-score distribution across the customer base
- NPS trend
- At-risk account count + recovery rate
- CSM performance: accounts managed, avg health, at-risk recovered
- Playbook effectiveness: completion rates, health delta after run
- Export reports (rate-limited)

See [[./features/retention-nrr|Retention & NRR]] and [[./features/cs-dashboard|CS Dashboard]].

---

## Build Manifest

```
app/Data/CS/CsMetricsData.php
app/Services/CS/CsAnalyticsService.php
app/Filament/CRM/Pages/CsDashboardPage.php
app/Filament/CRM/Widgets/{RetentionWidget,NrrWidget,HealthDistributionWidget,CsmPerformanceWidget}.php
tests/Feature/CS/CsAnalyticsTest.php
```

(No migration / model / factory — this module owns no tables.)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Churn/retention math over lifecycle fixtures
- [ ] NRR via brick/money; section hidden without invoicing
- [ ] Playbook health-delta over fixtures
- [ ] Soft sections hidden when inactive
- [ ] Export action rate-limited
- [ ] Aggregation reads only via read APIs — writes nothing anywhere

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | health scores + distribution (read API) | cs.health | Core metrics |
| Reads | open/resolved risks (read API) | cs.churn | At-risk count, recovery rate; soft |
| Reads | NPS trend (read API) | cs.nps | Soft |
| Reads | playbook run/step stats (read API) | cs.playbooks | Effectiveness; soft |
| Reads | invoice revenue per account (read API) | finance.invoicing | NRR; soft |
| Reads | account lifecycle + owner (read API) | crm.contacts | Churn/retention, CSM performance |
| Consumes | (none) | — | Pure pull, on view |
| Fires | (none) | — | Read-only module |

**Data ownership:** `cs.analytics` **owns no tables and writes nothing, anywhere.** Every metric is a read-only aggregation through the owning module's service/read API; NRR arithmetic uses `brick/money`. It is the purest expression of the data-ownership rule ([[../../../security/data-ownership]]).

---

## Related

- [[../health-scores/_module|cs.health]]
- [[../churn-risk/_module|cs.churn]]
- [[../nps/_module|cs.nps]]
- [[../playbooks/_module|cs.playbooks]]
- [[../../../architecture/caching]]
- [[../../../architecture/packages]]
