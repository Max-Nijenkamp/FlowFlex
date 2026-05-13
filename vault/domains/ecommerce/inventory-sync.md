---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.inventory
status: planned
color: "#4ADE80"
---

# Inventory Sync

> Real-time synchronisation of ecommerce stock levels with the operations inventory module, preventing overselling across all channels.

**Panel:** `ecommerce`
**Module key:** `ecommerce.inventory`

## What It Does

Inventory Sync is the bridge between the ecommerce domain and the operations inventory domain. Rather than maintaining a separate stock count in ecommerce and operations, this module publishes the single authoritative stock level from [[../operations/inventory]] to the storefront and all connected sales channels in real time. When a sale is made the stock is decremented; when a return is restocked the stock is incremented. This prevents overselling across own storefront, Amazon, eBay, and any other connected channel.

## Features

### Core
- Bidirectional sync: any stock change in operations inventory (receipt, adjustment, despatch, return) instantly propagates to ecommerce stock levels
- Per-variant mapping: each ecommerce product variant maps to its corresponding operations SKU by barcode or manual mapping
- Out-of-stock behaviour: configure per product whether to continue selling (backorder) or block checkout when stock reaches zero
- Low-stock threshold: configure a per-product alert threshold; when storefront stock falls below, notify the purchasing team
- Sync log: timestamped record of every sync event (what changed, which SKU, from what to what quantity) for audit and debugging

### Advanced
- Multi-warehouse allocation: for companies with multiple warehouses, configure which warehouse(s) fulfil ecommerce orders; only that warehouse's stock is shown as available on the storefront
- Safety stock buffer: configure a reserve quantity per SKU that is not shown as available on the storefront (e.g., keep 5 units back for wholesale orders)
- Channel-specific allocation: allocate a portion of total stock to own storefront vs Amazon vs eBay; prevent a single channel from consuming all stock
- Backorder management: when in backorder mode, display expected back-in-stock date to the customer; link to open purchase orders
- Sync error handling: alert when a sync fails (mapping not found, API timeout); show error log with retry option

### AI-Powered
- Demand-driven safety stock: adjust safety stock buffer automatically based on forecasted demand velocity
- Oversell risk alert: flag variants where real-time demand rate is approaching the available quantity, even if not yet at zero

## Data Model

```erDiagram
    ec_inventory_mappings {
        ulid id PK
        ulid company_id FK
        ulid product_variant_id FK
        ulid ops_product_id FK
        ulid warehouse_id FK
        integer safety_stock_buffer
        string out_of_stock_behaviour
        integer low_stock_threshold
        boolean is_active
        timestamps timestamps
    }

    ec_inventory_sync_events {
        ulid id PK
        ulid mapping_id FK
        integer qty_before
        integer qty_after
        string trigger_type
        string trigger_reference
        timestamp synced_at
    }

    ec_inventory_mappings ||--o{ ec_inventory_sync_events : "logs"
```

| Table | Purpose |
|---|---|
| `ec_inventory_mappings` | Links ecommerce variants to ops SKUs with configuration |
| `ec_inventory_sync_events` | Immutable log of every stock sync event |

## Permissions

```
ecommerce.inventory.view-any
ecommerce.inventory.manage-mappings
ecommerce.inventory.configure-buffers
ecommerce.inventory.view-sync-log
ecommerce.inventory.sync-now
```

## Filament

**Resource class:** `InventoryMappingResource`
**Pages:** List, Create, Edit
**Custom pages:** `SyncLogPage` (searchable event log with error filtering)
**Widgets:** `OutOfStockVariantsWidget`, `LowStockAlertsWidget`
**Nav group:** Catalog

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Linnworks | Multi-channel inventory synchronisation |
| Brightpearl | Inventory sync across ecommerce and operations |
| Skubana / Extensiv | Order and inventory management across channels |
| ChannelAdvisor | Multi-channel inventory feed management |

## Related

- [[products]] — product variants that require stock tracking
- [[orders]] — order fulfilment decrements synced stock
- [[returns]] — restocked returns increment synced stock
- [[multi-channel]] — all channels consume from the same synced stock pool
- [[../operations/inventory]] — the authoritative stock source
