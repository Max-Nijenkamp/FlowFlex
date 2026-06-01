---
type: module
domain: Customer Success
panel: crm
module-key: cs.churn
status: planned
color: "#4ADE80"
---

# Churn Risk Alerts

Identify accounts at risk of churning and alert the CS team with recommended interventions.

## Core Features

- Risk detection: low health score, declining usage, support escalations, missed payments, no recent engagement
- Risk level: low / medium / high / critical
- Alert generation: notify assigned CSM when an account becomes at-risk
- Risk reason breakdown: why this account is flagged
- Recommended action: suggest playbook to run (e.g. at-risk recovery)
- Risk trend tracking
- Churn prediction (basic scoring; ML later)
- At-risk account queue for CS team

## Data Model

| Table | Key Columns |
|---|---|
| `cs_churn_risks` | company_id, account_id, risk_level, risk_factors (json), detected_at, resolved_at, assigned_csm_id |

## Filament

**Nav group:** Accounts

- `ChurnRiskResource` — at-risk queue, sorted by severity
- Recommended playbook action per risk
- `ChurnRiskWidget` — at-risk count by level

## Cross-Domain / Events / Jobs

- Reads health scores; scheduled job evaluates risk
- Fires alerts via Core Notifications

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/playbooks]]
