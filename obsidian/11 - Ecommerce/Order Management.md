---
tags: [flowflex, domain/ecommerce, orders, fulfillment, phase/5]
domain: E-commerce & Sales Channels
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-06
---

# Order Management

Multi-channel order management from receipt to delivery.

**Who uses it:** Ecommerce team, warehouse, operations
**Filament Panel:** `ecommerce`
**Depends on:** [[Product Catalogue]], [[Inventory Management]]
**Phase:** 5
**Build complexity:** Very High — 3 resources, 2 pages, 8 tables

## Events Fired

- `OrderPlaced` → consumed by [[Inventory Management]] (deduct stock), [[Invoicing]] (record revenue), [[Contact & Company Management]] (update customer record)

## Database Tables (8)

1. `orders` — order headers with status workflow
2. `order_lines` — line items per order
3. `order_fulfillments` — fulfillment records (partial/split shipping)
4. `order_shipments` — shipment tracking records
5. `order_returns` — return/RMA records
6. `order_refunds` — refund records linked to returns
7. `shipping_labels` — generated label references
8. `order_channel_refs` — external marketplace order ID mapping

## Features

- **Multi-channel order import** — website, POS, marketplaces
- **Order status workflow** — custom stages
- **Fulfillment tracking**
- **Partial and split shipping**
- **Returns and refunds** — linked to [[Finance Overview]]
- **Packing slip generation**
- **Dropshipping supplier routing**
- **3PL integration** — ShipBob, ShipStation
- **Shipping label printing**
- **Customs documentation**

## Related

- [[Ecommerce Overview]]
- [[Product Catalogue]]
- [[Inventory Management]]
- [[Invoicing]]
- [[Contact & Company Management]]
