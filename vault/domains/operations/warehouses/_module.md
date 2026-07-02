---
domain: operations
module: warehouses
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Warehouses

Warehouse and location records. Stock is tracked per warehouse; supports atomic transfers between locations. Builds first in Operations — [[../inventory/_module|inventory]] references it.

> Operations hosts the [[../../procurement/_index|Procurement]] panel (shared PO/GRN/supplier entities). See [[../../../decisions/decision-2026-06-01-panel-consolidation]].

---

## Module-key

`operations.warehouses`

**Priority:** p3
**Panel:** operations (Orange)
**Permission prefix:** `operations.warehouses`
**Tables:** `ops_warehouses`, `ops_warehouse_transfers`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions |
| Soft | [[../inventory/_module\|operations.inventory]] | transfers execute via `StockService::move` once inventory exists; standalone warehouse CRUD without it |

---

## Core Features

- Warehouse record: name, code, address, type (main / satellite / virtual)
- Atomic stock transfer between warehouses (transfer-out + transfer-in movement pair)
- Per-warehouse stock view
- Default warehouse (exactly one per company)
- Bin/location subdivisions + capacity tracking deferred *(assumed)*

See [[./features/stock-transfer|Stock Transfer feature]].

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

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Transfer atomic: both movements or none; source availability enforced
- [ ] Same-warehouse transfer rejected
- [ ] Exactly one default warehouse per company
- [ ] Duplicate code rejected

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `StockService` | operations.inventory (same domain) | transfer executes two `move()` calls |

**Data ownership:** `operations.warehouses` writes only `ops_warehouses`, `ops_warehouse_transfers`. Stock levels/movements are owned + written by [[../inventory/_module|operations.inventory]] via `StockService` — never a direct write ([[../../../security/data-ownership]]).

---

## Related

- [[../inventory/_module|operations.inventory]]
- [[../_index|Operations MOC]]
- [[../../../architecture/ui-strategy]]
