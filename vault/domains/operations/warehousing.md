---
type: module
domain: Operations
panel: operations
module-key: operations.warehouse
status: planned
color: "#4ADE80"
---

# Warehousing

> Manage warehouse zones and bin locations, direct putaway, generate pick lists, and track pack-and-ship workflows with barcode scanning support.

**Panel:** `operations`
**Module key:** `operations.warehouse`

## What It Does

Warehousing adds bin-level precision on top of the warehouse-level stock counts in Inventory. A warehouse is divided into zones, aisles, and individual bins — each with capacity rules and storage constraints. When goods arrive the system generates a directed putaway task to the optimal bin. When orders need fulfilment the system generates a pick list using the configured strategy (FIFO, FEFO, zone picking), and warehouse staff confirm picks via barcode scan on a tablet or handheld device.

## Features

### Core
- Multi-warehouse configuration: each warehouse has zones, aisles, and bins with a unique code
- Bin capacity rules: weight limit, volume limit, allowed product categories, temperature zone
- Putaway rules: directed putaway assigns incoming stock to the optimal bin based on product category, velocity (ABC), and available space
- Pick list generation: pick tasks created per outbound order with bin location and quantity per line
- Picking strategies: FIFO (first in first out), FEFO (first expiry first out), zone picking, batch picking
- Barcode scan confirmation: scan product barcode and bin barcode to confirm each pick action; mispicks flagged

### Advanced
- Wave picking: group multiple orders into a wave for efficient picking in one warehouse pass
- Pack station workflow: after picking, packer selects box size, records weight, prints shipping label
- Cross-docking: route incoming goods directly to an outbound order without putaway
- Cycle count tasks: assign partial stocktake counts to staff per zone; compare to system quantities
- Bin-level stock enquiry: view exactly which lots and serials are in each bin
- Labour tracking: picks per hour, error rate, and workload distribution per warehouse operative

### AI-Powered
- Slotting optimisation: recommend which SKUs to move to which bins to minimise travel time based on order co-pick history
- Demand-driven putaway: place fast-moving SKUs closest to pack stations

## Data Model

```erDiagram
    ops_warehouses {
        ulid id PK
        ulid company_id FK
        string name
        string address
        boolean is_default
        timestamps timestamps
    }

    ops_warehouse_bins {
        ulid id PK
        ulid warehouse_id FK
        string bin_code
        string zone
        string aisle
        string rack
        decimal capacity_kg
        decimal capacity_m3
        string allowed_category
        boolean is_active
        timestamps timestamps
    }

    ops_bin_stock {
        ulid id PK
        ulid bin_id FK
        ulid product_id FK
        integer quantity
        string lot_number
        date expiry_date
        timestamps timestamps
    }

    ops_pick_tasks {
        ulid id PK
        ulid company_id FK
        ulid order_id FK
        ulid wave_id FK
        ulid assigned_to FK
        string status
        string strategy
        timestamp started_at
        timestamp completed_at
        timestamps timestamps
    }

    ops_pick_task_lines {
        ulid id PK
        ulid task_id FK
        ulid bin_id FK
        ulid product_id FK
        integer qty_requested
        integer qty_picked
        boolean confirmed
    }

    ops_warehouses ||--o{ ops_warehouse_bins : "contains"
    ops_warehouse_bins ||--o{ ops_bin_stock : "holds"
    ops_pick_tasks ||--o{ ops_pick_task_lines : "has"
```

| Table | Purpose |
|---|---|
| `ops_warehouses` | Warehouse master records |
| `ops_warehouse_bins` | Individual bin locations with capacity rules |
| `ops_bin_stock` | Bin-level stock with lot and expiry |
| `ops_pick_tasks` | Outbound pick tasks per order or wave |
| `ops_pick_task_lines` | Line-level pick instructions and confirmations |

## Permissions

```
operations.warehouse.view-any
operations.warehouse.manage-bins
operations.warehouse.receive
operations.warehouse.pick
operations.warehouse.ship
```

## Filament

**Resource class:** `WarehouseResource`, `BinResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `WarehouseMapPage` (visual bin layout), `PickListPage` (operative pick interface with scan confirmation)
**Widgets:** `OpenPickTasksWidget` (tasks awaiting assignment or completion)
**Nav group:** Warehouse

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Deposco WMS | Full warehouse management including pick/pack/ship |
| Fishbowl Warehouse | Bin locations and pick workflows |
| Cin7 WMS | Zone and bin management |
| ShipBob WMS | Pick, pack, and ship workflow |

## Related

- [[inventory]] — bin stock feeds into warehouse-level stock totals
- [[purchase-orders]] — goods receipts trigger putaway tasks
- [[quality-control]] — inspection hold before stock enters bins
- [[logistics]] — ship confirmation triggers carrier label creation
- [[../ecommerce/orders]] — ecommerce orders generate pick tasks
