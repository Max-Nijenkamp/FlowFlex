---
domain: analytics
module: kpi-tracking
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# KPI Tracking — Decisions

---

## ADR: KPIs are metric-driven; OKRs are goal-driven

`analytics.kpis` and [[../../projects/okrs/_module|projects.okrs]] are deliberately separate. A KPI tracks a **measurable number** against a target (revenue, headcount, churn) sourced from a metric or manual entry. An OKR frames a **qualitative objective** with key results. They share no tables; the link is conceptual (a KR may reference a KPI's number, read-only). Keeps each in its owning domain.

---

## ADR: Snapshots freeze history; the live number stays in the metric

`bi_kpi_snapshots` freezes `actual_value` + `target_value` per period for trend history. The *live* value is always re-resolved from `MetricRegistry` (or manual entry) — Analytics does not duplicate source data beyond the periodic snapshot it owns. This keeps the data-ownership boundary ([[../../../security/data-ownership]]) intact while giving fast historical reads.

---

## ADR: Threshold alert via notifications service, not a domain event

A below-threshold breach is delivered by calling `core.notifications`, not by firing an Analytics domain event. Analytics is a leaf: it reads metrics and requests notifications; it does not broadcast events for other domains to react to. The `alerted` once-guard on the snapshot prevents duplicates.

---

## Implementation Notes

- Status band ±5% *(assumed)* — configurable per KPI in a later pass.
- Capture is idempotent via upsert per `(kpi, period)`; safe to re-run.
- Metric-sourced KPIs skip capture gracefully if their module was deactivated.
