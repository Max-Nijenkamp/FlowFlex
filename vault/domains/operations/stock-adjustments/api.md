---
domain: operations
module: stock-adjustments
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Stock Adjustments — DTOs & API

## DTOs

### CreateAdjustmentData (input)

| Field | Type | Validation |
|---|---|---|
| item_id | string | required, ulid, exists in company |
| warehouse_id | string | required, ulid, exists in company |
| quantity_delta | decimal | required, not:0 — negative ≤ available |
| reason_code | string | required, in:damage,loss,theft,stocktake,write-off,found |
| notes | ?string | required_if reason_code in theft,write-off *(assumed)* |

### StocktakeData (input)

| Field | Type | Validation |
|---|---|---|
| warehouse_id | string | required, ulid, exists in company |
| counts | array | required, min:1 |
| counts[].item_id | string | required, ulid |
| counts[].counted_quantity | decimal | required, min:0 |

Deltas = `counted_quantity − current on-hand`; only non-zero deltas produce adjustments.

---

## Output

### StockAdjustmentData (output)

`id`, `item_name`, `warehouse_name`, `quantity_delta`, `reason_code`, `notes`, `value_impact_cents`, `value_impact_formatted`, `status`, `adjusted_by_name`, `approved_by_name`, `created_at`.

### StocktakeResult (output)

`warehouse_name`, `adjustments[]`, `total_value_impact_cents`, `pending_count`, `applied_count`.

---

## Public / Portal Endpoints

None. Authenticated `operations` panel only.
