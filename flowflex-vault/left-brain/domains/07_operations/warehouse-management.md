---
type: module
domain: Operations & Field Service
panel: operations
cssclasses: domain-operations
phase: 5
status: planned
migration_range: 300000–399999
last_updated: 2026-05-09
---

# Warehouse Management System (WMS)

Bin/location management, pick-pack-ship workflows, barcode scanning, and warehouse labour tracking. Replaces Deposco, ShipBob WMS, and Fishbowl.

---

## Features

### Warehouse Layout
- Multi-warehouse support (each with zones, aisles, bins)
- Visual warehouse map builder
- Storage rules per bin (temperature, hazard class, weight)
- Bin capacity tracking

### Receiving
- PO receipt with barcode/QR scanning
- License plate receiving (pallet-level tracking)
- Quality inspection gate on receipt
- Putaway task generation (directed putaway rules)
- Cross-docking support

### Pick, Pack & Ship
- Order picking strategies: FIFO, FEFO, wave picking, batch picking, zone picking
- Mobile scan-to-pick on tablet/handheld
- Pack stations: box selection, weight check, packing materials
- Multi-carrier shipping label print (PostNL, DHL, UPS, FedEx via EasyPost/Sendcloud)
- Pack slip and delivery note PDF

### Inventory Control
- Real-time bin-level stock
- Cycle count scheduling (ABC analysis)
- Serial number / lot tracking
- Expiry date tracking
- Stock discrepancy reporting

### Labour & Performance
- Task assignment to warehouse staff
- Picks-per-hour tracking
- Error rate per picker
- Workload heatmap by zone

---

## Data Model

```erDiagram
    warehouses {
        ulid id PK
        ulid company_id FK
        string name
        string address
        boolean is_default
    }

    warehouse_bins {
        ulid id PK
        ulid warehouse_id FK
        string bin_code
        string zone
        string aisle
        decimal capacity_kg
        boolean is_active
    }

    bin_stock {
        ulid id PK
        ulid bin_id FK
        ulid product_id FK
        integer quantity
        string lot_number
        date expiry_date
    }

    pick_tasks {
        ulid id PK
        ulid order_id FK
        ulid assigned_to FK
        string status
        timestamp started_at
        timestamp completed_at
        json scan_log
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `StockReceived` | Putaway complete | Inventory (update stock levels) |
| `OrderPicked` | All lines picked | Operations (advance to pack) |
| `OrderShipped` | Label printed + carrier scanned | E-commerce (update order tracking), Notifications (customer) |
| `BinStockDiscrepancy` | Cycle count finds mismatch | Notifications (warehouse manager), Inventory (adjust) |

---

## Permissions

```
operations.warehouse.view-any
operations.warehouse.receive
operations.warehouse.pick
operations.warehouse.ship
operations.warehouse.manage-bins
```

---

## Competitors Displaced

Deposco · ShipBob WMS · Fishbowl · Cin7 · inFlow · Linnworks WMS

---

## Related

- [[MOC_Operations]]
- [[entity-product]]
- [[MOC_Ecommerce]] — order fulfilment
