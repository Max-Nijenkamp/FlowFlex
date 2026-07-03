---
domain: operations
module: operations-reporting
feature: valuation-report
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Valuation Report

Total stock value by warehouse and category, with movement-trend context.

## Behaviour

- Aggregates `on_hand × cost_price_cents` (via `StockService::valuation`) by warehouse and category.
- Movement trends: in/out quantity + value over the selected period.
- Cached per company + date range; brick/money throughout.

## UI

- **Kind**: widget — `ValuationWidget` + `MovementTrendWidget` composed on `OperationsDashboardPage`.
- **Page**: widgets on `OperationsDashboardPage` at `/operations/dashboard`.
- **Layout**: total-value stat; by-warehouse + by-category tables; movement-trend apex chart (in vs out over time).
- **Key interactions**: date-range filter (recomputes/reads cache); Excel export; no writes.
- **States**: empty (no stock → €0.00, empty chart) · loading (skeleton stat + chart) · error (retry) · selected (n/a).
- **Gating**: `operations.reporting.view`.

## Data

- Owns / writes: nothing.
- Reads: `ops_items`, `ops_stock_levels`, `ops_stock_movements` (all operations.inventory).
- Cross-domain writes: none.

## Relations

- Consumes: nothing.
- Feeds: nothing (terminal reporting surface).
- Shared entity: inventory tables (read-only).

## Test Checklist

### Unit
- [ ] Valuation = on_hand x cost_price_cents via `StockService::valuation`, brick/money, grouped by warehouse + category

### Feature (Pest)
- [ ] Cache key embeds company + date range; historical vs current TTLs honoured
- [ ] Excel export rate-limited per user/company (`exports` limiter) and requires `operations.reporting.view`
- [ ] Tenant isolation: valuation never mixes companies

### Livewire
- [ ] `OperationsDashboardPage` canAccess() explicit; date filter re-scopes; `ValuationWidget`/`MovementTrendWidget` render

## Related

- [[../_module|Operations Reporting]] · [[../../inventory/features/valuation|Inventory Valuation]]
