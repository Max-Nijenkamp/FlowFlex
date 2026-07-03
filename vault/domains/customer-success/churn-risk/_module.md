---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.churn
status: planned
build-status: planned
priority: p3
depends-on: [cs.health, core.billing, core.rbac, core.notifications]
soft-depends: [cs.playbooks]
fires-events: []
consumes-events: []
patterns: [queues]
tables: [cs_churn_risks]
permission-prefix: cs.churn
encrypted-fields: []
last-reviewed: 2026-06-20
color: "#4ADE80"
---

# Churn Risk Alerts

Identify accounts at risk of churning and alert the CS team with recommended interventions. Rule-based v1, chained after the nightly health recalc. Hosted in the `/crm` panel under the **Customer Success** nav group.

---

## Module-key

`cs.churn`

**Priority:** p3
**Panel:** crm (Customer Success nav group)
**Permission prefix:** `cs.churn`
**Tables:** `cs_churn_risks`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../health-scores/_module\|cs.health]] | Primary risk signal (red tier, tier drop) |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Hard | [[../../core/notifications/_module\|core.notifications]] | CSM at-risk alerts |
| Soft | [[../playbooks/_module\|cs.playbooks]] | Recommended recovery playbook + one-click run |

---

## Core Features

- Risk detection (rule-based v1): red health tier, tier dropping 2 levels, NPS detractor, overdue invoices, no engagement N days
- Risk level: low / medium / high / critical (factor-count driven *(assumed)*)
- Alert generation: notify assigned CSM (account owner *(assumed: crm account owner_id = CSM)*) once per detection / escalation
- Risk reason breakdown: factors stored for explainability
- Recommended action: at-risk recovery playbook suggested; one-click run when playbooks active
- Risk auto-resolved when factors clear (on re-evaluation)
- At-risk account queue, severity-sorted

See [[./features/rule-based-detection|Rule-Based Detection]] and [[./features/at-risk-queue|At-Risk Queue]].

---

## Build Manifest

```
database/migrations/xxxx_create_cs_churn_risks_table.php
app/Models/CS/ChurnRisk.php
app/Services/CS/ChurnRiskService.php
app/Actions/CS/RunRecoveryPlaybookAction.php
app/Console/Commands/CS/EvaluateChurnRiskCommand.php
app/Filament/CRM/Resources/ChurnRiskResource.php
app/Filament/CRM/Widgets/ChurnRiskWidget.php
database/factories/CS/ChurnRiskFactory.php
tests/Feature/CS/ChurnRiskTest.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's churn risk data
- [ ] Module gating: artifacts hidden when `cs.churn` inactive
- [ ] Each detection rule fixture (red tier, detractor, overdue, inactivity)
- [ ] One open risk per account; level escalation re-alerts, same level doesn't
- [ ] Auto-resolve when factors clear
- [ ] Playbook action hidden when module inactive
- [ ] Evaluation reads health/nps/finance via read APIs, never their tables

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `HealthScoreService` breakdown/tier (read API) | cs.health | Primary risk signal; chained after health recalc |
| Reads | latest NPS response (read API) | cs.nps | Detractor factor; soft |
| Reads | overdue invoices (read API) | finance.invoicing | Payment factor; soft |
| Reads | account `owner_id` (read API) | crm.contacts | CSM recipient for alerts |
| Consumes | (none v1) | — | Evaluation is schedule-chained, not event-driven *(assumed)* |
| Fires | (none) | — | At-risk alert is a notification; recovery run is an in-process action into cs.playbooks *(assumed)* |

**Data ownership:** `cs.churn` writes only `cs_churn_risks`. All risk inputs are read-only queries through the owning module's service/read API; it never writes cs.health, cs.nps, finance, or CRM tables. Recovery-playbook launch calls `cs.playbooks`' own service (which writes its own tables) ([[../../../security/data-ownership]]).

---

## Related

- [[../health-scores/_module|cs.health]]
- [[../playbooks/_module|cs.playbooks]]
- [[../success-analytics/_module|cs.analytics]]
- [[../../../architecture/queue-jobs]]
