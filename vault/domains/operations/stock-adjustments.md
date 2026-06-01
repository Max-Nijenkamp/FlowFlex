---
type: module
domain: Operations
panel: operations
module-key: operations.adjustments
status: planned
color: "#4ADE80"
---

# Stock Adjustments

Manual stock corrections for damage, loss, stocktake reconciliation, and write-offs. Every adjustment is logged with a reason.

## Core Features

- Adjustment record: item, warehouse, quantity delta (+/-), reason, date
- Reason codes: damage, loss, theft, stocktake correction, write-off, found
- Stocktake mode: bulk count entry, system calculates adjustments vs recorded levels
- Approval required for adjustments above a threshold value
- Adjustment creates a stock movement record
- Audit trail: who adjusted, when, why, value impact
- Adjustment report by reason/period

## Data Model

| Table | Key Columns |
|---|---|
| `ops_stock_adjustments` | company_id, item_id, warehouse_id, quantity_delta, reason_code, notes, adjusted_by, approved_by, value_impact_cents |

## Filament

**Nav group:** Inventory

- `StockAdjustmentResource` — list, create, approve action
- `StocktakePage` (custom page) — bulk count entry, auto-calculate deltas

## Cross-Domain

- Adjustments may post to Finance GL (inventory write-off expense)

## Related

- [[domains/operations/inventory]]
- [[domains/finance/general-ledger]]
