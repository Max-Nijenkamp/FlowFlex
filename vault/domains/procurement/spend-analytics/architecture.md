---
domain: procurement
module: spend-analytics
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Analytics — Architecture

## Shape

A single read service (`SpendAnalyticsService`) producing one output DTO (`SpendMetricsData`), rendered by a dashboard page + widgets. No writes, no owned tables — the strictest form of a read-only bounded context.

```mermaid
flowchart LR
    REQ[(proc_requisitions)] --> S[SpendAnalyticsService.metrics]
    POL[(ops_po_lines)] --> S
    CAT[(proc_catalogue_items)] --> S
    BUD`finance.budgets read` -.soft.-> S
    S --> DTO[SpendMetricsData]
    DTO --> DASH[SpendAnalyticsDashboard + apex charts]
    DTO --> W[widgets]
    CACHE[(redis cache)] --- S
```

## Key decisions

- **Read-only aggregation** — reads other modules' tables/read APIs; writes nothing. Owns no tables at all.
- **Cached** per `(company, from, to)`: 1h historical / 15min current window.
- **Soft-dep sections conditional** — savings/maverick need catalogue; budget-vs-actual needs finance.budgets; hidden when inactive.
- **No N+1** — aggregate queries, brick/money for sums.
- **Export rate-limited** (throttle on the export action) — [[../../../architecture/security]].

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/caching]] · [[../../../architecture/performance]]
