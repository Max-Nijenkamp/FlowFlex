---
domain: operations
module: warehouses
feature: stock-transfer
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Stock Transfer

Move quantity of an item from one warehouse to another as one atomic operation.

## Behaviour

1. User picks source warehouse, destination warehouse (≠ source), item, quantity.
2. `TransferStockAction::run(CreateTransferData)`:
   - checks `quantity ≤ available` at source (`StockService`);
   - opens one DB transaction: writes the `ops_warehouse_transfers` row, calls `StockService::move(transfer-out @ source)` then `StockService::move(transfer-in @ dest)`;
   - commits both movements together or rolls the whole thing back.
3. `status` = `completed` (instant; in-transit deferred — [[../decisions]]).
4. Same-warehouse transfer and over-available quantity are rejected before any write.

## UI

- **Kind**: simple-resource — create form + history table on `WarehouseTransferResource`.
- **Page**: `WarehouseTransferResource` at `/operations/warehouse-transfers`.
- **Layout**: create form (source select, destination select, item select with live available-qty hint, quantity); table lists past transfers with from/to/item/qty/date/user.
- **Key interactions**: select item → panel shows available at chosen source; submit → atomic transfer → row appears in history.
- **States**: empty (no transfers → "record your first transfer" CTA) · loading (submit spinner) · error (same-warehouse / over-available → inline validation, no write) · selected (n/a).
- **Gating**: view `operations.warehouses.view-any`; execute `operations.warehouses.transfer`.

## Data

- Owns / writes: `ops_warehouse_transfers`.
- Reads: available stock via `StockService` (operations.inventory).
- Cross-domain writes: none — stock levels/movements are mutated only through inventory's `StockService::move`, never a direct write ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: nothing (no domain event; movements are same-domain).
- Shared entity: `ops_items` / `ops_stock_levels` owned by operations.inventory.

## Test Checklist

### Unit
- [ ] Validation: destination != source; quantity <= available at source; both checked before any write

### Feature (Pest)
- [ ] Transfer writes transfer row + transfer-out + transfer-in in ONE transaction -- failure of either movement rolls back all three
- [ ] Concurrent transfers of the same stock cannot oversell the source (lockForUpdate in `StockService::move`)
- [ ] Tenant isolation + permission: own-company warehouses only, `operations.warehouses` verbs enforced

### Livewire
- [ ] Transfer create form validates source/dest/qty; history read-only; canAccess() hides without permission/module

## Related

- [[../_module|Warehouses]] · [[../../inventory/_module|Inventory]]
