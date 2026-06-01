---
type: domain-index
domain: Customer Success
panel: crm
color: "#4ADE80"
---

# Customer Success

Health scores, playbooks, churn risk alerts, NPS, QBR management, and analytics. **Panel:** `/crm` (hosted in the CRM panel — see [[build/decisions/decision-2026-06-01-panel-consolidation]]) — Phase 3.

Customer Success does NOT have its own panel. Its resources appear in the `/crm` panel under the **Customer Success** nav group. CS operates on CRM accounts.

**Displaces**: Gainsight (SMB), ChurnZero, Vitally

---

## Navigation Groups

- **Accounts** — Health Scores, Churn Risk, NPS, QBRs
- **Playbooks** — Playbooks, Active Runs
- **Analytics** — CS Dashboard

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/customer-success/health-scores\|Customer Health Scores]] | `cs.health` | planned | **P3 core** |
| [[domains/customer-success/churn-risk\|Churn Risk Alerts]] | `cs.churn` | planned | **P3 core** |
| [[domains/customer-success/playbooks\|CS Playbooks]] | `cs.playbooks` | planned | P3 |
| [[domains/customer-success/nps\|NPS Surveys]] | `cs.nps` | planned | P3 |
| [[domains/customer-success/qbr\|QBR Management]] | `cs.qbr` | planned | P3 |
| [[domains/customer-success/success-analytics\|Success Analytics]] | `cs.analytics` | planned | P3 |

---

## Key Patterns

- Health scores recalculated via scheduled job (see [[architecture/queue-jobs]])
- Pulls signals cross-domain: CRM (accounts), Support (tickets), Finance (payments)
- Churn/detractor alerts via Core Notifications
- Heavy caching of aggregations (see [[architecture/caching]])
- Builds on [[domains/crm/contacts]] accounts
