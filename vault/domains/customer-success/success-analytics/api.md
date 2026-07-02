---
domain: customer-success
module: success-analytics
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Success Analytics — DTOs & API

## DTOs

Output only — this module has no input DTOs (no writes).

### CsMetricsData (output)

| Field | Type | Notes |
|---|---|---|
| retention_rate / churn_rate | float | over the `[from, to]` window |
| nrr | Money-derived % | expansion vs churn; **only when `finance.invoicing` active** |
| health_distribution | array | count per tier (green/amber/red) |
| nps_trend | array | only when `cs.nps` active |
| at_risk_count / recovery_rate | int / float | only when `cs.churn` active |
| csm_performance | array | accounts managed, avg health, at-risk recovered |
| playbook_effectiveness | array | completion rate, health delta; only when `cs.playbooks` active |

Soft sections are absent (not zeroed) when their source module is inactive.

---

## Internal Read API

`CsAnalyticsService::metrics` is consumed only by this module's own dashboard/widgets. It does not expose data to other domains (it is a leaf consumer).

---

## Public / Portal Endpoints

None. The only surface is the authenticated `CsDashboardPage` + its **Export** action (rate-limited, `cs.analytics.view`).
