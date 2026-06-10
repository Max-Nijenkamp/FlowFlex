---
type: module
domain: Customer Success
domain-key: customer-success
panel: crm
module-key: cs.churn
status: planned
priority: p3
depends-on: [cs.health, core.billing, core.rbac, core.notifications]
soft-depends: [cs.playbooks]
fires-events: []
consumes-events: []
patterns: [queues]
tables: [cs_churn_risks]
permission-prefix: cs.churn
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Churn Risk Alerts

Identify accounts at risk of churning and alert the CS team with recommended interventions.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/customer-success/health-scores\|cs.health]] | primary risk signal |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, CSM alerts |
| Soft | [[domains/customer-success/playbooks\|cs.playbooks]] | recommended recovery playbook + one-click run |

---

## Core Features

- Risk detection (rule-based v1): red health tier, tier dropping 2 levels, NPS detractor, overdue invoices, no engagement N days
- Risk level: low / medium / high / critical (factor-count driven *(assumed)*)
- Alert generation: notify assigned CSM (account owner *(assumed: crm account owner_id = CSM)*) once per detection
- Risk reason breakdown: factors stored
- Recommended action: at-risk recovery playbook suggested; one-click run when playbooks active
- Risk resolved when factors clear (auto on re-evaluation)
- At-risk account queue, severity-sorted

---

## Data Model

### cs_churn_risks

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| account_id | ulid FK | one open risk per account (partial unique) |
| risk_level | string | low/medium/high/critical |
| risk_factors | jsonb | [{factor, detail}] |
| assigned_csm_id | ulid nullable | |
| detected_at | timestamp | |
| resolved_at | timestamp nullable | |

---

## DTOs

None input — detection automatic; `ResolveRiskData` (risk_id, note) for manual resolution *(assumed)*.

## Services & Actions

- `ChurnRiskService::evaluate(): EvalResult` — runs after health recalc (chained); opens/updates/resolves risk rows; alerts on new/escalated only
- `RunRecoveryPlaybookAction` — soft-dep bridge

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `EvaluateChurnRiskCommand` | default | nightly (after health recalc) | open-risk upsert; alert on level change only |

---

## Filament

**Nav group:** Customer Success

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ChurnRiskResource` | #1 (read-only + actions) | severity queue, factor breakdown, run-playbook + resolve actions |
| `ChurnRiskWidget` | #6 widget | counts by level |

---

## Permissions

`cs.churn.view-any` · `cs.churn.resolve`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Each detection rule fixture (red tier, detractor, overdue, inactivity)
- [ ] One open risk per account; level escalation re-alerts, same level doesn't
- [ ] Auto-resolve when factors clear
- [ ] Playbook action hidden when module inactive

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

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/playbooks]]
