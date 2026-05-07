---
tags: [flowflex, domain/ecommerce, orders, fulfillment, phase/4]
domain: E-commerce & Sales Channels
panel: ecommerce
color: "#0D9488"
status: planned
last_updated: 2026-05-07
---

# Order Management

Multi-channel order management from receipt to delivery. Handles order creation, status workflow, fulfillment, shipping, returns, and refunds across all sales channels.

**Who uses it:** Ecommerce team, warehouse staff, operations
**Filament Panel:** `ecommerce`
**Depends on:** [[Product Catalogue]], [[Inventory Management]]
**Phase:** 4
**Build complexity:** Very High — 3 resources, 2 pages, 8 tables

---

## Features

- **Multi-channel order import** — website checkout, POS, marketplace orders (Amazon/eBay/Etsy) all funnel to a single order queue
- **Order status workflow** — `pending` → `processing` → `picking` → `packed` → `shipped` → `delivered` → `completed`; custom statuses configurable
- **Partial and split fulfillment** — ship available items now, backorder remainder; tracks multiple fulfillments per order
- **Return / RMA workflow** — customer requests return, ops approves/denies, stock returned or written off
- **Refunds** — full or partial refund; linked to original payment; notifies customer
- **Packing slip generation** — PDF packing slip per fulfillment
- **Shipping label printing** — integrated with ShipStation, ShipBob; generate label, track number auto-populated
- **Dropshipping supplier routing** — route specific order lines to supplier for direct dispatch
- **3PL integration** — ShipBob, ShipStation webhook-based fulfillment handoff
- **Customs documentation** — CN22/CN23 for international orders
- **Customer notifications** — email on order confirmation, dispatch, delivery
- **Fraud risk flagging** — flag orders with mismatched billing/shipping address or high-value anomalies
- **Channel reference mapping** — store external marketplace order IDs alongside internal order ID
- **Bulk actions** — bulk print packing slips, bulk mark as shipped

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.
> All ecommerce product tables use the `ec_` prefix.

### `ec_orders`
| Column | Type | Notes |
|---|---|---|
| `order_number` | string unique | auto-generated EC-YYYY-NNNN |
| `crm_contact_id` | ulid FK nullable | → crm_contacts (customer) |
| `channel` | enum | `website`, `pos`, `amazon`, `ebay`, `etsy`, `manual` |
| `status` | enum | `pending`, `processing`, `picking`, `packed`, `shipped`, `delivered`, `completed`, `cancelled`, `refunded` |
| `currency` | string(3) | ISO 4217 |
| `subtotal` | decimal(12,2) | |
| `discount_amount` | decimal(12,2) | default 0 |
| `shipping_amount` | decimal(12,2) | default 0 |
| `tax_amount` | decimal(12,2) | default 0 |
| `total` | decimal(12,2) | |
| `shipping_name` | string | |
| `shipping_address_line1` | string | |
| `shipping_address_line2` | string nullable | |
| `shipping_city` | string | |
| `shipping_postcode` | string | |
| `shipping_country` | string(2) | ISO 3166-1 alpha-2 |
| `billing_name` | string | |
| `billing_address_line1` | string | |
| `billing_address_line2` | string nullable | |
| `billing_city` | string | |
| `billing_postcode` | string | |
| `billing_country` | string(2) | |
| `notes` | text nullable | |
| `fraud_risk` | enum | `low`, `medium`, `high`; auto-scored |
| `payment_status` | enum | `unpaid`, `paid`, `partially_refunded`, `refunded` |

### `ec_order_lines`
| Column | Type | Notes |
|---|---|---|
| `ec_order_id` | ulid FK | → ec_orders |
| `ec_product_id` | ulid FK | → ec_products |
| `ec_product_variant_id` | ulid FK nullable | → ec_product_variants |
| `name` | string | product name at time of order (snapshot) |
| `sku` | string | |
| `quantity` | integer | |
| `unit_price` | decimal(10,2) | |
| `discount_amount` | decimal(10,2) | default 0 |
| `tax_amount` | decimal(10,2) | default 0 |
| `total` | decimal(12,2) | |
| `fulfillment_status` | enum | `unfulfilled`, `fulfilled`, `returned` |
| `supplier_id` | ulid FK nullable | → suppliers (if dropship) |

