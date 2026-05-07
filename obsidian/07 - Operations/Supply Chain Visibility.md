---
tags: [flowflex, domain/operations, supply-chain, phase/4]
domain: Operations
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Supply Chain Visibility

Track shipments from supplier dispatch to warehouse arrival. Know where stock is before customers ask, and measure supplier performance over time.

**Who uses it:** Operations managers, procurement team, warehouse managers
**Filament Panel:** `operations`
**Depends on:** [[Purchasing & Procurement]], [[Inventory Management]]
**Phase:** 4
**Build complexity:** Medium — 3 resources, 1 page, 4 tables

---

## Features

- **Shipment tracking** — link shipments to purchase orders; record carrier, tracking number, and estimated arrival
- **Shipment event timeline** — log carrier events (dispatched, in-transit, customs, delivered) with location and timestamp
- **Real-time status dashboard** — overview of all in-flight shipments with colour-coded delay indicators
- **Delay alerting** — if `estimated_arrival` is passed with status not `delivered`, fire `ShipmentDelayed` event and notify procurement team
- **Landed cost tracking** — record freight, insurance, and duty costs per shipment for accurate cost-of-goods calculation
- **Supplier performance scorecards** — automated monthly scores per supplier: on-time rate %, quality score, average response time; trend charts per supplier
- **Lead time analysis** — compare promised vs actual lead times per supplier over rolling 12 months
- **Goods received confirmation** — mark a shipment as received and optionally trigger [[Quality Control & Inspections]] inspection
- **Carrier integration placeholders** — pre-built webhook receiver structure for common carrier APIs (DHL, FedEx, UPS)
- **Supplier performance alerts** — notify procurement when a supplier's on-time rate drops below threshold for the period
- **Export supplier performance** — CSV/PDF scorecard for supplier reviews or contract negotiations
- **Link to purchase orders** — drill-through from purchase order to all related shipments

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `shipments`
| Column | Type | Notes |
|---|---|---|
| `purchase_order_id` | ulid FK nullable | → purchase_orders |
| `supplier_id` | ulid FK | → suppliers |
| `reference` | string nullable | internal reference |
| `tracking_number` | string nullable | |
| `carrier` | string nullable | e.g. "DHL", "FedEx" |
| `status` | enum | `pending`, `dispatched`, `in_transit`, `customs`, `delivered`, `exception` |
| `shipped_at` | timestamp nullable | |
| `estimated_arrival` | date nullable | |
| `actual_arrival` | timestamp nullable | |
| `freight_cost` | decimal(10,2) nullable | |
| `insurance_cost` | decimal(10,2) nullable | |
| `duty_cost` | decimal(10,2) nullable | |
| `landed_cost_total` | decimal(10,2) nullable | |
| `notes` | text nullable | |

### `shipment_events`
| Column | Type | Notes |
|---|---|---|
| `shipment_id` | ulid FK | → shipments |
| `event_type` | string | e.g. "dispatched", "in_transit", "customs_held", "delivered" |
| `location` | string nullable | city or depot name |
| `occurred_at` | timestamp | |
| `notes` | string nullable | |
| `source` | enum | `manual`, `carrier_webhook`, `api` |

### `supplier_performance_scores`
| Column | Type | Notes |
|---|---|---|
| `supplier_id` | ulid FK | → suppliers |
| `period_month` | date | first day of the month |
| `on_time_rate` | decimal(5,2) | % |
| `quality_score` | decimal(5,2) | % (based on NCR rate) |
| `response_time_hours` | decimal(8,2) nullable | avg hours to acknowledge PO |
| `total_orders` | integer default 0 | |
| `total_shipments` | integer default 0 | |
| `on_time_count` | integer default 0 | |
| `late_count` | integer default 0 | |

### `landed_cost_records`
| Column | Type | Notes |
|---|---|---|
| `shipment_id` | ulid FK | → shipments |
| `cost_type` | enum | `freight`, `insurance`, `duty`, `handling`, `other` |
| `amount` | decimal(10,2) | |
| `currency` | string(3) default 'GBP' | |
| `notes` | string nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `ShipmentDelayed` | `shipment_id`, `supplier_id`, `days_late` | Notification to procurement team |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `PurchaseOrderSent` | [[Purchasing & Procurement]] | Creates a `shipment` record in `pending` status |
| `GoodsReceived` | [[Inventory Management]] | Updates shipment status to `delivered`, sets `actual_arrival` |

---

## Permissions

```
operations.shipments.view
operations.shipments.create
operations.shipments.edit
operations.shipments.delete
operations.shipment-events.view
operations.shipment-events.create
operations.supplier-performance.view
```

---

## Related

- [[Operations Overview]]
- [[Purchasing & Procurement]]
- [[Inventory Management]]
- [[Quality Control & Inspections]]
