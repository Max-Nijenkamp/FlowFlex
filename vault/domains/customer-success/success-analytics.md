---
type: module
domain: Customer Success
panel: crm
module-key: cs.analytics
status: planned
color: "#4ADE80"
---

# Success Analytics

Retention, churn rate, NPS trends, health distribution, and CSM performance dashboards.

## Core Features

- Retention rate and churn rate over time
- Net revenue retention (NRR) — expansion vs churn
- Health score distribution across the customer base
- NPS trend
- At-risk account count and recovery rate
- CSM performance: accounts managed, avg health, churn prevented
- Playbook effectiveness: completion rates, impact on health
- Export reports

## Data Model

No additional tables. Aggregates from `cs_health_scores`, `cs_churn_risks`, `cs_nps_responses`, `cs_playbook_runs`, plus CRM/Finance for revenue.

## Filament

**Nav group:** Analytics

- `CsDashboardPage` (custom dashboard) — chart widgets

## Related

- [[domains/customer-success/health-scores]]
- [[domains/customer-success/churn-risk]]
- [[architecture/performance]]
