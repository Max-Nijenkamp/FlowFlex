---
type: module
domain: Operations
domain-key: operations
panel: operations
module-key: operations.warehouses
status: planned
priority: p3
depends-on: [core.billing, core.rbac]
soft-depends: [operations.inventory]
fires-events: []
consumes-events: []
patterns: []
tables: [ops_warehouses, ops_warehouse_transfers]
permission-prefix: operations.warehouses
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Warehouses

Warehouse and location records. Stock is tracked per warehouse; supports transfers between locations. Builds first in Operations (inventory references it).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] | gating + permissions |
| Soft | [[domains/operations/inventory\|operations.inventory]] | transfers execute via `StockService::move` once inventory exists |

---

## Core Features

- Warehouse record: name, code, address, type (main/satellite/virtual)
- Bin/location subdivisions deferred *(assumed)*
- Stock transfer between warehouses (transfer-out + transfer-in movement pair, atomic)
- Per-warehouse stock view
- Default warehouse setting (exactly one)
- Capacity tracking deferred *(assumed)*

---

## Data Model

### ops_warehouses — id, company_id (indexed), name, code (unique per company), address jsonb nullable, type (main/satellite/virtual), is_default (one per company), deleted_at (blocked while stock > 0 *(assumed)*)
### ops_warehouse_transfers

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| from_warehouse_id / to_warehouse_id | ulid FK | ≠ |
| item_id | ulid FK | |
| quantity | decimal(12,2) | > 0, ≤ available at source |
| status | string default `completed` | completed (v1 instant; in-transit later *(assumed)*) |
| transferred_by / transferred_at | ulid / timestamp | |

---

## DTOs

### CreateTransferData — from/to warehouse (≠, both active), item_id, quantity (> 0; availability checked in service)

## Services & Actions

- `TransferStockAction::run(CreateTransferData $data): WarehouseTransfer` — one transaction: transfer row + two `StockService::move` calls (transfer-out, transfer-in)

---

## Filament

**Nav group:** Inventory

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `WarehouseResource` | #1 CRUD resource | default toggle |
| `WarehouseTransferResource` | #1 CRUD resource | create + history |

---

## Permissions

`operations.warehouses.manage` · `operations.warehouses.transfer`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Transfer atomic: both movements or none; source availability enforced
- [ ] Same-warehouse transfer rejected
- [ ] Exactly one default warehouse
- [ ] Duplicate code rejected

---

## Build Manifest

```
database/migrations/xxxx_create_ops_warehouses_table.php
database/migrations/xxxx_create_ops_warehouse_transfers_table.php
app/Models/Operations/{Warehouse,WarehouseTransfer}.php
app/Data/Operations/CreateTransferData.php
app/Actions/Operations/TransferStockAction.php
app/Filament/Operations/Resources/{WarehouseResource,WarehouseTransferResource}.php
database/factories/Operations/WarehouseFactory.php
tests/Feature/Operations/WarehouseTransferTest.php
```

---

## Related

- [[domains/operations/inventory]]
