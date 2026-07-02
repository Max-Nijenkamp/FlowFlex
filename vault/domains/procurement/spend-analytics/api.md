---
domain: procurement
module: spend-analytics
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Spend Analytics — DTO & Service API

## DTO (output only)

### SpendMetricsData
- `by_supplier` / `by_category` / `by_department` — breakdown maps (Money).
- `trend` — time series.
- `maverick` — list of off-catalogue / non-approved-supplier lines.
- `savings` — table of (agreed − actual) × qty.
- `committed_vs_actual` — {committed, actual} Money.
- `budget_comparison?` — present only when finance.budgets active.

## Service API

| Method | Signature | Notes |
|---|---|---|
| `SpendAnalyticsService::metrics` | `metrics(CarbonImmutable $from, CarbonImmutable $to): SpendMetricsData` | brick/money aggregates, no N+1; soft-dep sections conditional; cached |

## Read-only

No mutating methods, no events. Everything is a query. Export is a throttled action rendering `SpendMetricsData` to xlsx/pdf.

## Related

- [[_module]] · [[data-model]] · [[architecture]] · [[../purchase-orders/api]]
