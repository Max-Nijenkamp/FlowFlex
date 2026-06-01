---
type: module
domain: Operations
panel: operations
module-key: operations.warehouses
status: planned
color: "#4ADE80"
---

# Warehouses

Warehouse and location records. Stock is tracked per warehouse; supports transfers between locations.

## Core Features

- Warehouse record: name, code, address, type (main/satellite/virtual)
- Bin/location subdivisions within a warehouse (optional)
- Stock transfer between warehouses (creates out + in movements)
- Per-warehouse stock view
- Default warehouse setting
- Warehouse capacity tracking (optional)

## Data Model

| Table | Key Columns |
|---|---|
| `ops_warehouses` | company_id, name, code, address, type, is_default |
| `ops_warehouse_transfers` | company_id, from_warehouse_id, to_warehouse_id, item_id, quantity, status, transferred_at |

## Filament

**Nav group:** Inventory

- `WarehouseResource` — list, create, edit
- `WarehouseTransferResource` — create and track transfers

## Related

- [[domains/operations/inventory]]
