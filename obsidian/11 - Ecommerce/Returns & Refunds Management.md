---
tags: [flowflex, domain/ecommerce, returns, refunds, phase/5]
domain: Ecommerce
panel: ecommerce
color: "#0891B2"
status: planned
last_updated: 2026-05-08
---

# Returns & Refunds Management

A self-service return portal for customers and a streamlined processing flow for your team. EU 14-day right of withdrawal baked in. Auto-generates return labels, notifies the warehouse, and issues refunds through Stripe. Replaces manual email returns and Returnly subscriptions.

**Who uses it:** Ecommerce managers, warehouse staff, customer support, customers
**Filament Panel:** `ecommerce` (staff); Vue + Inertia (customer portal)
**Depends on:** Core, [[Order Management]], [[Storefront & Checkout]], Finance
**Phase:** 5

---

## Features

### Customer Self-Service Portal

- Accessible from order confirmation email or "My Orders" in customer account
- Customer selects: order → items to return → quantity → reason
- Return reason options: wrong item, defective, changed mind, doesn't fit, other (free text)
- Photos upload: customer can attach images of defective/damaged items
- Policy check: auto-validates return window (e.g. 30 days from delivery)
- Return methods: ship back (label provided), drop-off point, in-store
- Confirmation: return ID, instructions, expected refund timeline shown

### Return Label Generation

- Auto-generate return shipping label via: PostNL, DHL, DPD, UPS, or bpost API
- PDF label emailed + downloadable from portal
- Tracking number registered → customer can track return shipment
- Prepaid label cost deducted from refund (configurable: free returns or deducted)

### Returns Processing (Staff)

- Return requests inbox: pending / received / inspected / approved / rejected
- Receive return: scan return tracking → marks as received, triggers inspection task
- Inspection result: accept in full, accept partial, reject (reason required)
- Condition grading: new/like-new/used/damaged → determines restock or write-off
- Notes: internal notes per return item
- Bulk process: approve + refund multiple returns at once

### Refund Handling

- Refund methods: original payment method (Stripe), store credit, gift card
- Partial refunds: if only some items accepted or restocking fee applied
- Automatic refund via Stripe API on approval (no manual processing)
- Finance integration: refund creates credit note in Finance module, reduces revenue
- Refund timeline communicated to customer automatically

### Return Policy Management

- Configure per product/category: return window (days), return eligible (yes/no)
- No-return items: digital downloads, perishables, customised products
- Restocking fee: optional % deducted from refund
- EU Right of Withdrawal: 14-day default, 30-day extended option
- Policy displayed on product page and in checkout flow

### Analytics

- Return rate by product, category, reason
- Refund amount total per period
- Most-returned products (merchandising signal: quality issue?)
- Average return processing time
- Customer return rate: flagging serial returners

---

## Database Tables (3)

### `ecommerce_return_requests`
| Column | Type | Notes |
|---|---|---|
| `order_id` | ulid FK | |
| `customer_id` | ulid FK | |
| `return_number` | string unique | RMA-XXXXXX |
| `status` | enum | `pending`, `label_sent`, `in_transit`, `received`, `inspecting`, `approved`, `rejected`, `refunded` |
| `return_method` | enum | `ship`, `drop_off`, `in_store` |
| `refund_method` | enum | `original_payment`, `store_credit`, `gift_card` |
| `refund_amount` | decimal nullable | |
| `tracking_number` | string nullable | |
| `label_file_id` | ulid FK nullable | |
| `approved_at` | timestamp nullable | |
| `refunded_at` | timestamp nullable | |

### `ecommerce_return_items`
| Column | Type | Notes |
|---|---|---|
| `return_id` | ulid FK | |
| `order_item_id` | ulid FK | |
| `quantity` | integer | |
| `reason` | string | |
| `reason_notes` | text nullable | |
| `photo_file_ids` | json nullable | ulid[] |
| `condition` | enum nullable | `new`, `like_new`, `used`, `damaged` |
| `approved_quantity` | integer nullable | after inspection |
| `restock` | boolean nullable | |

### `ecommerce_return_policies`
| Column | Type | Notes |
|---|---|---|
| `product_id` | ulid FK nullable | null = default policy |
| `category_id` | ulid FK nullable | |
| `return_window_days` | integer | |
| `eligible` | boolean | |
| `restocking_fee_pct` | decimal default 0 | |
| `free_return_label` | boolean default true | |

---

## Permissions

```
ecommerce.returns.view
ecommerce.returns.process
ecommerce.returns.approve-refunds
ecommerce.returns.manage-policies
ecommerce.returns.view-analytics
```

---

## Competitor Comparison

| Feature | FlowFlex | Returnly | Loop Returns | WooCommerce Refunds |
|---|---|---|---|---|
| No separate subscription | ✅ | ❌ (€€€) | ❌ (€€€) | ✅ (basic) |
| Self-service customer portal | ✅ | ✅ | ✅ | ❌ |
| Auto label generation | ✅ | ✅ | ✅ | ❌ |
| Stripe auto-refund | ✅ | ✅ | ✅ | ✅ |
| EU Right of Withdrawal built-in | ✅ | ❌ | ❌ | manual |
| Finance credit note auto-created | ✅ | ❌ | ❌ | ❌ |

---

## Related

- [[Ecommerce Overview]]
- [[Order Management]]
- [[Storefront & Checkout]]
- [[Customer Support & Helpdesk]]
- [[Finance Overview]]
