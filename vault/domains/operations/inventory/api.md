---
domain: operations
module: inventory
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Inventory — DTOs & API

## DTOs

### CreateItemData (input)

| Field | Type | Validation |
|---|---|---|
| sku | string | required, max:64, unique `(company_id, sku)` |
| name | string | required, max:255 |
| unit | string | required, max:16 |
| cost_price_cents | int | required, min:0 |
| reorder_point | decimal | required, min:0 |
| category | ?string | nullable, max:255 |

### MoveStockData (input)

| Field | Type | Validation |
|---|---|---|
| item_id | string | required, ulid, exists in company |
| warehouse_id | string | required, ulid, exists in company |
| type | string | required, in:in,out,transfer-in,transfer-out,adjust |
| quantity | decimal | required, gt:0 (signed internally by type) |
| unit_cost_cents | ?int | nullable, min:0 — required for `in` (receipt cost) *(assumed)* |
| reference_type | ?string | nullable |
| reference_id | ?string | nullable, ulid |

---

## Output

### ItemData (output)

`id`, `sku`, `name`, `category`, `unit`, `cost_price_cents`, `cost_price_formatted`, `reorder_point`, `total_on_hand` (Σ across warehouses), `total_available`, `is_low_stock`, `levels[]` (per-warehouse on-hand/reserved/available).

### StockMovementData (output)

`id`, `item_name`, `warehouse_name`, `type`, `quantity`, `unit_cost_cents`, `reference_type`, `occurred_at`.

---

## Public / Portal Endpoints

None planned for v1. `StockService` is an internal same-app contract consumed by other Operations modules (and e-commerce/sales when active). All UI is via the authenticated `operations` panel.
