---
type: module
domain: Operations
panel: operations
module-key: operations.inventory
status: planned
color: "#4ADE80"
---

# Inventory

> Manage SKUs, track real-time stock levels across multiple warehouses, enforce reorder points, and record every stock movement with a full audit trail.

**Panel:** `operations`
**Module key:** `operations.inventory`

## What It Does

Inventory is the authoritative stock ledger for the company. Every product SKU has a defined unit of measure, reorder point, and reorder quantity. Stock levels are updated in real time by goods receipts, sales order fulfilment, stock adjustments, and transfers between warehouses. The module raises reorder alerts when stock falls below the reorder point and provides a complete movement history for every SKU so discrepancies can be investigated.

## Features

### Core
- Product SKU register: name, SKU code, barcode (EAN/UPC), unit of measure, category, and supplier
- Multi-warehouse stock levels: separate on-hand quantity per warehouse location
- Stock movements: goods receipt, sales allocation, fulfilment, adjustment, transfer, and return — all recorded with reference, quantity, date, and user
- Reorder points: configurable minimum stock level per SKU; alerts when breached
- Reorder quantity: economic order quantity suggestion or manual setting
- Stock valuation: FIFO or weighted average cost method per company preference

### Advanced
- Lot and serial number tracking: trace individual units through goods-in to goods-out
- Expiry date tracking: FEFO picking support; alerts for stock approaching expiry
- ABC analysis: classify SKUs by movement velocity (A = fast, B = medium, C = slow) for cycle count prioritisation
- Cycle count scheduling: assign partial stocktake tasks to warehouse staff on a rolling basis
- Stock discrepancy workflow: counted quantity vs system quantity → approve adjustment or raise investigation
- Multi-location transfers: internal transfer order with in-transit status until receiving warehouse confirms receipt

### AI-Powered
- Demand forecasting: predict stock requirements for the next 30/60/90 days based on sales history and seasonality
- Reorder point optimisation: suggest adjusted reorder points based on lead time variability and stockout history

## Data Model

```erDiagram
    ops_products {
        ulid id PK
        ulid company_id FK
        string name
        string sku_code
        string barcode
        string uom
        string category
        decimal cost_price
        string valuation_method
        integer reorder_point
        integer reorder_qty
        boolean track_serial
        boolean track_lot
        softDeletes deleted_at
        timestamps timestamps
    }

    ops_stock_levels {
        ulid id PK
        ulid product_id FK
        ulid warehouse_id FK
        integer qty_on_hand
        integer qty_reserved
        integer qty_in_transit
        timestamps timestamps
    }

    ops_stock_movements {
        ulid id PK
        ulid company_id FK
        ulid product_id FK
        ulid warehouse_id FK
        string movement_type
        integer quantity
        decimal unit_cost
        string reference
        ulid created_by FK
        timestamp moved_at
    }

    ops_products ||--o{ ops_stock_levels : "stocked at"
    ops_products ||--o{ ops_stock_movements : "moved by"
```

| Table | Purpose |
|---|---|
| `ops_products` | SKU master data and valuation settings |
| `ops_stock_levels` | Current quantities per product per warehouse |
| `ops_stock_movements` | Immutable ledger of every stock change |

## Permissions

```
operations.inventory.view-any
operations.inventory.create
operations.inventory.adjust
operations.inventory.transfer
operations.inventory.export
```

## Filament

**Resource class:** `ProductResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `StockMovementsPage` (movement history per SKU), `CycleCountPage` (stocktake workflow)
**Widgets:** `LowStockWidget` (SKUs below reorder point), `StockValuationWidget` (total inventory value)
**Nav group:** Inventory

## Displaces

| Competitor | Feature Replaced |
|---|---|
| TradeGecko / Cin7 | Inventory management and stock levels |
| Fishbowl Inventory | Multi-warehouse stock tracking |
| inFlow Inventory | SKU management and reorder alerts |
| DEAR Inventory | Inventory with lot/serial tracking |

## Related

- [[purchase-orders]] — goods receipts update stock levels
- [[warehousing]] — bin-level detail within warehouse locations
- [[quality-control]] — inspection holds stock before receipt
- [[production-planning]] — BOM consumption reduces stock
- [[../ecommerce/inventory-sync]] — ecommerce stock syncs from here
