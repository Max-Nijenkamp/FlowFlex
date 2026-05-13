---
type: module
domain: Field Service Management
panel: field
module-key: field.parts
status: planned
color: "#4ADE80"
---

# Part Inventory

> Field parts and stock management — warehouse and van stock levels, reorder alerts, and parts consumption tracking.

**Panel:** `field`
**Module key:** `field.parts`

---

## What It Does

Part Inventory manages the stock of spare parts and consumables required by field technicians. Stock is tracked at two levels: the central warehouse and each technician's van. When a technician uses a part on a job, they log it against the work order and their van stock is decremented. Reorder alerts fire when any stock location falls below its minimum level. Managers can transfer stock from warehouse to van, receive new stock from purchase orders, and view a full consumption history by part, technician, and job.

---

## Features

### Core
- Part catalogue: part number, description, category, unit, and cost price
- Stock locations: warehouse and per-technician van stock with separate quantity tracking
- Stock adjustment: receive new stock, write off damaged parts, manual adjustment with reason
- Minimum stock levels: set reorder point per part per location; alert when threshold breached
- Parts consumption: log parts used against a work order; van stock auto-decremented
- Stock transfer: transfer parts from warehouse to van or between vans

### Advanced
- Purchase order integration: receive parts against a purchase order from the procurement module
- Barcode scanning: scan part barcodes for fast stock receipt and consumption logging
- Van stocktake: periodic mobile stocktake flow for technicians to verify van inventory
- Part substitution: flag approved substitute parts when the primary part is out of stock
- Supplier lead times: track expected replenishment time per supplier for smarter reorder timing
- ABC analysis: classify parts by consumption frequency for stock prioritisation

### AI-Powered
- Demand forecasting: predict part consumption volumes by week based on scheduled job types
- Smart reorder quantities: calculate optimal reorder quantity based on lead time and demand variability
- Stockout risk alerts: flag parts at risk of stockout before the current job schedule exhausts stock

---

## Data Model

```erDiagram
    parts {
        ulid id PK
        ulid company_id FK
        string part_number
        string description
        string category
        string unit
        decimal cost_price
        integer reorder_point
        timestamps created_at_updated_at
    }

    stock_locations {
        ulid id PK
        ulid company_id FK
        string location_type
        string location_name
        ulid technician_id FK
        timestamps created_at_updated_at
    }

    stock_levels {
        ulid id PK
        ulid company_id FK
        ulid part_id FK
        ulid location_id FK
        integer quantity_on_hand
        timestamps created_at_updated_at
    }

    stock_transactions {
        ulid id PK
        ulid company_id FK
        ulid part_id FK
        ulid location_id FK
        ulid work_order_id FK
        string transaction_type
        integer quantity
        decimal unit_cost
        text reason
        timestamps created_at_updated_at
    }

    parts ||--o{ stock_levels : "tracked at"
    stock_locations ||--o{ stock_levels : "holds"
    parts ||--o{ stock_transactions : "consumed in"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `parts` | Part catalogue | `id`, `company_id`, `part_number`, `description`, `category`, `cost_price`, `reorder_point` |
| `stock_locations` | Warehouse and van locations | `id`, `company_id`, `location_type`, `location_name`, `technician_id` |
| `stock_levels` | Current quantity per part per location | `id`, `part_id`, `location_id`, `quantity_on_hand` |
| `stock_transactions` | Stock movements | `id`, `part_id`, `location_id`, `work_order_id`, `transaction_type`, `quantity` |

---

## Permissions

```
field.parts.view
field.parts.manage-catalogue
field.parts.adjust-stock
field.parts.consume
field.parts.transfer
```

---

## Filament

- **Resource:** `App\Filament\Field\Resources\PartResource`
- **Pages:** `ListParts`, `CreatePart`, `EditPart`, `ViewPart`
- **Custom pages:** `StockLevelsPage`, `StockTransactionHistoryPage`, `VanStocktakePage`
- **Widgets:** `LowStockPartsWidget`, `RecentConsumptionWidget`
- **Nav group:** Work Orders

---

## Displaces

| Feature | FlowFlex | ServiceTitan | FieldAware | Jobber |
|---|---|---|---|---|
| Warehouse and van stock | Yes | Yes | Yes | Partial |
| Parts consumption per job | Yes | Yes | Yes | Yes |
| Reorder alerts | Yes | Yes | Yes | No |
| AI demand forecasting | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[work-orders]] — parts consumed are logged against work orders
- [[technician-dispatch]] — van stock level visible on technician profile
- [[procurement/purchase-orders]] — new stock received against purchase orders
- [[job-invoicing]] — parts cost added to job invoice
