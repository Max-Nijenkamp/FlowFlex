---
domain: customer-success
module: success-analytics
feature: cs-dashboard
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# CS Dashboard

The single Customer Success command view: health distribution, NPS trend, at-risk, CSM performance, playbook effectiveness, retention/NRR — with export.

## Behaviour

- `CsDashboardPage` calls `CsAnalyticsService::metrics(from, to)` and renders each section as a widget/chart.
- Soft sections appear only when their source module is active: NPS trend (`cs.nps`), at-risk + recovery (`cs.churn`), playbook effectiveness (`cs.playbooks`), NRR (`finance.invoicing`).
- A date-range filter drives every section; results are cached per company + window.
- **Export** produces a report (CSV *(assumed)*), rate-limited per user.

## UI

- **Kind**: custom-page (dashboard) with apex charts + composed widgets.
- **Page**: "CS Dashboard" at `/crm/cs-dashboard` (Customer Success nav group).
- **Layout**: top row = headline KPIs (retention, churn, NRR, avg health, NPS); grid of charts below (health distribution, NPS trend, at-risk, CSM performance, playbook effectiveness); date-range filter in the header; Export button.
- **Key interactions**: change date range → all sections refresh; export report; drill from a widget into the owning module's resource.
- **States**: empty (no CS data yet → "metrics appear once health scores run") · loading (skeleton cards) · error (an individual section soft-fails without breaking the page) · selected (a KPI focused / date range applied). Inactive-module sections simply not rendered.
- **Gating**: `cs.analytics.view` (view + export).

## Data

- Owns / writes: nothing (no tables).
- Reads: `cs.health`, `cs.churn`, `cs.nps`, `cs.playbooks`, `finance.invoicing`, `crm.contacts` — all via read APIs, never their tables.
- Cross-domain writes: none — read-only ([[../../../../security/data-ownership]]).

## Relations

- Consumes: read APIs of every other CS module + finance + crm.
- Feeds: nothing (leaf consumer).
- Shared entity: none written; reads reference `crm_accounts` + each module's metrics.

## Unknowns

- Export format (CSV vs PDF) assumed CSV — [[../unknowns]].

## Related

- [[../_module|Success Analytics]] · [[./retention-nrr|Retention & NRR]]
- [[../../health-scores/_module|cs.health]] · [[../../../../architecture/caching]]
