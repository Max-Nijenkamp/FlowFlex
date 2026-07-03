---
domain: procurement
module: spend-analytics
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `SpendAnalyticsDashboard` | #6 dashboard custom page + apex charts | date filter, soft-dep sections | Spend breakdown, committed-vs-actual, budget-vs-actual (soft) |
| `SpendBySupplierWidget` / `MaverickSpendWidget` / `SavingsWidget` | #6 widgets | conditional render | Hidden when their soft-dep source is inactive |

Hosted in **/operations** (Reporting nav group). Gates on `canAccess() = Auth::user()->can('procurement.spend.view') && BillingService::hasModule('procurement.spend')` per [[../../../architecture/filament-patterns]] #1 -- the dashboard states it explicitly; export action cites the `exports` limiter.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| All dashboard/widget/export paths | n-a | Read-only; owns no tables, writes nothing |
| Metrics cache writes | n-a | TTL-keyed `(company, from, to)`, idempotent recompute |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[data-model]] · [[api]] · [[../../../architecture/caching]] · [[../../../architecture/performance]]
