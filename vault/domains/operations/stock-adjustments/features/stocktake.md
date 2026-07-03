---
domain: operations
module: stock-adjustments
feature: stocktake
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Stocktake

Bulk count a warehouse; the system computes and applies the deltas against recorded levels.

## Behaviour

- `AdjustmentService::stocktake(StocktakeData)`: for each counted item, delta = `counted − current on-hand`; non-zero deltas become adjustments with reason `stocktake correction`.
- Each delta applies (or queues for approval if over threshold) via `StockService::move(adjust)`.
- Deltas computed at confirm time against then-current levels (no freeze in v1 *(assumed)*).
- Bulk submission is rate-limited per company.

## UI

- **Kind**: custom-page — a count grid with a delta preview step, beyond table+form ([[../../../../architecture/patterns/custom-pages]]).
- **Page**: `StocktakePage` at `/operations/stocktake`.
- **Layout**: step 1 pick warehouse; step 2 count grid (item, system qty, counted qty input); step 3 preview deltas (highlight discrepancies + total value impact); confirm.
- **Key interactions**: enter counts → preview computed deltas → confirm → adjustments created/applied; large runs throttled.
- **States**: empty (warehouse has no items → prompt) · loading (grid load + submit spinner) · error (throttled; negative-beyond-available on a delta) · selected (discrepant rows highlighted in preview).
- **Gating**: `operations.adjustments.create` (over-threshold deltas still need `.approve`).

## Data

- Owns / writes: `ops_stock_adjustments` (one per non-zero delta).
- Reads: `ops_stock_levels` (current on-hand), `ops_items` (cost).
- Cross-domain writes: none — deltas applied via `StockService::move` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: adjustments into inventory's ledger + the write-off report.
- Shared entity: `ops_items`, `ops_stock_levels`, `ops_warehouses`.

## Test Checklist

### Unit
- [ ] Delta = counted - current on-hand per item; zero deltas produce no adjustment

### Feature (Pest)
- [ ] Non-zero deltas become adjustments with reason `stocktake correction`; over-threshold deltas queue for approval
- [ ] Bulk submission rate-limited per company; deltas computed at confirm time against then-current levels *(assumed no freeze)*
- [ ] Tenant isolation: stocktake writes own-company stock only

### Livewire
- [ ] Stocktake form validates counted quantities; confirm summarises deltas before apply

## Related

- [[../_module|Stock Adjustments]] · [[./adjustment-approval|Adjustment & Approval]]
