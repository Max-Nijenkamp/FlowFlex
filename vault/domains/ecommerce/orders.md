---
type: module
domain: E-commerce
panel: ecommerce
module-key: ecommerce.orders
status: planned
color: "#4ADE80"
---

# Orders

> Receive, manage, fulfil, and track every customer order through a defined status workflow from placement to delivery confirmation.

**Panel:** `ecommerce`
**Module key:** `ecommerce.orders`

## What It Does

Orders is the operational centre of the ecommerce domain. Every purchase — from the storefront, multi-channel listings, or a manual order — creates an order record here. The order moves through a status workflow: pending payment → paid → in fulfilment → shipped → delivered → closed. Each stage triggers downstream actions: a paid order releases a pick task in [[../operations/warehousing]], a shipped order updates the tracking number and notifies the customer. Order editing, partial fulfilment, and cancellation are all handled here.

## Features

### Core
- Order list: filterable by status, customer, date, channel, and fulfilment state
- Order detail: line items, quantities, pricing, discounts applied, shipping address, billing address, payment method, and channel source
- Status workflow: pending → payment captured → processing → fulfilled → shipped → delivered → closed; cancelled at any stage
- Customer notification: automated emails at payment, dispatch, and delivery; customisable templates
- Order editing: add or remove line items before fulfilment; recalculate total and recharge/refund the difference
- Manual order: create an order on behalf of a customer (phone order, in-person sale)

### Advanced
- Partial fulfilment: fulfil available items now and hold back-ordered items; multiple shipments per order
- Split orders: split an order into separate fulfilment groups for different warehouse locations
- Order notes: internal notes visible only to staff; customer-facing order message from checkout
- Order tags: tag orders for filtering and routing (fragile, gift, VIP customer, requires signature)
- Bulk actions: bulk export to CSV; bulk mark as fulfilled; bulk print packing slips
- Returns initiation: create a return request from the order detail page; links to [[returns]] module

### AI-Powered
- Fraud scoring: score each incoming order for fraud risk based on address mismatch, velocity, and payment signals
- Next best action: surface suggested action (contact customer, check stock, escalate) for orders that have been stalled in a status for too long

## Data Model

```erDiagram
    ec_orders {
        ulid id PK
        ulid company_id FK
        string order_number
        string channel
        ulid customer_id FK
        string status
        decimal subtotal
        decimal discount_total
        decimal shipping_total
        decimal tax_total
        decimal grand_total
        string currency
        json shipping_address
        json billing_address
        string payment_status
        timestamp paid_at
        timestamp fulfilled_at
        timestamp shipped_at
        timestamp delivered_at
        timestamps timestamps
    }

    ec_order_lines {
        ulid id PK
        ulid order_id FK
        ulid product_variant_id FK
        string product_name
        string variant_label
        integer quantity
        decimal unit_price
        decimal line_total
        string fulfilment_status
    }

    ec_orders ||--o{ ec_order_lines : "contains"
```

| Table | Purpose |
|---|---|
| `ec_orders` | Order header with totals, status, and addresses |
| `ec_order_lines` | Line items with product snapshot and fulfilment status |

## Permissions

```
ecommerce.orders.view-any
ecommerce.orders.create
ecommerce.orders.update
ecommerce.orders.fulfil
ecommerce.orders.cancel
```

## Filament

**Resource class:** `OrderResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `OrderFulfilmentPage` (pick-and-pack interface linking to warehousing)
**Widgets:** `OrderStatusSummaryWidget`, `RecentOrdersWidget`
**Nav group:** Orders

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Shopify Orders | Order management and fulfilment workflow |
| WooCommerce Orders | Order processing and status tracking |
| BigCommerce Orders | Multi-channel order management |
| ShipStation Orders | Order management pre-shipment |

## Related

- [[payments]] — payment capture and refund linked to order
- [[returns]] — return requests created from order records
- [[abandoned-carts]] — checkout sessions that did not convert to orders
- [[../operations/warehousing]] — fulfilled orders generate pick tasks
- [[../operations/logistics]] — fulfilled orders trigger shipment creation
