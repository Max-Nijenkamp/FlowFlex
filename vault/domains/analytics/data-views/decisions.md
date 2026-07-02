---
domain: analytics
module: data-views
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Cross-Domain Data Views — Decisions

---

## ADR: Views are shipped code, not user-built queries

v1 cross-domain views are FlowFlex-maintained `DataView` classes, not a user query builder. Rationale: cross-domain joins are the highest-risk surface for tenant leakage and expensive scans; hand-authored views let FlowFlex guarantee CompanyScope-safe reads and indexed access. The no-code *report* builder ([[../report-builder/_module|analytics.reports]]) covers single-source user queries; cross-domain stays curated.

---

## ADR: Reads via source-domain read paths, never their tables

Each view's `run()` calls the owning domain's query/read API under `CompanyContext`, then aggregates. Analytics owns no tables and writes nothing. This keeps the data-ownership boundary ([[../../../security/data-ownership]]) intact — identical stance to [[../dashboards/_module|dashboards]] and its `MetricRegistry`.

---

## Module gating per source

A view appears only when **all** `requiredModules()` are active. Deactivating any source module hides the view rather than erroring — mirrors the module-scoped rule from [[../../../decisions/decision-2026-06-20-full-mapping-conventions]].

---

## Implementation Notes

- Results cached per `(view, range)`, TTL 1 h *(assumed)* — cross-domain joins are the most expensive path in Analytics.
- Depends on `analytics.dashboards` for the shared read-consumer infrastructure and panel; not on the `bi_dashboards` tables.
