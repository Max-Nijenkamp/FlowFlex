---
domain: operations
module: warehouses
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Warehouses — DTOs & API

## DTOs

### CreateWarehouseData (input)

| Field | Type | Validation |
|---|---|---|
| name | string | required, max:255 |
| code | string | required, max:32, unique per company |
| type | string | required, in:main,satellite,virtual |
| address | ?array | nullable |
| is_default | bool | default false |

### CreateTransferData (input)

| Field | Type | Validation |
|---|---|---|
| from_warehouse_id | string | required, ulid, exists in company, active |
| to_warehouse_id | string | required, ulid, exists in company, active, different from source |
| item_id | string | required, ulid, exists in company |
| quantity | decimal | required, gt:0 — availability at source checked in `TransferStockAction` |

---

## Output

### WarehouseData (output)

`id`, `name`, `code`, `type`, `is_default`, `address`, `stock_line_count` (computed from inventory read).

### WarehouseTransferData (output)

`id`, `from_warehouse_name`, `to_warehouse_name`, `item_name`, `quantity`, `status`, `transferred_at`, `transferred_by_name`.

---

## Public / Portal Endpoints

None. Access is via the authenticated Filament `operations` panel only.
