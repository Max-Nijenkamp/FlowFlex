---
domain: operations
module: stock-adjustments
feature: write-off-report
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Write-Off Report

Summarise adjustment value impact by reason/period for finance to journal manually (GL auto-posting deferred).

## Behaviour

- Aggregates `ops_stock_adjustments.value_impact_cents` grouped by `reason_code` and period.
- The finance-facing output: a write-off / shrinkage report a finance user reads to post a manual journal entry (no automated GL event in v1 — [[../decisions]]).
- Exportable to Excel.

## UI

- **Kind**: widget — a report view / filtered tab on `StockAdjustmentResource` (reason + period filters) with export. Cross-domain valuation reporting lives in [[../../operations-reporting/_module|operations.reporting]].
- **Page**: report filters on `StockAdjustmentResource` at `/operations/adjustments`.
- **Layout**: filters (reason code, date range, warehouse); grouped totals (value impact by reason); export button.
- **Key interactions**: filter by reason/period → totals recompute; export to Excel for finance.
- **States**: empty (no adjustments in range → €0.00) · loading (skeleton) · error (export throttled) · selected (n/a).
- **Gating**: `operations.adjustments.view-any`.

## Data

- Owns / writes: nothing (read/aggregate over `ops_stock_adjustments`).
- Reads: own module table only.
- Cross-domain writes: none — the report is **read** by finance; no GL posting from Operations ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: write-off totals consumed manually by finance.ledger (a human journals them); no event in v1.
- Shared entity: none.

## Related

- [[../_module|Stock Adjustments]] · [[../../../finance/general-ledger/_module|finance.ledger]] · [[../../operations-reporting/_module|operations.reporting]]