### `ec_order_fulfillments`
| Column | Type | Notes |
|---|---|---|
| `ec_order_id` | ulid FK | → ec_orders |
| `status` | enum | `pending`, `processing`, `shipped`, `delivered` |
| `fulfilled_by_tenant_id` | ulid FK nullable | → tenants |
| `fulfilled_at` | timestamp nullable | |
| `notes` | text nullable | |

### `ec_order_shipments`
| Column | Type | Notes |
|---|---|---|
| `ec_order_fulfillment_id` | ulid FK | → ec_order_fulfillments |
| `carrier` | string nullable | e.g. "Royal Mail", "UPS" |
| `tracking_number` | string nullable | |
| `tracking_url` | string nullable | |
| `shipped_at` | timestamp nullable | |
| `estimated_delivery_at` | timestamp nullable | |
| `delivered_at` | timestamp nullable | |

### `ec_order_returns`
| Column | Type | Notes |
|---|---|---|
| `ec_order_id` | ulid FK | → ec_orders |
| `reason` | enum | `wrong_item`, `damaged`, `not_as_described`, `changed_mind`, `other` |
| `status` | enum | `requested`, `approved`, `received`, `rejected` |
| `notes` | text nullable | |
| `approved_by_tenant_id` | ulid FK nullable | |
| `approved_at` | timestamp nullable | |

### `ec_order_refunds`
| Column | Type | Notes |
|---|---|---|
| `ec_order_id` | ulid FK | → ec_orders |
| `ec_order_return_id` | ulid FK nullable | → ec_order_returns |
| `amount` | decimal(12,2) | |
| `reason` | text nullable | |
| `processed_by_tenant_id` | ulid FK | |
| `processed_at` | timestamp | |
| `stripe_refund_id` | string nullable encrypted | |

### `ec_shipping_labels`
| Column | Type | Notes |
|---|---|---|
| `ec_order_shipment_id` | ulid FK | → ec_order_shipments |
| `carrier` | string | |
| `label_url` | string encrypted | external URL or S3 path |
| `file_id` | ulid FK nullable | → files |
| `generated_at` | timestamp | |

### `ec_order_channel_refs`
| Column | Type | Notes |
|---|---|---|
| `ec_order_id` | ulid FK | → ec_orders |
| `channel` | enum | `amazon`, `ebay`, `etsy`, `shopify`, `other` |
| `external_order_id` | string | marketplace order ID |
| `external_status` | string nullable | raw status from marketplace |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `OrderPlaced` | `ec_order_id`, `crm_contact_id` | [[Inventory Management]] (deduct stock), [[Invoicing]] (record revenue), [[Contact & Company Management]] (update customer) |
| `OrderShipped` | `ec_order_id`, `tracking_number` | Notifications (email customer with tracking) |
| `ReturnRequested` | `ec_order_id`, `ec_order_return_id` | Notifications (notify ops team) |
| `RefundProcessed` | `ec_order_id`, `amount` | Finance (record refund) |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `CheckoutCompleted` | [[Storefront & Checkout]] | Creates order record |
| `ChannelSyncCompleted` | [[Marketplace Channel Sync]] | Imports new channel orders |
| `POSTransactionCompleted` | [[Point of Sale]] | Creates order record for POS sales |

---

## Permissions

```
ecommerce.orders.view
ecommerce.orders.create
ecommerce.orders.edit
ecommerce.orders.delete
ecommerce.orders.fulfil
ecommerce.orders.refund
ecommerce.returns.view
ecommerce.returns.approve
ecommerce.returns.reject
```

---

## Related

- [[Ecommerce Overview]]
- [[Product Catalogue]]
- [[Inventory Management]]
- [[Storefront & Checkout]]
- [[Marketplace Channel Sync]]
- [[Invoicing]]
- [[Contact & Company Management]]
