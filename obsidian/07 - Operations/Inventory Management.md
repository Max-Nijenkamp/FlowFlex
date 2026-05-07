---
tags: [flowflex, domain/operations, inventory, stock, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Inventory Management

Real-time stock control across multiple warehouses. Reorder automation, batch/serial traceability, and full movement audit trail.

**Who uses it:** Operations managers, warehouse staff, purchasing team
**Filament Panel:** `operations`
**Depends on:** Core
**Phase:** 4
**Build complexity:** Very High — 3 resources, 2 pages, 8 tables

---

## Features

- **Real-time stock levels** — per product per warehouse location; live quantity on-hand
- **Multi-location warehouse support** — track stock across multiple sites, warehouses, bins
- **Reorder point alerts** — auto-create draft PO in [[Purchasing & Procurement]] when stock falls below threshold
- **Barcode / QR scanning** — mobile app for receiving stock, picking, and cycle counts
- **Batch and serial number management** — full traceability from receipt to consumption
- **Stock adjustments** — write-off damage, shrinkage corrections, manual corrections with reason codes
- **Supplier tracking** — preferred supplier and lead time per product
- **Costing methods** — FIFO / LIFO / FEFO selectable per company
- **Returns and RMA handling** — customer returns processed back into stock or write-off
- **Stocktake / cycle count workflows** — guided counting interface; variance report on completion
- **Stock movement history** — every movement logged with source (PO receipt, sale, adjustment, field use)
- **Min/max inventory planning** — set minimum and maximum stock levels per product per location
- **Stock valuation report** — total inventory value using chosen costing method

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `products`
| Column | Type | Notes |
|---|---|---|
| `sku` | string unique (per company) | |
| `name` | string | |
| `description` | text nullable | |
| `unit` | string | e.g. "each", "kg", "litre" |
| `cost_price` | decimal(10,2) nullable | default purchase cost |
| `sale_price` | decimal(10,2) nullable | |
| `tax_rate` | decimal(5,4) | default 0.00 |
| `is_stockable` | boolean | default true |
| `is_active` | boolean | default true |
| `preferred_supplier_id` | ulid FK nullable | → suppliers |
| `costing_method` | enum | `fifo`, `lifo`, `fefo`; inherits company default |
| `image_file_id` | ulid FK nullable | → files |

### `stock_locations`
| Column | Type | Notes |
|---|---|---|
| `name` | string | e.g. "Warehouse A", "Shelf B3" |
| `address` | text nullable | |
| `is_active` | boolean | default true |
| `parent_id` | ulid FK nullable | self-referential for bin within warehouse |

### `stock_levels`
| Column | Type | Notes |
|---|---|---|
| `product_id` | ulid FK | → products |
| `stock_location_id` | ulid FK | → stock_locations |
| `quantity_on_hand` | decimal(10,3) | |
| `quantity_reserved` | decimal(10,3) | committed to orders not yet picked |
| `reorder_point` | decimal(10,3) nullable | trigger threshold |
| `reorder_quantity` | decimal(10,3) nullable | amount to order |
| `min_quantity` | decimal(10,3) nullable | |
| `max_quantity` | decimal(10,3) nullable | |

### `stock_movements`
| Column | Type | Notes |
|---|---|---|
| `product_id` | ulid FK | → products |
| `stock_location_id` | ulid FK | → stock_locations |
| `type` | enum | `receipt`, `dispatch`, `adjustment`, `transfer`, `return`, `write_off`, `field_use` |
| `quantity` | decimal(10,3) | positive = in, negative = out |
| `unit_cost` | decimal(10,2) nullable | at time of movement |
| `reference_type` | string nullable | morph type (PurchaseOrder, FieldJob, etc.) |
| `reference_id` | ulid nullable | morph id |
| `batch_serial_id` | ulid FK nullable | → batch_serials |
| `tenant_id` | ulid FK | who recorded |
| `notes` | text nullable | |
| `moved_at` | timestamp | |

### `stock_adjustments`
| Column | Type | Notes |
|---|---|---|
| `product_id` | ulid FK | → products |
| `stock_location_id` | ulid FK | → stock_locations |
| `quantity` | decimal(10,3) | signed |
| `reason` | enum | `damage`, `shrinkage`, `count_correction`, `expiry`, `other` |
| `notes` | text nullable | |
| `approved_by_tenant_id` | ulid FK nullable | |
| `stock_movement_id` | ulid FK | → stock_movements |

### `batch_serials`
| Column | Type | Notes |
|---|---|---|
| `product_id` | ulid FK | → products |
| `type` | enum | `batch`, `serial` |
| `number` | string | batch or serial number |
| `expiry_date` | date nullable | for FEFO costing |
| `quantity` | decimal(10,3) | remaining (for batch) |
| `stock_location_id` | ulid FK | → stock_locations |
| `received_at` | date | |

### `reorder_rules`
| Column | Type | Notes |
|---|---|---|
| `product_id` | ulid FK | → products |
| `stock_location_id` | ulid FK nullable | null = all locations |
| `reorder_point` | decimal(10,3) | |
| `reorder_quantity` | decimal(10,3) | |
| `supplier_id` | ulid FK nullable | → suppliers; overrides product preferred supplier |
| `is_active` | boolean | default true |

### `cycle_counts`
| Column | Type | Notes |
|---|---|---|
| `stock_location_id` | ulid FK | → stock_locations |
| `status` | enum | `in_progress`, `completed`, `cancelled` |
| `counted_by_tenant_id` | ulid FK | |
| `started_at` | timestamp | |
| `completed_at` | timestamp nullable | |
| `total_variance_value` | decimal(12,2) nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `StockBelowReorderPoint` | `product_id`, `stock_location_id`, `quantity_on_hand` | [[Purchasing & Procurement]] (create draft PO) |
| `StockAdjusted` | `product_id`, `adjustment_id` | Audit log |
| `CycleCountCompleted` | `cycle_count_id`, `variance_value` | Operations manager notification if variance > threshold |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `GoodsReceived` | [[Purchasing & Procurement]] | Updates stock levels for received lines |
| `FieldJobCompleted` | [[Field Service Management]] | Deducts parts used on-site from stock |
| `OrderPlaced` | E-commerce / [[Point of Sale]] | Reserves stock; deducts on dispatch |

---

## Permissions

```
operations.products.view
operations.products.create
operations.products.edit
operations.products.delete
operations.stock-locations.view
operations.stock-locations.create
operations.stock-locations.edit
operations.stock-locations.delete
operations.stock-levels.view
operations.stock-adjustments.view
operations.stock-adjustments.create
operations.stock-adjustments.approve
operations.cycle-counts.view
operations.cycle-counts.create
operations.cycle-counts.complete
```

---

## Related

- [[Operations Overview]]
- [[Purchasing & Procurement]]
- [[Asset Management]]
- [[Field Service Management]]
- [[Point of Sale]]
- [[Order Management]]
- [[Fixed Asset & Depreciation]]
