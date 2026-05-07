---
tags: [flowflex, domain/operations, purchasing, procurement, phase/4]
domain: Operations & Field Service
panel: operations
color: "#D97706"
status: planned
last_updated: 2026-05-07
---

# Purchasing & Procurement

Purchase orders, supplier management, and 3-way invoice matching. Connects operations purchasing to finance accounts payable automatically.

**Who uses it:** Operations, finance, procurement team
**Filament Panel:** `operations`
**Depends on:** Core, [[Inventory Management]]
**Phase:** 4
**Build complexity:** High — 4 resources, 8 tables

---

## Features

- **Purchase order builder** — line items, quantities, unit costs, delivery dates, reference numbers
- **Supplier management** — approved supplier register with contact details, payment terms, default currency
- **Approval thresholds** — POs above configurable amounts require senior approval; multi-level approval chain
- **Goods receipt notes (GRN)** — record partial or full receipt of goods against a PO; flags discrepancies
- **3-way matching** — PO → GRN → supplier invoice must match quantities and values before finance approves payment
- **Auto-PO from reorder** — when `StockBelowReorderPoint` fires, draft PO created for preferred supplier with reorder quantity
- **Supplier portal** — external login for suppliers to view, acknowledge, and update delivery status on POs
- **Budget checking** — optional integration with [[Budgeting & Forecasting]]; warns if PO exceeds department budget
- **Delivery tracking** — track expected vs actual delivery dates per PO line
- **PO PDF generation** — PDF export of PO for emailing to supplier
- **Spend analytics** — total spend per supplier per period, top suppliers dashboard widget

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `suppliers`
| Column | Type | Notes |
|---|---|---|
| `name` | string | |
| `email` | string nullable | |
| `phone` | string nullable | |
| `address` | text nullable | |
| `payment_terms_days` | integer | default 30 |
| `currency` | string(3) | ISO 4217 |
| `is_approved` | boolean | default false |
| `notes` | text nullable | |

### `purchase_orders`
| Column | Type | Notes |
|---|---|---|
| `supplier_id` | ulid FK | → suppliers |
| `reference` | string | auto-generated PO-YYYY-NNNN |
| `status` | enum | `draft`, `pending_approval`, `approved`, `sent`, `partially_received`, `received`, `cancelled` |
| `order_date` | date | |
| `expected_delivery_date` | date nullable | |
| `currency` | string(3) | |
| `subtotal` | decimal(12,2) | |
| `tax_amount` | decimal(12,2) | default 0 |
| `total` | decimal(12,2) | |
| `notes` | text nullable | |
| `approved_by_tenant_id` | ulid FK nullable | → tenants |
| `approved_at` | timestamp nullable | |

### `purchase_order_lines`
| Column | Type | Notes |
|---|---|---|
| `purchase_order_id` | ulid FK | → purchase_orders |
| `product_id` | ulid FK nullable | → products |
| `description` | string | |
| `quantity` | decimal(10,3) | |
| `unit_cost` | decimal(10,2) | |
| `tax_rate` | decimal(5,4) | default 0.00 |
| `total` | decimal(12,2) | |
| `quantity_received` | decimal(10,3) | default 0 |

### `goods_receipts`
| Column | Type | Notes |
|---|---|---|
| `purchase_order_id` | ulid FK | → purchase_orders |
| `received_by_tenant_id` | ulid FK | → tenants |
| `received_at` | timestamp | |
| `notes` | text nullable | |
| `status` | enum | `partial`, `complete` |

### `goods_receipt_lines`
| Column | Type | Notes |
|---|---|---|
| `goods_receipt_id` | ulid FK | → goods_receipts |
| `purchase_order_line_id` | ulid FK | → purchase_order_lines |
| `quantity_received` | decimal(10,3) | |
| `notes` | text nullable | |

### `po_approvals`
| Column | Type | Notes |
|---|---|---|
| `purchase_order_id` | ulid FK | → purchase_orders |
| `tenant_id` | ulid FK | approver |
| `decision` | enum | `approved`, `rejected` |
| `notes` | text nullable | |
| `decided_at` | timestamp | |

### `po_approval_thresholds`
| Column | Type | Notes |
|---|---|---|
| `amount` | decimal(12,2) | threshold above which approval required |
| `approver_role` | string | role name from RBAC |

### `supplier_portal_tokens`
| Column | Type | Notes |
|---|---|---|
| `supplier_id` | ulid FK | → suppliers |
| `purchase_order_id` | ulid FK | → purchase_orders |
| `token` | string | hashed, single-use per session |
| `expires_at` | timestamp | |
| `accessed_at` | timestamp nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `PurchaseOrderApproved` | `purchase_order_id` | [[Accounts Payable & Receivable]] (creates bill record, updates committed spend) |
| `PurchaseOrderSent` | `purchase_order_id`, `supplier_id` | Notification to supplier (email with PDF) |
| `GoodsReceived` | `purchase_order_id`, `goods_receipt_id` | [[Inventory Management]] (update stock levels) |

---

## Events Consumed

| Event | Source | Action |
|---|---|---|
| `StockBelowReorderPoint` | [[Inventory Management]] | Creates draft PO for preferred supplier with reorder quantity |

---

## Permissions

```
operations.suppliers.view
operations.suppliers.create
operations.suppliers.edit
operations.suppliers.delete
operations.purchase-orders.view
operations.purchase-orders.create
operations.purchase-orders.edit
operations.purchase-orders.delete
operations.purchase-orders.approve
operations.purchase-orders.send
operations.goods-receipts.view
operations.goods-receipts.create
operations.goods-receipts.edit
```

---

## Related

- [[Operations Overview]]
- [[Inventory Management]]
- [[Accounts Payable & Receivable]]
- [[Budgeting & Forecasting]]
- [[Finance Overview]]
